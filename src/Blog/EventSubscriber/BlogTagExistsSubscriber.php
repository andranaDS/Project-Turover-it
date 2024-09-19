<?php

namespace App\Blog\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Blog\Repository\BlogTagRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class BlogTagExistsSubscriber implements EventSubscriberInterface
{
    private BlogTagRepository $repository;

    public function __construct(BlogTagRepository $repository)
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

        if ('api_blog_tags_posts_get_subresource' !== $request->attributes->get('_route')) {
            return;
        }

        $slug = $request->attributes->get('slug');
        if (!$this->repository->exists($slug)) {
            throw new NotFoundHttpException("The tag \"$slug\" does not exist");
        }
    }
}