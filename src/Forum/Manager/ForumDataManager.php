<?php

namespace App\Forum\Manager;

use App\Forum\Entity\ForumPostUpvote;
use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicFavorite;
use App\Forum\Entity\ForumTopicTrace;
use App\User\Contracts\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Arrays;

class ForumDataManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUserForumData(UserInterface $user, array $scopes = []): array
    {
        $data = [];

        if (\in_array('forum_topic_traces', $scopes, true)) {
            $data['forum_topic_traces'] = $this->getUserForumTopicTraces($user);
        }
        if (\in_array('forum_topic_favorites', $scopes, true)) {
            $data['forum_topic_favorites'] = $this->getUserForumTopicFavorites($user);
        }
        if (\in_array('forum_topic_participations', $scopes, true)) {
            $data['forum_topic_participations'] = $this->getUserForumTopicsParticipations($user);
        }
        if (\in_array('forum_post_upvotes', $scopes, true)) {
            $data['forum_post_upvotes'] = $this->getUserForumPostUpvotes($user);
        }

        return $data;
    }

    private function getUserForumTopicTraces(UserInterface $user): array
    {
        $data = [];
        foreach ($this->em->getRepository(ForumTopicTrace::class)->findLastByUser($user) as $topicTrace) {
            /** @var ForumTopicTrace $topicTrace */
            $topicId = $topicTrace->getTopicId();
            $topic = $this->em->getRepository(ForumTopic::class)->findOneById($topicId);
            $readAt = $topicTrace->getReadAt();
            if (null === $topic || null === $topicId || null === $readAt) {
                continue;
            }
            $data[$topicId] = $readAt->format(\DateTime::RFC3339);
        }

        return $data;
    }

    private function getUserForumTopicFavorites(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(ForumTopicFavorite::class)->findTopicIdByUser($user), function (array $element) {
            return (int) $element['topicId'];
        });
    }

    private function getUserForumTopicsParticipations(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(ForumTopic::class)->findParticipations($user), function (array $element) {
            return (int) $element['topicId'];
        });
    }

    private function getUserForumPostUpvotes(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(ForumPostUpvote::class)->findPostIdByUser($user), function (array $element) {
            return (int) $element['postId'];
        });
    }
}
