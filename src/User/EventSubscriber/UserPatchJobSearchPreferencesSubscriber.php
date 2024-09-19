<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Partner\Entity\Partner;
use App\Partner\Enum\Partner as PartnerEnum;
use App\User\Entity\User;
use App\User\Enum\FreelanceLegalStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserPatchJobSearchPreferencesSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['preValidate', EventPriorities::PRE_VALIDATE],
                ['postWrite', EventPriorities::POST_WRITE],
            ],
        ];
    }

    public function preValidate(ViewEvent $event): void
    {
        $user = $event->getControllerResult();

        if (!$user instanceof User || 'api_users_freework_patch_profile_job_search_preferences_item' !== $event->getRequest()->attributes->get('_route')) {
            return;
        }

        if (false === $user->getEmployee()) {
            $user->setContracts([]);
        }
    }

    public function postWrite(ViewEvent $event): void
    {
        $user = $event->getControllerResult();

        if (!$user instanceof User || 'api_users_freework_patch_profile_job_search_preferences_item' !== $event->getRequest()->attributes->get('_route')) {
            return;
        }

        if (null === $user->getPartner() && true === $user->getFreelance() && FreelanceLegalStatus::STATUS_IN_PROGRESS === $user->getFreelanceLegalStatus()) {
            $partners = $this->entityManager->getRepository(Partner::class)->getDistributionsRange();
            $randNumber = random_int(0, 100);

            foreach ($partners as $partner) {
                if ($randNumber < $partner['range'] && PartnerEnum::NONE !== $partner['partner']->getPartner()) {
                    $user->setPartner($partner['partner']);
                    break;
                }
            }

            $this->entityManager->flush();
        }

        if (false === $user->getEmployee()) {
            $user->setContracts([]);
        }
    }
}
