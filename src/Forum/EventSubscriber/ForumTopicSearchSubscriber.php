<?php

namespace App\Forum\EventSubscriber;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumTopic;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ForumTopicSearchSubscriber implements EventSubscriberInterface
{
    public function onPreSerialize(ViewEvent $event): void
    {
        if (
            (null !== $request = $event->getRequest())
            && 'api_forum_topics_get_search_collection' === $request->attributes->get('_route')
            && null !== $searchParam = $request->query->get('q')
        ) {
            /* @var Paginator $paginator */
            $paginator = $event->getControllerResult();
            // use to splits words: $regexExp = \preg_replace('/\s+/', '|', $searchParam);

            foreach ($paginator->getIterator() as $forumTopic) {
                if ($forumTopic instanceof ForumTopic) {
                    // filter posts on contentJson containing $searchParam
                    $filteredPosts = $forumTopic->getPosts()->filter(static function (ForumPost $post) use ($searchParam) {
                        $content = $post->getContent();

                        return $content && preg_match('/(' . $searchParam . ')/i', $content) > 0;
                    });

                    // used to reindex ArrayCollection
                    $filteredPosts = new ArrayCollection($filteredPosts->getValues());

                    // update ForumTopic
                    $forumTopic->setPosts($filteredPosts);
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onPreSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }
}
