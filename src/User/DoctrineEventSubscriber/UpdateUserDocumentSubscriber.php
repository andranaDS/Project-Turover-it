<?php

namespace App\User\DoctrineEventSubscriber;

use App\User\Entity\UserDocument;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * TODO: Refacto this class.
 */
class UpdateUserDocumentSubscriber implements EventSubscriber
{
    private array $userDocuments = [];
    private array $updatedUserDocuments = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
            Events::postFlush,
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $document = $args->getObject();

        if (!$document instanceof UserDocument) {
            return;
        }

        if (null === $document->getUser()) {
            return;
        }

        $resumeDocuments = $document->getUser()->getDocuments()->filter(static function (UserDocument $userDocument) use ($document) {
            return $userDocument !== $document && true === $userDocument->getResume();
        });

        if (true === $document->getDefaultResume()) {
            if (!$resumeDocuments->isEmpty()) {
                $resumeDocuments->first()->setDefaultResume(true);
            } else {
                $document->getUser()->setProfileCompleted(false);
                $document->getUser()->setFormStep(null);
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->userDocuments)) {
            return;
        }

        // undefault old user resume
        foreach ($this->userDocuments as $userDocument) {
            /* @var UserDocument $userDocument */
            $userDocument->setDefaultResume(false);
            $this->updatedUserDocuments[] = $userDocument;
        }

        $this->userDocuments = [];
        $args->getObjectManager()->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $userDocument = $args->getObject();

        if (!$userDocument instanceof UserDocument) {
            return;
        }

        if ($args instanceof PreUpdateEventArgs && false === $args->hasChangedField('defaultResume')) {
            return;
        }

        if (\in_array($userDocument, $this->updatedUserDocuments, true)) {
            return;
        }

        // get old UserDocument::defaultResume
        $userDefaultResumeDocuments = $args->getObjectManager()->getRepository(UserDocument::class)->findOldDefaultResumes($userDocument);
        foreach ($userDefaultResumeDocuments as $document) {
            if ($document instanceof UserDocument) {
                $this->userDocuments[] = $document;
            }
        }
    }
}
