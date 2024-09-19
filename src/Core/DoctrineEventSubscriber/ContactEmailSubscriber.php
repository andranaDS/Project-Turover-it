<?php

namespace App\Core\DoctrineEventSubscriber;

use App\Core\Email\ContactEmail;
use App\Core\Entity\Contact;
use App\Core\Mailer\Mailer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ContactEmailSubscriber implements EventSubscriber
{
    private ?Request $request;
    private Mailer $mailer;
    private string $contactRecipientAdmin;
    private LoggerInterface $logger;

    public function __construct(RequestStack $requestStack, Mailer $mailer, string $contactRecipientAdmin, LoggerInterface $logger)
    {
        $this->request = $requestStack->getMainRequest();
        $this->mailer = $mailer;
        $this->contactRecipientAdmin = $contactRecipientAdmin;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return null !== $this->request ? [Events::postPersist] : [];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    private function process(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Contact) {
            return;
        }

        $email = (new ContactEmail())
            ->to($this->contactRecipientAdmin)
            ->context([
                'object' => $object,
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
