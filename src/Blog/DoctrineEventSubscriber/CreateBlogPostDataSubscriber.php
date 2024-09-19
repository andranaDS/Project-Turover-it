<?php

namespace App\Blog\DoctrineEventSubscriber;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostData;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CreateBlogPostDataSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $post = $args->getObject();

        if (!$post instanceof BlogPost) {
            return;
        }

        if (null === $post->getId()) {
            return;
        }

        $blogPostData = new BlogPostData($post->getId());

        $args->getObjectManager()->persist($blogPostData);
        $args->getObjectManager()->flush();
    }
}
