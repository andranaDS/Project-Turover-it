<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumCategory;
use App\Forum\Entity\ForumPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateForumCategoryLastPostSubscriber implements EventSubscriber
{
    private array $categories = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postRemove,
            Events::postPersist,
            Events::postUpdate,
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

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->categories)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->categories as $category) {
            /** @var ForumCategory $category */
            $lastPost = $em->getRepository(ForumPost::class)->findLastPostByCategory($category);

            $category->setLastPost($lastPost);
        }

        $this->categories = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $post = $args->getObject();

        if (!$post instanceof ForumPost
            || (null === $topic = $post->getTopic())
            || (null === $category = $topic->getCategory())
            || (null === $categoryId = $category->getId())
            || isset($this->categories[$categoryId])
        ) {
            return;
        }

        $this->categories[$categoryId] = $category;
    }
}
