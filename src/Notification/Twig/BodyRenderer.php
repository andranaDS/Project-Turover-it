<?php

namespace App\Notification\Twig;

use App\Core\Enum\EmailSenderType;
use League\HTMLToMarkdown\HtmlConverter;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Message;
use Twig\Environment;

class BodyRenderer implements BodyRendererInterface
{
    private Environment $twig;
    private array $context;
    private LoggerInterface $logger;
    private HtmlConverter $converter;
    private array $senderTypes;

    public function __construct(Environment $twig, LoggerInterface $logger, ParameterBagInterface $params, array $context = [])
    {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->context = $context;
        $this->converter = new HtmlConverter([
            'hard_break' => true,
            'strip_tags' => true,
            'remove_nodes' => 'head style',
        ]);
        $this->senderTypes = [
            EmailSenderType::FREE_WORK_CONTACT => $params->get('mailer_headers_from_free_work_contact'),
            EmailSenderType::FREE_WORK_PROFILE => $params->get('mailer_headers_from_free_work_profile'),
            EmailSenderType::FREE_WORK_JOB => $params->get('mailer_headers_from_free_work_job'),
            EmailSenderType::FREE_WORK_ACCOUNT => $params->get('mailer_headers_from_free_work_account'),
            EmailSenderType::FREE_WORK_FORUM => $params->get('mailer_headers_from_free_work_forum'),
            EmailSenderType::TURNOVER_CONTACT => $params->get('mailer_headers_from_turnover_contact'),
        ];
    }

    public function render(Message $message): void
    {
        if (!$message instanceof Email) {
            return;
        }

        if (null === $messageTemplate = $message->getTemplate()) {
            throw new \InvalidArgumentException('Template is missing');
        }
        $messageContext = $message->getContext();

        $previousRenderingKey = $messageContext[__CLASS__] ?? null;
        unset($messageContext[__CLASS__]);
        $currentRenderingKey = $this->getFingerPrint($message);
        if ($previousRenderingKey === $currentRenderingKey) {
            return;
        }

        if (isset($messageContext['email'])) {
            throw new InvalidArgumentException(sprintf('A "%s" context cannot have an "email" entry as this is a reserved variable.', get_debug_type($message)));
        }

        $vars = array_merge($this->context, $messageContext, [
            'email' => new WrappedTemplatedEmail($this->twig, $message),
        ]);

        try {
            $template = $this->twig->load($messageTemplate);
            $subject = trim($template->renderBlock('subject', $vars));
            $html = trim($template->renderBlock('html', $vars));
            $text = $template->hasBlock('text') ? trim($template->renderBlock('text', $vars)) : $this->convertHtmlToText($html);
            if ($template->hasBlock('config')) {
                $template->renderBlock('config', $vars);
            }
        } catch (\Throwable $e) {
            $this->logger->error('[MAILER] Twig load failed', [
                'template' => $messageTemplate,
                'context' => $messageContext,
            ]);
            throw $e;
        }

        $message
            ->subject($subject)
            ->text($text)
            ->html($html)
            ->context($message->getContext() + [__CLASS__ => $currentRenderingKey])
        ;

        $this->setSender($message);
    }

    private function getFingerPrint(Email $message): string
    {
        $messageContext = $message->getContext();
        unset($messageContext[__CLASS__]);

        $payload = [$messageContext, $message->getTemplate()];
        try {
            $serialized = serialize($payload);
        } catch (\Exception $e) {
            $serialized = random_bytes(8);
        }

        return md5($serialized);
    }

    private function convertHtmlToText(string $html): string
    {
        return $this->converter->convert($html);
    }

    private function setSender(Email $message): void
    {
        if (null !== $message->getSenderType() && isset($this->senderTypes[$message->getSenderType()])) {
            $message->from($this->senderTypes[$message->getSenderType()]);
        }
    }
}
