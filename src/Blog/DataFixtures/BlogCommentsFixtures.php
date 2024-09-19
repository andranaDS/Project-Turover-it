<?php

namespace App\Blog\DataFixtures;

use App\Blog\Entity\BlogComment;
use App\Blog\Entity\BlogPost;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class BlogCommentsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $posts = [];
    private array $users = [];

    public function load(ObjectManager $manager): void
    {
        // fetch posts
        foreach ($manager->getRepository(BlogPost::class)->findAll() as $post) {
            $this->posts[$post->getId()] = $post;
        }

        // fetch users
        $filter = $manager->getFilters()->enable('soft_deleteable'); /* @phpstan-ignore-line */
        $filter->disableForEntity(User::class);
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        foreach ($this->getData() as $d) {
            $comment = (new BlogComment())
                ->setPost($d['post'])
                ->setAuthor($d['author'])
                ->setContent($d['content'])
                ->setCreatedAt($d['createdAt'])
                ->setUpdatedAt($d['updatedAt'])
                ->setDeletedAt($d['deletedAt'] ?? null)
            ;
            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $faker = Faker::create('fr_FR');

        $data = [];

        $data[] = [
            'content' => $faker->paragraph(),
            'createdAt' => new \DateTime('2000-01-01 12:00:00'),
            'updatedAt' => new \DateTime('2000-01-01 12:00:00'),
            'post' => $this->posts[1],
            'author' => $this->users['user-deleted@free-work.fr'],
        ];

        $commentsCount = 100;
        for ($i = 0; $i < $commentsCount; ++$i) {
            $createdAt = $faker->dateTimeBetween('- 6 months', '- 1 month');

            $data[] = [
                'content' => $faker->paragraph(mt_rand(1, 3)),
                'createdAt' => $createdAt,
                'updatedAt' => $faker->dateTimeBetween($createdAt),
                'deletedAt' => 0 === mt_rand(0, 10) ? $faker->dateTimeBetween($createdAt) : null,
                'post' => Arrays::getRandom($this->posts),
                'author' => Arrays::getRandom($this->users),
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'content' => 'Blog - Post 1 - Comment 1',
                'createdAt' => new \DateTime('2021-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 20:00:00'),
                'post' => $this->posts[1],
                'author' => $this->users['claude.monet@free-work.fr'],
            ],
            [
                'content' => 'Blog - Post 1 - Comment 2',
                'createdAt' => new \DateTime('2021-01-01 11:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 11:00:00'),
                'post' => $this->posts[1],
                'author' => $this->users['claude.monet@free-work.fr'],
            ],
            [
                'content' => 'Blog - Post 1 - Comment 3',
                'createdAt' => new \DateTime('2021-01-01 12:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 12:00:00'),
                'post' => $this->posts[1],
                'author' => $this->users['vincent.van-gogh@free-work.fr'],
            ],
            [
                'content' => 'Blog - Post 2 - Comment 1',
                'createdAt' => new \DateTime('2021-01-01 12:30:00'),
                'updatedAt' => new \DateTime('2021-01-01 12:30:00'),
                'post' => $this->posts[2],
                'author' => $this->users['vincent.van-gogh@free-work.fr'],
            ],
            [
                'content' => 'Blog - Post 2 - Comment 2',
                'createdAt' => new \DateTime('2021-01-01 13:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 13:00:00'),
                'post' => $this->posts[2],
                'author' => $this->users['henri.matisse@free-work.fr'],
            ],
            [
                'content' => 'Blog - Post 3 - Comment 1',
                'updatedAt' => new \DateTime('2021-01-01 13:30:00'),
                'createdAt' => new \DateTime('2021-01-01 13:30:00'),
                'deletedAt' => new \DateTime('2021-01-01 13:30:00'),
                'post' => $this->posts[3],
                'author' => $this->users['henri.matisse@free-work.fr'],
            ],
            [
                'content' => 'Blog - Post 3 - Comment 2',
                'createdAt' => new \DateTime('2021-01-01 14:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 14:00:00'),
                'post' => $this->posts[3],
                'author' => $this->users['claude.monet@free-work.fr'],
            ],
            [
                'content' => 'Blog - Post 4 - Comment 1',
                'createdAt' => new \DateTime('2021-01-01 14:30:00'),
                'updatedAt' => new \DateTime('2021-01-01 14:30:00'),
                'deletedAt' => new \DateTime('2021-01-01 14:30:00'),
                'post' => $this->posts[4],
                'author' => $this->users['claude.monet@free-work.fr'],
            ],
            [
                'content' => 'Blog - Post 4 - Comment 2',
                'createdAt' => new \DateTime('2021-01-01 14:30:00'),
                'updatedAt' => new \DateTime('2021-01-01 14:30:00'),
                'post' => $this->posts[4],
                'author' => $this->users['user-deleted@free-work.fr'],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            BlogPostsFixtures::class,
            UsersFixtures::class,
        ];
    }
}
