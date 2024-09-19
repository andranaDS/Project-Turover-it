<?php

namespace App\Partner\EventSubscriber;

use App\Partner\Entity\Partner;
use App\Partner\Enum\Partner as PartnerEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PartnerSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityUpdatedEvent::class => ['preUpdate'],
            BeforeEntityDeletedEvent::class => ['preDelete'],
        ];
    }

    public function preUpdate(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Partner) {
            return;
        }

        if (null === $partnerNone = $this->entityManager->getRepository(Partner::class)->findOneByPartner(PartnerEnum::NONE)) {
            return;
        }

        /** @var array $oldPartner */
        $oldPartner = $this->entityManager->getUnitOfWork()->getOriginalEntityData($entity);

        if ($oldPartner['distribution'] === $entity->getDistribution()) {
            return;
        }

        if ($oldPartner['distribution'] < $entity->getDistribution()) {
            $partnerNone->setDistribution($partnerNone->getDistribution() - $entity->getDistribution() + $oldPartner['distribution']);
        }

        if ($oldPartner['distribution'] > $entity->getDistribution()) {
            $partnerNone->setDistribution($partnerNone->getDistribution() + ($oldPartner['distribution'] - $entity->getDistribution()));
        }
    }

    public function preDelete(BeforeEntityDeletedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Partner) {
            return;
        }

        if ($entity->getDistribution() > 0 && null !== ($partnerNone = $this->entityManager->getRepository(Partner::class)->findOneByPartner(PartnerEnum::NONE))) {
            /* @var Partner $partnerNone */
            $partnerNone->setDistribution(
                $partnerNone->getDistribution() +
                $entity->getDistribution());
        }
    }
}
