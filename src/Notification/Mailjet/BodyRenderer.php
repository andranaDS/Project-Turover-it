<?php

namespace App\Notification\Mailjet;

use Predis\Client;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Mime\Message;
use Symfony\Contracts\Cache\ItemInterface;

class BodyRenderer implements BodyRendererInterface
{
    private Mailjet $mailjet;
    private Client $redis;

    public function __construct(Mailjet $mailjet, Client $redis)
    {
        $this->mailjet = $mailjet;
        $this->redis = $redis;
    }

    public function render(Message $message): void
    {
        if (!$message instanceof Email) {
            return;
        }

        $messageTemplateId = $message->getTemplateId();
        $messageVariables = array_filter($message->getVariables());

        if (null === $messageTemplateId) {
            throw new \InvalidArgumentException('TemplateId is missing');
        }

        // fetch template data from cache if exists
        $cache = new RedisTagAwareAdapter($this->redis);
        $templateData = $cache->get("mailjet_template_$messageTemplateId", function (ItemInterface $item) use ($messageTemplateId) {
            $item->expiresAfter(3600);
            $item->tag(['mailjet']);

            return $this->mailjet->getTemplate($messageTemplateId);
        });

        $templateDataHeaders = $templateData['Headers'] ?? [];

        // subject, text, html
        $subject = $this->renderVariables($templateDataHeaders['Subject'] ?? '', $messageVariables);
        $text = $this->renderVariables($templateData['Text-part'] ?? '', $messageVariables);
        $html = $this->renderVariables($templateData['Html-part'] ?? '', $messageVariables);

        $message
            ->subject($subject)
            ->text($text)
            ->html($html)
        ;

        // from
        $senderName = $templateDataHeaders['SenderName'] ?? null;
        $senderEmail = $templateDataHeaders['SenderEmail'] ?? null;

        if (empty($senderEmail)) {
            throw new \InvalidArgumentException('The template variable "%s" is missing.');
        }

        $from = empty($senderName) ? $senderEmail : sprintf('%s <%s>', $senderName, $senderEmail);
        if (!empty($from) && empty($message->getFrom())) {
            $message->from($from);
        }

        // reply to
        $replyTo = $templateData['Headers']['Reply-To'] ?? null;
        if (!empty($replyTo) && empty($message->getReplyTo())) {
            $message->replyTo($replyTo);
        }
    }

    public function renderVariables(string $content, array $variables): string
    {
        return preg_replace_callback(
            '/{{var:(\w+)(?::"(.*?)")?}}/m',
            static function (array $matches) use ($variables) {
                return $variables[$matches[1]] ?? $matches[2] ?? '';
            },
            $content
        ) ?? $content;
    }
}
