<?php

namespace App\Blog\DataFixtures;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostTrace;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BlogPostTracesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users;
    private array $posts;

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // fetch posts
        foreach ($manager->getRepository(BlogPost::class)->findAll() as $post) {
            /* @var BlogPost $post */
            $this->posts[$post->getTitle()] = $post;
        }

        // process data
        foreach ($this->getData() as $d) {
            $postTrace = (new BlogPostTrace())
                ->setPost($d['post'])
                ->setUser($d['user'])
                ->setReadAt($d['readAt'])
                ->setIp($d['ip'])
            ;

            $manager->persist($postTrace);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        $faker = Factory::create('fr_Fr');

        foreach ($this->posts as $post) {
            /** @var BlogPost $post */
            $blogPostUsers = Arrays::getRandomSubarray($this->users, 0, 16);
            foreach ($blogPostUsers as $blogPostUser) {
                /* @var User $blogPostUser */

                $readAt = $post->getCreatedAt();

                $postTracesCount = mt_rand(1, 5);
                for ($i = 0; $i < $postTracesCount; ++$i) {
                    $readAt = $faker->dateTimeBetween($readAt);
                    $data[] = [
                        'post' => $post,
                        'user' => $blogPostUser,
                        'readAt' => $readAt,
                        'ip' => $faker->ipv4(),
                    ];
                }
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'post' => $this->posts['Blog - Category 1 - Post 1'],
                'user' => $this->users['claude.monet@free-work.fr'],
                'readAt' => new \DateTime('2021-01-01 13:30:00'),
                'ip' => '1.2.3.4',
            ],
            [
                'post' => $this->posts['Blog - Category 1 - Post 1'],
                'user' => $this->users['claude.monet@free-work.fr'],
                'readAt' => new \DateTime('2021-01-01 13:35:00'),
                'ip' => '1.2.3.4',
            ],
            [
                'post' => $this->posts['Blog - Category 1 - Post 1'],
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'readAt' => new \DateTime('2021-01-01 13:40:00'),
                'ip' => '1.2.3.4',
            ],
            [
                'post' => $this->posts['Blog - Category 1 - Post 2'],
                'user' => $this->users['claude.monet@free-work.fr'],
                'readAt' => (new \DateTime())->setTime(0, 15),
                'ip' => '1.2.3.4',
            ],
            [
                'post' => $this->posts['Blog - Category 2 - Post 1'],
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'readAt' => new \DateTime('2021-01-01 19:40:00'),
                'ip' => '1.2.3.4',
            ],
            [
                'post' => $this->posts['Blog - Category 2 - Post 1'],
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'readAt' => (new \DateTime())->setTime(0, 30),
                'ip' => '1.2.3.4',
            ],
            [
                'post' => $this->posts['Blog - Category 2 - Post 1'],
                'user' => $this->users['claude.monet@free-work.fr'],
                'readAt' => (new \DateTime())->setTime(0, 45),
                'ip' => '1.2.3.4',
            ],
            [
                'post' => $this->posts['Blog - Category 2 - Post 1'],
                'user' => $this->users['claude.monet@free-work.fr'],
                'readAt' => (new \DateTime())->setTime(1, 0),
                'ip' => '1.2.3.4',
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
            BlogPostsFixtures::class,
        ];
    }
}
