<?php

namespace App\Admin\EventSubscriber;

use App\Blog\Entity\BlogComment;
use App\Company\Entity\Company;
use App\Company\Entity\CompanyPicture;
use App\Company\Entity\Site;
use App\JobPosting\Entity\JobPosting;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SoftDeleteableSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityBuiltEvent::class => ['process'],
        ];
    }

    public function process(AfterEntityBuiltEvent $event): void
    {
        $classes = [User::class, BlogComment::class, Company::class, CompanyPicture::class, Site::class, JobPosting::class];
        /** @var SoftDeleteableFilter $softDeleteableFilter */
        $softDeleteableFilter = $this->entityManager->getFilters()->getFilter('soft_deleteable');

        if (false === \in_array($event->getEntity()->getFqcn(), $classes, true)) {
            foreach ($classes as $class) {
                $softDeleteableFilter->disableForEntity($class);
            }
        }
    }
}
