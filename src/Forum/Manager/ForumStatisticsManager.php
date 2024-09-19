<?php

namespace App\Forum\Manager;

use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumTopic;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class ForumStatisticsManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getGlobalData(): array
    {
        return [
            'topicsCount' => $this->em->getRepository(ForumTopic::class)->countAll(),
            'postsCount' => $this->em->getRepository(ForumPost::class)->countAll(),
            'recentPostsCount' => $this->em->getRepository(ForumPost::class)->countRecent(),
            'contributorsCount' => $this->em->getRepository(User::class)->countContributors(),
            'forumActiveUsersCount' => $this->em->getRepository(User::class)->countForumActive(),
        ];
    }

    public function getDataByDateInterval(\DateTime $start, \DateTime $end): array
    {
        $now = Carbon::now();

        return [
            'topicsCount' => $this->em->getRepository(ForumTopic::class)->countAll($start, $end),
            'postsCount' => $this->em->getRepository(ForumPost::class)->countAll($start, $end),
            'contributorsCount' => $this->em->getRepository(ForumPost::class)->countContributorsByDateInterval($start, $end),
            'newContributorsCount' => $this->em->getRepository(ForumPost::class)->countNewContributorsByDateInterval($start, $end),
            'contributorsCountSixMonthsAgo' => $this->em->getRepository(ForumPost::class)->countContributorsByDateInterval($now->copy()->subMonths(6), $now),
        ];
    }
}
