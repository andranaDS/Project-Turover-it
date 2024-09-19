<?php

namespace App\Blog\DoctrineEventSubscriber;

use App\Blog\Entity\BlogCategory;
use App\Blog\Entity\BlogPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateBlogCategoryPostsCountSubscriber implements EventSubscriber
{
    private array $categories = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
            Events::preUpdate,
            Events::prePersist,
            Events::postFlush,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->prePersistOrPreRemoveProcess($args);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->prePersistOrPreRemoveProcess($args);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->preUpdateProcess($args);
    }

    private function preUpdateProcess(PreUpdateEventArgs $args): void
    {
        $post = $args->getObject();

        if (
            !$post instanceof BlogPost
            || false === $args->hasChangedField('category')
        ) {
            return;
        }

        if (null !== $oldCategory = $args->getOldValue('category')) {
            /* @var BlogCategory $oldCategory */
            $this->categories[$oldCategory->getId()] = $oldCategory;
        }
        if (null !== $newCategory = $args->getNewValue('category')) {
            /* @var BlogCategory $newCategory */
            $this->categories[$newCategory->getId()] = $newCategory;
        }
    }

    private function prePersistOrPreRemoveProcess(LifecycleEventArgs $args): void
    {
        $post = $args->getObject();

        if (
            !$post instanceof BlogPost
            || null === $category = $post->getCategory()
        ) {
            return;
        }

        $this->categories[$category->getId()] = $category;
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->categories)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->categories as $category) {
            /* @var BlogCategory $category */

            $category->setPostsCount($em->getRepository(BlogPost::class)->count([
                'category' => $category,
            ]));
        }

        $this->categories = [];

        $em->flush();
    }
}
