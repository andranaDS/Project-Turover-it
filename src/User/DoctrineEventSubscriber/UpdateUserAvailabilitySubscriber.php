<?php

namespace App\User\DoctrineEventSubscriber;

use App\Core\Mailer\Mailer;
use App\JobPosting\Entity\JobPosting;
use App\User\Email\UpdateAvailabilityImmediateEmail;
use App\User\Email\UpdateConfirmationAvailabilityNonImmediateEmail;
use App\User\Entity\User;
use App\User\Enum\Availability;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class UpdateUserAvailabilitySubscriber implements EventSubscriber
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
            Events::postUpdate,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        if (null === $user->getEmail()) {
            return;
        }

        /** @var EntityManagerInterface $em */
        $em = $args->getObjectManager();
        $unitOfWork = $em->getUnitOfWork();
        $changeSet = $unitOfWork->getEntityChangeSet($user);

        // if user change availability to immediate
        if (
            isset($changeSet['availability']) &&
            Availability::IMMEDIATE !== $changeSet['availability'][0] &&
            Availability::IMMEDIATE === $changeSet['availability'][1] &&
            true === $user->getVisible()
        ) {
            try {
                $email = (new UpdateAvailabilityImmediateEmail())
                    ->to($user->getEmail())
                    ->context([
                        'user' => $user,
                        'jobPostings' => $em->getRepository(JobPosting::class)->findUserSuggested($user, 1, 10),
                    ])
                ;

                $this->mailer->send($email);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }

            // if user change availability for other than immediate or none
        } elseif (
            isset($changeSet['availability']) &&
            true === $user->getVisible() &&
            \in_array($changeSet['availability'][1], [Availability::WITHIN_1_MONTH, Availability::WITHIN_2_MONTH, Availability::WITHIN_3_MONTH, Availability::DATE], true)
        ) {
            try {
                $email = (new UpdateConfirmationAvailabilityNonImmediateEmail())
                    ->to($user->getEmail())
                    ->context([
                        'user' => $user,
                        'jobPostings' => $em->getRepository(JobPosting::class)->findUserSuggestedAfterNextAvailability($user, 1, 5),
                    ])
                ;

                $this->mailer->send($email);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
