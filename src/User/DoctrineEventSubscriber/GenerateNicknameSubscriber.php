<?php

namespace App\User\DoctrineEventSubscriber;

use App\User\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\Translation\TranslatorInterface;

class GenerateNicknameSubscriber implements EventSubscriber
{
    private array $users = [];
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postFlush,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof User && null === $object->getDeletedAt() && null === $object->getNickname()) {
            $this->users[] = $object;
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->users)) {
            return;
        }

        foreach ($this->users as $user) {
            $user->setNickname($this->translator->trans('user.nickname.default', ['%id%' => $user->getId()]));
        }

        $this->users = [];

        $args->getObjectManager()->flush();
    }
}
