<?php

namespace App\Messaging\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\JobPosting\DataFixtures\ApplicationFixtures;
use App\Messaging\Entity\Feed;
use App\Messaging\Entity\FeedUser;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class FeedFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];

    public function load(ObjectManager $manager): void
    {
        // fetch all users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        foreach ($this->getData() as $d) {
            $feed = (new Feed())
                ->setApplication($d['application'])
            ;

            foreach ($d['feedUsers'] as $feedUser) {
                $feedUser = (new FeedUser())
                    ->setFavorite($feedUser['favorite'])
                    ->setViewAt($feedUser['viewAt'])
                    ->setUser($feedUser['user'])
                ;

                $feed->addFeedUser($feedUser);

                $manager->persist($feedUser);
            }

            $manager->persist($feed);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        $feedCount = 50;
        $userCount = 2;
        $faker = Faker::create('fr_FR');

        for ($i = 0; $i <= $feedCount; ++$i) {
            $users = Arrays::getRandomSubarray($this->users, $userCount, $userCount);
            $data[$i] = [
                'application' => null,
            ];

            foreach ($users as $user) {
                $data[$i]['feedUsers'][] = [
                    'favorite' => mt_rand(0, 1),
                    'viewAt' => $faker->dateTimeBetween('- 6 months', '- 1 month'),
                    'user' => $user,
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'application' => null,
                'feedUsers' => [
                    [
                        'favorite' => false,
                        'viewAt' => new \DateTime('2021-01-10 23:00:00'),
                        'user' => $this->users['claude.monet@free-work.fr'],
                    ],
                    [
                        'favorite' => false,
                        'viewAt' => new \DateTime('2021-02-10 20:00:00'),
                        'user' => $this->users['vincent.van-gogh@free-work.fr'],
                    ],
                ],
            ],
            [
                'application' => null,
                'feedUsers' => [
                    [
                        'favorite' => true,
                        'viewAt' => new \DateTime('2021-01-10 23:00:00'),
                        'user' => $this->users['claude.monet@free-work.fr'],
                    ],
                    [
                        'favorite' => false,
                        'viewAt' => new \DateTime('2021-01-10 19:00:00'),
                        'user' => $this->users['auguste.renoir@free-work.fr'],
                    ],
                ],
            ],
            [
                'application' => $this->users['claude.monet@free-work.fr']->getApplications()->first(),
                'feedUsers' => [
                    [
                        'favorite' => false,
                        'viewAt' => new \DateTime('2021-03-10 20:00:00'),
                        'user' => $this->users['claude.monet@free-work.fr'],
                    ],
                    [
                        'favorite' => true,
                        'viewAt' => new \DateTime('2021-04-10 20:00:00'),
                        'user' => $this->users['henri.matisse@free-work.fr'],
                    ],
                ],
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            ApplicationFixtures::class,
            UsersFixtures::class,
        ];
    }
}
