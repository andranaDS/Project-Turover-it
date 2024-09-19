<?php

namespace App\Blog\DataFixtures;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostUpvote;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BlogPostUpvotesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];
    private array $posts = [];

    public function load(ObjectManager $manager): void
    {
        // fetch all users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // fetch posts
        foreach ($manager->getRepository(BlogPost::class)->findAll() as $post) {
            $this->posts[$post->getId()] = $post;
        }

        // process data
        foreach ($this->getData() as $d) {
            $postUpvote = (new BlogPostUpvote())
                ->setUser($d['user'])
                ->setPost($d['post'])
            ;
            $manager->persist($postUpvote);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        foreach ($this->posts as $post) {
            /* @var BlogPost $post */

            $postUpvoteUsers = Arrays::getRandomSubarray($this->users, 1, 16);

            foreach ($postUpvoteUsers as $postUpvoteUser) {
                /* @var User $postUpvoteUser */
                $data[] = [
                    'user' => $postUpvoteUser,
                    'post' => $post,
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['pablo.picasso@free-work.fr'],
                'post' => $this->posts[1],
            ],
            [
                'user' => $this->users['henri.matisse@free-work.fr'],
                'post' => $this->posts[1],
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'post' => $this->posts[2],
            ],
            [
                'user' => $this->users['henri.matisse@free-work.fr'],
                'post' => $this->posts[2],
            ],
            [
                'user' => $this->users['pablo.picasso@free-work.fr'],
                'post' => $this->posts[5],
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'post' => $this->posts[5],
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'post' => $this->posts[1],
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'post' => $this->posts[3],
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'post' => $this->posts[6],
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'post' => $this->posts[6],
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
