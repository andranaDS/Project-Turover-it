<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\User\Entity\User;
use App\User\Entity\UserProfileViews;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserProfileViewsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        foreach ($this->getData() as $data) {
            $userProfileViews = (new UserProfileViews())
                ->setUser($data['user'])
                ->setDate($data['date'])
                ->setCount($data['count'])
            ;
            $manager->persist($userProfileViews);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        $start = (new \DateTime())->modify('-2 month');
        $end = (new \DateTime());
        $interval = new \DateInterval('P1D');

        foreach (new \DatePeriod($start, $interval, $end) as $date) {
            foreach ($this->users as $user) {
                /* @var User $user */

                if (0 === random_int(0, 50)) {
                    continue;
                }

                $data[] = [
                    'user' => $user,
                    'date' => $date,
                    'count' => random_int(1, 150),
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'date' => (new \DateTime())->modify('-7 days'),
                'count' => 12,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'date' => (new \DateTime())->modify('-6 days'),
                'count' => 10,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'date' => (new \DateTime())->modify('-5 days'),
                'count' => 8,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'date' => (new \DateTime())->modify('-3 days'),
                'count' => 9,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'date' => (new \DateTime())->modify('-2 days'),
                'count' => 0,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'date' => (new \DateTime())->modify('-1 day'),
                'count' => 11,
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'date' => (new \DateTime())->modify('-5 days'),
                'count' => 8,
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'date' => (new \DateTime())->modify('-1 day'),
                'count' => 11,
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
        ];
    }
}
