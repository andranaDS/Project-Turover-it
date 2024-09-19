<?php

namespace App\Forum\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Forum\Repository\ForumCategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ForumCategoryExistsSubscriber implements EventSubscriberInterface
{
    private ForumCategoryRepository $repository;

    public function __construct(ForumCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['process', EventPriorities::PRE_READ],
            ],
        ];
    }

    public function process(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ('api_forum_categories_topics_get_subresource' !== $request->attributes->get('_route')) {
            return;
        }

        $slug = $request->attributes->get('slug');
        if (!$this->repository->exists($slug)) {
            throw new NotFoundHttpException("The category \"$slug\" does not exist");
        }
    }
}
