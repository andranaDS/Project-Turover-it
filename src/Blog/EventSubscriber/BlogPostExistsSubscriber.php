<?php

namespace App\Blog\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Blog\Repository\BlogPostRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class BlogPostExistsSubscriber implements EventSubscriberInterface
{
    private BlogPostRepository $repository;

    public function __construct(BlogPostRepository $repository)
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

        if ('api_blog_posts_comments_get_subresource' !== $request->attributes->get('_route')) {
            return;
        }

        $slug = $request->attributes->get('slug');
        if (!$this->repository->exists($slug)) {
            throw new NotFoundHttpException("The post \"$slug\" does not exist");
        }
    }
}
