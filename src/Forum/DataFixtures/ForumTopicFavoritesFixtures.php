<?php

namespace App\Forum\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicFavorite;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ForumTopicFavoritesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users;
    private array $topics;

    public function load(ObjectManager $manager): void
    {
        // fetch all users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // fetch all topics
        foreach ($manager->getRepository(ForumTopic::class)->findAll() as $topic) {
            /* @var ForumTopic $topic */
            $this->topics[$topic->getTitle()] = $topic;
        }

        // process data
        foreach ($this->getData() as $d) {
            $topicFavorite = (new ForumTopicFavorite())
                ->setTopic($d['topic'])
                ->setUser($d['user'])
                ->setCreatedAt($d['createdAt'])
            ;

            $manager->persist($topicFavorite);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        $faker = Factory::create('fr_Fr');

        foreach ($this->topics as $topic) {
            /** @var ForumTopic $topic */
            $topicFavoritesUsers = Arrays::getRandomSubarray($this->users, 0, 16);
            foreach ($topicFavoritesUsers as $topicFavoritesUser) {
                /* @var User $topicFavoritesUser */

                $data[] = [
                    'topic' => $topic,
                    'user' => $topicFavoritesUser,
                    'createdAt' => $faker->dateTimeBetween('- 6 months'),
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'topic' => $this->topics['Category 1.1 - Topic 1'],
                'user' => $this->users['claude.monet@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:30:00'),
            ],
            [
                'topic' => $this->topics['Category 4.1 - Topic 1'],
                'user' => $this->users['claude.monet@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:45:00'),
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            UsersFixtures::class,
            ForumTopicsFixtures::class,
        ];
    }
}
