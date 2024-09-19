<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicTrace;
use App\Forum\Manager\ForumTopicTraceManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateForumTopicTraceLastSubscriber implements EventSubscriber
{
    private array $topicTraces = [];
    private ForumTopicTraceManager $fttm;
    private EntityManagerInterface $em;

    public function __construct(ForumTopicTraceManager $fttm, EntityManagerInterface $em)
    {
        $this->fttm = $fttm;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->topicTraces)) {
            return;
        }

        $couples = [];
        foreach ($this->topicTraces as $topicTrace) {
            /** @var ForumTopicTrace $topicTrace */
            $user = $topicTrace->getUser();
            $topic = $this->em->getRepository(ForumTopic::class)->findOneById($topicTrace->getTopicId());

            if (null === $user || null === $user->getId() || null === $topic || null === $topic->getId() || isset($couples[$user->getId()][$topic->getId()])) {
                continue;
            }

            $couples[$user->getId()][$topic->getId()] = true;
            $this->fttm->updateLastTopicTrace($user, $topic);
        }

        $this->topicTraces = [];
        $args->getObjectManager()->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $topicTrace = $args->getObject();

        if (!$topicTrace instanceof ForumTopicTrace) {
            return;
        }

        $this->topicTraces[] = $topicTrace;
    }
}
