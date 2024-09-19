<?php

namespace App\Blog\DoctrineEventSubscriber;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostData;
use App\Blog\Entity\BlogPostUpvote;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateBlogPostUpvotesCountSubscriber implements EventSubscriber
{
    private array $posts = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postRemove,
            Events::postPersist,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
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
            $upvotesCount = $em->getRepository(BlogPostUpvote::class)->countByPost($post);

            $blogPostData = $em->getRepository(BlogPostData::class)->findOneById($post->getId());

            if (null !== $blogPostData) {
                $blogPostData->setUpvotesCount($upvotesCount);
            }
        }

        $this->posts = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $postUpvote = $args->getObject();

        if (
            !$postUpvote instanceof BlogPostUpvote
            || (null === $post = $postUpvote->getPost())
            || (null === $postId = $post->getId())
        ) {
            return;
        }

        $this->posts[$postId] = $post;
    }
}
