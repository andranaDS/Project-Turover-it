<?php

namespace App\Core\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RemoveCacheTagsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['removeCacheTags', -2],
        ];
    }

    public function removeCacheTags(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $routes = [
            'api_skills_get_collection',
            'api_blog_posts_freework_get_most_viewed_collection',
            'api_trends_get_item',
            'api_companies_get_homepage_collection',
        ];

        if (\in_array($request->attributes->get('_route'), $routes, true)) {
            $response->headers->remove('Cache-Tags');
        }
    }
}
