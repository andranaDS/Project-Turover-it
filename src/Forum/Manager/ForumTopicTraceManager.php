<?php

namespace App\Forum\Manager;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicTrace;
use App\User\Contracts\UserInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class ForumTopicTraceManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function updateLastTopicTrace(UserInterface $user, ForumTopic $topic): void
    {
        // reset old
        if (null !== $oldLast = $this->em->getRepository(ForumTopicTrace::class)->findOneBy([
            'user' => $user,
            'topicId' => $topic->getId(),
            'last' => true,
        ])) {
            $oldLast->setLast(false);
        }

        // set new
        if (null !== $newLast = $this->em->getRepository(ForumTopicTrace::class)->findOneBy([
            'user' => $user,
            'topicId' => $topic->getId(),
        ], [
            'readAt' => Criteria::DESC,
        ])) {
            $newLast->setLast(true);
        }
    }
}
