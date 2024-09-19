<?php

namespace App\Blog\DoctrineEventSubscriber;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostData;
use App\Blog\Entity\BlogPostTrace;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateBlogPostViewsCountSubscriber implements EventSubscriber
{
    private array $posts = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->posts)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->posts as $post) {
            /** @var BlogPost $post */
            $recentViewsCount = $em->getRepository(BlogPostTrace::class)->countRecent($post);
            $viewsCount = $em->getRepository(BlogPostTrace::class)->count([
                'post' => $post,
            ]);
            $blogPostData = $em->getRepository(BlogPostData::class)->findOneById($post->getId());

            if (null !== $blogPostData) {
                $blogPostData
                    ->setRecentViewsCount($recentViewsCount)
                    ->setViewsCount($viewsCount)
                ;
            }
        }

        $this->posts = [];
        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $postTrace = $args->getObject();

        if (
            !$postTrace instanceof BlogPostTrace
            || (null === $post = $postTrace->getPost())
            || (null === $postId = $post->getId())
            || isset($this->posts[$postId])
        ) {
            return;
        }

        $this->posts[$postId] = $post;
    }
}
