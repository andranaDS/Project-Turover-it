<?php

namespace App\JobPosting\DoctrineEventSubscriber;

use App\Core\Mailer\Mailer;
use App\JobPosting\Entity\Application;
use App\User\Email\UserApplicationAcknowledgedEmail;
use App\User\Email\UserApplicationAcknowledgedUnsolicitedEmail;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class UserApplicationSubscriber implements EventSubscriber
{
    private Mailer $mailer;
    private LoggerInterface $logger;

    public function __construct(Mailer $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $application = $args->getObject();

        if (!$application instanceof Application || null === $application->getUser() || null === $application->getUser()->getEmail()) {
            return;
        }

        if (null !== $application->getJobPosting()) {
            try {
                $email = (new UserApplicationAcknowledgedEmail())
                    ->to((string) $application->getUser()->getEmail())
                    ->context([
                        'user' => $application->getUser(),
                        'jobPosting' => [$application->getJobPosting()],
                        'application' => $application,
                    ])
                ;

                $this->mailer->send($email);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        } elseif (null !== $application->getCompany()) {
            try {
                $email = (new UserApplicationAcknowledgedUnsolicitedEmail())
                    ->to((string) $application->getUser()->getEmail())
                    ->context([
                        'user' => $application->getUser(),
                        'application' => $application,
                    ])
                ;

                $this->mailer->send($email);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
