<?php

namespace App\Core\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Blog\Entity\BlogComment;
use App\Company\Entity\Company;
use App\Company\Entity\CompanyPicture;
use App\Company\Entity\Site;
use App\JobPosting\Entity\JobPosting;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TmpFixUserDeletedSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['process', EventPriorities::PRE_READ],
            ],
        ];
    }

    public function process(KernelEvent $event): void
    {
        if (!\in_array($event->getRequest()->get('_route'), [
            'api_forum_topics_get_collection',
            'api_forum_categories_get_collection',
            'api_feeds_get_collection',
            'api_feeds_get_item',
        ], true)) {
            return;
        }

        $classes = [User::class, BlogComment::class, Company::class, CompanyPicture::class, Site::class, JobPosting::class];
        /** @var SoftDeleteableFilter $softDeleteableFilter */
        $softDeleteableFilter = $this->em->getFilters()->getFilter('soft_deleteable');

        foreach ($classes as $class) {
            $softDeleteableFilter->disableForEntity($class);
        }
    }
}
