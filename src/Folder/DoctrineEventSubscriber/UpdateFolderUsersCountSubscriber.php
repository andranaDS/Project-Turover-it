<?php

namespace App\Folder\DoctrineEventSubscriber;

use App\Folder\Entity\FolderUser;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateFolderUsersCountSubscriber implements EventSubscriber
{
    private array $folders = [];

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
        if (empty($this->folders)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->folders as $folder) {
            $folder->setUsersCount($em->getRepository(FolderUser::class)->countByFolder($folder));
        }

        $this->folders = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $folderUser = $args->getObject();

        if (
            !$folderUser instanceof FolderUser
            || (null === $folder = $folderUser->getFolder())
            || (null === $folderId = $folder->getId())
            || isset($this->folders[$folderId])
        ) {
            return;
        }

        $this->folders[$folderId] = $folder;
    }
}
