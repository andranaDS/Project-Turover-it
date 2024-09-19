<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicData;
use App\Forum\Entity\ForumTopicTrace;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateForumTopicViewsCountSubscriber implements EventSubscriber
{
    private array $topics = [];
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
        if (empty($this->topics)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->topics as $topic) {
            /** @var ForumTopic $topic */
            $topicData = $em->getRepository(ForumTopicData::class)->findOneById($topic->getId());
            $viewsCount = $em->getRepository(ForumTopicTrace::class)->count([
                'topicId' => $topic->getId(),
                'markAllAsRead' => false,
                'created' => false,
            ]);
            if (null !== $topicData) {
                $topicData->setViewsCount($viewsCount);
            }
        }

        $this->topics = [];
        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $topicTrace = $args->getObject();

        if (
            !$topicTrace instanceof ForumTopicTrace
            || (null === $topicId = $topicTrace->getTopicId())
            || (null === $topic = $this->em->getRepository(ForumTopic::class)->findOneById($topicId))
            || isset($this->topics[$topicId])
        ) {
            return;
        }

        $this->topics[$topicId] = $topic;
    }
}
