<?php

namespace App\Notification\EventListener;

use App\Core\Util\Strings;
use App\Notification\Mailjet\BodyRenderer as MailjetBodyRenderer;
use App\Notification\Mailjet\Email as MailjetEmail;
use App\Notification\Twig\BodyRenderer as TwigBodyRenderer;
use App\Notification\Twig\Email as TwigEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;

class MessageListener implements EventSubscriberInterface
{
    private TwigBodyRenderer $twigRenderer;
    private MailjetBodyRenderer $mailjetRenderer;
    private string $cluster;

    public function __construct(TwigBodyRenderer $twigRenderer, MailjetBodyRenderer $mailjetRenderer, ParameterBagInterface $params)
    {
        $this->twigRenderer = $twigRenderer;
        $this->mailjetRenderer = $mailjetRenderer;
        $this->cluster = $params->get('cluster');
    }

    public function onMessage(MessageEvent $event): void
    {
        $email = $event->getMessage();

        // render
        if ($email instanceof TwigEmail) {
            $this->twigRenderer->render($email);
        } elseif ($email instanceof MailjetEmail) {
            $this->mailjetRenderer->render($email);
        }

        // update title in local/dev/preprod cluster
        if ('prod' !== $this->cluster && $email instanceof Email) {
            $email->subject(sprintf('%s: %s', Strings::upper($this->cluster), $email->getSubject()));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => ['onMessage', 1],
        ];
    }
}
