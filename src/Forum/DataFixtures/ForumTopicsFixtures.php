<?php

namespace App\Forum\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\Core\Util\Strings;
use App\Forum\Entity\ForumCategory;
use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumPostReport;
use App\Forum\Entity\ForumPostUpvote;
use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicTrace;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use ProseMirror\ProseMirror;
use Symfony\Component\Yaml\Yaml;

class ForumTopicsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];
    private array $categories = [];

    public function load(ObjectManager $manager): void
    {
        // fetch all users
        $filter = $manager->getFilters()->enable('soft_deleteable'); /* @phpstan-ignore-line */
        $filter->disableForEntity(User::class);
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // fetch leaves categories
        foreach ($manager->getRepository(ForumCategory::class)->findBy([
            'level' => 1,
        ]) as $category) {
            /* @var ForumCategory $category */
            $this->categories[$category->getTitle()] = $category;
        }

        // process topics/posts data
        foreach ($this->getData() as $data) {
            $topic = $this->createTopic($data, $manager);
            $manager->persist($topic);

            if (\array_key_exists('traces', $data)) {
                $manager->flush();
                foreach ($data['traces'] as $dataTrace) {
                    $topicTrace = (new ForumTopicTrace())
                        ->setUser($dataTrace['user'])
                        ->setReadAt($dataTrace['readAt'])
                        ->setIp($dataTrace['ip'])
                        ->setTopicId($topic->getId())
                    ;
                    $manager->persist($topicTrace);
                }
            }
        }

        $manager->flush();
    }

    public function createTopic(array $data, ObjectManager $manager): ForumTopic
    {
        $topic = (new ForumTopic())
            ->setCategory($data['category'])
            ->setTitle($data['title'])
            ->setPinned($data['pinned'])
            ->setAuthor($data['author'])
            ->setMetaTitle($data['metaTitle'] ?? null)
            ->setMetaDescription($data['metaDescription'] ?? null)
        ;

        // topic posts
        foreach ($data['posts'] as $dataPost) {
            $this->createPost($dataPost, $topic);
        }

        return $topic;
    }

    public function createPost(array $data, ForumTopic $topic): ?ForumPost
    {
        if ('' === $data['contentHtml']) {
            return null;
        }

        $post = (new ForumPost())
            ->setAuthor($data['author'])
            ->setContentHtml($data['contentHtml'])
        ;

        $topic->addPost($post);

        try {
            $post->setContentJson(Json::encode(ProseMirror::htmlToJson($data['contentHtml'])));
        } catch (JsonException $e) {
            // @ignoreException
        }

        $createdAt = $data['createdAt'] ?? null;
        if (null !== $createdAt) {
            $post->setCreatedAt($createdAt);
        }

        $updatedAt = $data['updatedAt'] ?? null;
        if (null !== $updatedAt) {
            $post->setUpdatedAt($updatedAt);
        }

        $post->setDeletedAt($data['deletedAt'] ?? null)
            ->setModeratedAt($data['moderatedAt'] ?? null)
        ;

        if (null !== $post->getDeletedAt() || null !== $post->getModeratedAt()) {
            $post->setContentHtml(null)
                ->setContentJson(null)
            ;
        }

        // children
        foreach (($data['children'] ?? []) as $d) {
            if (null !== $postChild = $this->createPost($d, $topic)) {
                $post->addChild($postChild);
            }
        }

        // soft deletable
        if (0 === $post->getChildren()->count() && null !== $post->getDeletedAt()) {
            $post->setHidden(true);
        }

        // post reports
        foreach (($data['reports'] ?? []) as $dataReport) {
            $postReport = (new ForumPostReport())
                ->setUser($dataReport['user'])
                ->setContent($dataReport['content'])
            ;
            $post->addReport($postReport);
        }

        // post upvote
        foreach (($data['upvotes'] ?? []) as $user) {
            $postUpvote = (new ForumPostUpvote())
                ->setUser($user)
            ;
            $post->addUpvote($postUpvote);
        }

        return $post;
    }

    public function getDevData(): array
    {
        $faker = Faker::create('fr_FR');

        $data = [];

        // fixed
        $data[] = [
            'category' => $this->categories['Par où commencer ?'],
            'title' => 'Retour de Benzema en équipe de France',
            'pinned' => true,
            'author' => $this->users['jacques.delamballerie@free-work.fr'],
            'posts' => [
                [
                    'author' => $this->users['jacques.delamballerie@free-work.fr'],
                    'contentHtml' => '<p>Que pensez-vous du retour de Benzema en équipe de France ?</p>',
                    'createdAt' => new \DateTime('2021-06-01 12:00:00'),
                    'updatedAt' => new \DateTime('2021-06-01 12:00:00'),
                ],
                [
                    'author' => $this->users['user-deleted@free-work.fr'],
                    'contentHtml' => '<p>Moi je suis contre, il va créer pleins de problèmes... comme toujours ! Avez-vous oublié Knysna ?</p>',
                    'children' => [
                        [
                            'author' => Arrays::getRandom($this->users),
                            'contentHtml' => '<p>Bien tenté, mais il n\'était pas selectionné en 2010.</p>',
                            'createdAt' => new \DateTime('2021-06-01 12:15:00'),
                            'updatedAt' => new \DateTime('2021-06-01 12:15:00'),
                        ],
                        [
                            'author' => $this->users['user-deleted@free-work.fr'],
                            'contentHtml' => '<p>😂😂😂😂😂</p>',
                            'createdAt' => new \DateTime('2021-06-01 17:00:00'),
                            'updatedAt' => new \DateTime('2021-06-01 17:00:00'),
                        ],
                    ],
                    'createdAt' => new \DateTime('2021-06-01 13:00:00'),
                    'updatedAt' => new \DateTime('2021-06-01 13:00:00'),
                ],
                [
                    'author' => Arrays::getRandom($this->users),
                    'contentHtml' => '<p>Je suis sûr que ça va super bien se passer ! Il a tout de même gagné 4 ligues des champions. Probablement le meilleur attaquant français de tous les temps.</p>',
                    'createdAt' => new \DateTime('2021-06-01 14:00:00'),
                    'updatedAt' => new \DateTime('2021-06-01 14:00:00'),
                ],
                [
                    'author' => Arrays::getRandom($this->users),
                    'contentHtml' => '<p>C`est une très bonne nouvelle ! Sportivement c\'est une plus value indéniable, d\'autant plus vu la méforme actuelle de l\'attaque tricolore.</p>',
                    'children' => [
                        [
                            'author' => Arrays::getRandom($this->users),
                            'contentHtml' => '<p>Méforme ? Mbappé 42 buts cette saison !</p>',
                            'children' => [
                                [
                                    'author' => $this->users['user-deleted@free-work.fr'],
                                    'contentHtml' => '<p>Et combien de but en équipe de France ?</p>',
                                    'children' => [
                                        [
                                            'author' => Arrays::getRandom($this->users),
                                            'contentHtml' => '<p>Il y a eu 3 matches dans les éliminatoires...</p>',
                                            'createdAt' => new \DateTime('2021-06-01 17:30:00'),
                                            'updatedAt' => new \DateTime('2021-06-01 17:30:00'),
                                        ],
                                    ],
                                    'createdAt' => new \DateTime('2021-06-01 16:30:00'),
                                    'updatedAt' => new \DateTime('2021-06-01 16:30:00'),
                                ],
                            ],
                            'createdAt' => new \DateTime('2021-06-01 15:30:00'),
                            'updatedAt' => new \DateTime('2021-01-01 15:30:00'),
                        ],
                        [
                            'author' => Arrays::getRandom($this->users),
                            'contentHtml' => '<p>Totalement d\'accord avec votre analyse !</p>',
                            'createdAt' => new \DateTime('2021-06-01 15:45:00'),
                            'updatedAt' => new \DateTime('2021-06-01 15:45:00'),
                        ],
                    ],
                    'createdAt' => new \DateTime('2021-06-01 15:00:00'),
                    'updatedAt' => new \DateTime('2021-06-01 15:00:00'),
                ],
            ],
        ];

        // old topics
        $yaml = Yaml::parseFile(__DIR__ . '/topics.yaml');

        foreach ($yaml as $y) {
            $category = $this->categories[$y['category']] ?? null;
            if (null === $category) {
                continue;
            }

            $dataTopic = [
                'category' => $category,
                'title' => $y['title'],
                'pinned' => $y['pinned'],
                'author' => Arrays::getRandom($this->users),
                'posts' => [],
            ];

            // topic posts
            $i = 0;
            foreach ($y['posts'] as $yPost) {
                $author = 1 === ++$i ? $dataTopic['author'] : Arrays::getRandom($this->users);

                $dataPost = [
                    'author' => $author,
                    'contentHtml' => Strings::bbCodeToHtml($yPost['content']),
                    'createdAt' => (new \DateTime())->setTimestamp($yPost['createdAt']),
                    'updatedAt' => (new \DateTime())->setTimestamp($yPost['createdAt']),
                ];

                // post reports
                if (0 === mt_rand(0, 50)) {
                    $reportsCount = mt_rand(1, 3);
                    $dataPost['reports'] = [];
                    for ($j = 0; $j < $reportsCount; ++$j) {
                        $dataPost['reports'][] = [
                            'user' => Arrays::getRandom($this->users),
                            'content' => mt_rand(0, 1) ? $faker->sentences(mt_rand(1, 3), true) : null,
                        ];
                    }
                }

                // post upvotes
                if (0 === mt_rand(0, 10)) {
                    $dataPost['upvotes'] = Arrays::getRandomSubarray($this->users, 1, 32);
                }

                $dataTopic['posts'][] = $dataPost;
            }

            // topic traces

            $data[] = $dataTopic;
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'category' => $this->categories['Category 1.1'],
                'title' => 'Category 1.1 - Topic 1',
                'pinned' => false,
                'author' => $this->users['claude.monet@free-work.fr'],
                'metaTitle' => 'Forum - Topic 1 // Meta title',
                'metaDescription' => 'Blog - Topic 1 // Meta description',
                'posts' => [
                    [
                        'author' => $this->users['claude.monet@free-work.fr'],
                        'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Lorem</p>',
                        'createdAt' => new \DateTime('2021-01-01 12:00:00'),
                        'updatedAt' => new \DateTime('2021-01-01 12:00:00'),
                    ],
                    [
                        'author' => $this->users['vincent.van-gogh@free-work.fr'],
                        'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 2</p>',
                        'createdAt' => new \DateTime('2021-01-01 13:00:00'),
                        'updatedAt' => new \DateTime('2021-01-01 13:00:00'),
                        'reports' => [
                            [
                                'user' => $this->users['claude.monet@free-work.fr'],
                                'content' => null,
                            ],
                            [
                                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                                'content' => 'Report content from VVG on post',
                            ],
                        ],
                        'upvotes' => [
                            $this->users['claude.monet@free-work.fr'],
                            $this->users['henri.matisse@free-work.fr'],
                        ],
                    ],
                ],
                'traces' => [
                    [
                        'user' => $this->users['claude.monet@free-work.fr'],
                        'readAt' => new \DateTime('2021-01-01 13:30:00'),
                        'ip' => '1.2.3.4',
                    ],
                    [
                        'user' => $this->users['claude.monet@free-work.fr'],
                        'readAt' => new \DateTime('2021-01-01 13:35:00'),
                        'ip' => '1.2.3.4',
                    ],
                    [
                        'user' => $this->users['claude.monet@free-work.fr'],
                        'readAt' => new \DateTime('2021-01-01 13:40:00'),
                        'ip' => '1.2.3.4',
                    ],
                    [
                        'user' => $this->users['vincent.van-gogh@free-work.fr'],
                        'readAt' => new \DateTime('2021-01-01 10:00:00'),
                        'ip' => '1.2.3.4',
                    ],
                ],
            ],
            [
                'category' => $this->categories['Category 1.1'],
                'title' => 'Category 1.1 - Topic 2',
                'pinned' => false,
                'author' => $this->users['vincent.van-gogh@free-work.fr'],
                'metaTitle' => 'Forum - Topic 2 // Meta title',
                'metaDescription' => 'Blog - Topic 2 // Meta description',
                'posts' => [
                    [
                        'author' => $this->users['vincent.van-gogh@free-work.fr'],
                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 1</p>',
                        'createdAt' => new \DateTime('2021-01-10 19:00:00'),
                        'updatedAt' => new \DateTime('2021-01-10 19:00:00'),
                    ],
                    [
                        'author' => $this->users['auguste.renoir@free-work.fr'],
                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2 - Lorem</p>',
                        'children' => [
                            [
                                'author' => $this->users['vincent.van-gogh@free-work.fr'],
                                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.1</p>',
                                'createdAt' => new \DateTime('2021-01-10 19:45:00'),
                                'updatedAt' => new \DateTime('2021-01-10 19:45:00'),
                                'reports' => [
                                    [
                                        'user' => $this->users['pablo.picasso@free-work.fr'],
                                        'content' => null,
                                    ],
                                ],
                                'upvotes' => [
                                    $this->users['pablo.picasso@free-work.fr'],
                                ],
                            ],
                            [
                                'author' => $this->users['auguste.renoir@free-work.fr'],
                                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2</p>',
                                'children' => [
                                    [
                                        'author' => $this->users['vincent.van-gogh@free-work.fr'],
                                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                                        'createdAt' => (new \DateTime())->setTime(0, 30),
                                        'updatedAt' => (new \DateTime())->setTime(0, 30),
                                    ],
                                ],
                                'createdAt' => new \DateTime('2021-01-10 20:30:00'),
                                'updatedAt' => new \DateTime('2021-01-10 20:30:00'),
                            ],
                            [
                                'author' => $this->users['vincent.van-gogh@free-work.fr'],
                                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.3</p>',
                                'createdAt' => (new \DateTime())->setTime(0, 25),
                                'updatedAt' => (new \DateTime())->setTime(0, 25),
                            ],
                        ],
                        'createdAt' => new \DateTime('2021-01-10 19:30:00'),
                        'updatedAt' => new \DateTime('2021-01-10 19:30:00'),
                    ],
                    [
                        'author' => $this->users['claude.monet@free-work.fr'],
                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 3</p>',
                        'createdAt' => new \DateTime('2021-01-10 20:30:00'),
                        'updatedAt' => new \DateTime('2021-01-10 20:30:00'),
                    ],
                ],
                'traces' => [
                    [
                        'user' => $this->users['claude.monet@free-work.fr'],
                        'readAt' => new \DateTime('2021-01-10 22:00:00'),
                        'ip' => '1.2.3.4',
                    ],
                ],
            ],
            [
                'category' => $this->categories['Category 1.1'],
                'title' => 'Category 1.1 - Topic 3',
                'pinned' => true,
                'author' => $this->users['vincent.van-gogh@free-work.fr'],
                'posts' => [
                    [
                        'author' => $this->users['vincent.van-gogh@free-work.fr'],
                        'contentHtml' => '<p>Category 1.1 - Topic 3 - Post 1</p>',
                        'createdAt' => new \DateTime('2021-01-09 19:00:00'),
                        'updatedAt' => new \DateTime('2021-01-09 19:00:00'),
                    ],
                ],
            ],
            [
                'category' => $this->categories['Category 1.2'],
                'title' => 'Category 1.2 - Topic 1',
                'pinned' => false,
                'author' => $this->users['claude.monet@free-work.fr'],
                'posts' => [
                    ['author' => $this->users['claude.monet@free-work.fr'],
                        'contentHtml' => '<p>Category 1.2 - Topic 1 - Post 1</p>',
                        'createdAt' => new \DateTime('2021-01-03 12:00:00'),
                        'updatedAt' => new \DateTime('2021-01-03 12:00:00'), ],
                ],
                'traces' => [
                    [
                        'user' => $this->users['pablo.picasso@free-work.fr'],
                        'readAt' => new \DateTime('2021-01-10 22:00:00'),
                        'ip' => '1.2.3.4',
                    ],
                    [
                        'user' => $this->users['pablo.picasso@free-work.fr'],
                        'readAt' => new \DateTime('2021-01-10 23:00:00'),
                        'ip' => '1.2.3.4',
                    ],
                ],
            ],
            [
                'category' => $this->categories['Category 2.1'],
                'title' => 'Category 2.1 - Topic 1',
                'pinned' => false,
                'author' => $this->users['auguste.renoir@free-work.fr'],
                'posts' => [
                    [
                        'author' => $this->users['auguste.renoir@free-work.fr'],
                        'contentHtml' => '<p>Category 2.1 - Topic 1 - Post 1</p>',
                        'createdAt' => new \DateTime('2021-01-04 08:00:00'),
                        'updatedAt' => new \DateTime('2021-01-04 08:00:00'),
                    ],
                    [
                        'author' => $this->users['claude.monet@free-work.fr'],
                        'contentHtml' => '<p>Category 2.1 - Topic 1 - Post 2</p>',
                        'createdAt' => new \DateTime('2021-01-04 08:30:00'),
                        'updatedAt' => new \DateTime('2021-01-04 08:30:00'),
                    ],
                ],
            ],
            [
                'category' => $this->categories['Category 3.1'],
                'title' => 'Category 3.1 - Topic 1',
                'pinned' => false,
                'author' => $this->users['auguste.renoir@free-work.fr'],
                'posts' => [
                    [
                        'author' => $this->users['claude.monet@free-work.fr'],
                        'contentHtml' => '<p>Category 3.1 - Topic 1 - Post 1 - Moderated</p>',
                        'createdAt' => new \DateTime('2021-01-04 08:25:00'),
                        'updatedAt' => new \DateTime('2021-01-04 08:25:00'),
                        'moderatedAt' => new \DateTime('2021-01-04 08:35:00'),
                    ],
                    [
                        'author' => $this->users['claude.monet@free-work.fr'],
                        'contentHtml' => '<p>Category 3.1 - Topic 1 - Post 2- Deleted (hidden)</p>',
                        'createdAt' => new \DateTime('2021-01-06 08:30:00'),
                        'updatedAt' => new \DateTime('2021-01-06 08:30:00'),
                        'deletedAt' => new \DateTime('2021-01-06 08:40:00'),
                    ],
                    [
                        'author' => $this->users['claude.monet@free-work.fr'],
                        'contentHtml' => '<p>Category 3.1 - Topic 1 - Post 3 - Moderated and deleted (hidden)</p>',
                        'createdAt' => new \DateTime('2021-01-07 08:30:00'),
                        'updatedAt' => new \DateTime('2021-01-07 08:30:00'),
                        'moderatedAt' => new \DateTime('2021-01-07 08:35:00'),
                        'deletedAt' => new \DateTime('2021-01-07 08:40:00'),
                    ],
                ],
            ],
            [
                'category' => $this->categories['Category 4.1'],
                'title' => 'Category 4.1 - Topic 1',
                'pinned' => false,
                'author' => $this->users['henri.matisse@free-work.fr'],
                'posts' => [
                    [
                        'author' => $this->users['henri.matisse@free-work.fr'],
                        'contentHtml' => '<p>Category 4.1 - Topic 1 - Post 1</p>',
                        'createdAt' => new \DateTime('2021-01-10 08:30:00'),
                        'updatedAt' => new \DateTime('2021-01-10 08:30:00'),
                        'reports' => [
                            [
                                'user' => $this->users['claude.monet@free-work.fr'],
                                'content' => null,
                            ],
                        ],
                        'upvotes' => [
                            $this->users['claude.monet@free-work.fr'],
                        ],
                    ],
                    [
                        'author' => $this->users['claude.monet@free-work.fr'],
                        'contentHtml' => '<p>Category 4.1 - Topic 1 - Post 2</p>',
                        'createdAt' => (new \DateTime())->setTime(0, 20),
                        'updatedAt' => (new \DateTime())->setTime(0, 20),
                    ],
                ],
                'traces' => [
                    [
                        'user' => $this->users['claude.monet@free-work.fr'],
                        'readAt' => new \DateTime('2021-01-02 13:30:00'),
                        'ip' => '1.2.3.4',
                    ],
                ],
            ],
            [
                'category' => $this->categories['Category 7.1'],
                'title' => 'Category 5.1 - Topic 1 - Lorem',
                'pinned' => false,
                'author' => $this->users['henri.matisse@free-work.fr'],
                'posts' => [
                    [
                        'author' => $this->users['claude.monet@free-work.fr'],
                        'contentHtml' => '<p>Category 5.1 - Topic 1 - Post 1</p>',
                        'createdAt' => new \DateTime('2021-01-10 11:00:00'),
                        'updatedAt' => new \DateTime('2021-01-10 11:00:00'),
                    ],
                    [
                        'author' => $this->users['vincent.van-gogh@free-work.fr'],
                        'contentHtml' => '<p>Category 5.1 - Topic 1 - Post 2 - Deleted</p>',
                        'createdAt' => (new \DateTime())->setTime(0, 5),
                        'updatedAt' => (new \DateTime())->setTime(0, 5),
                        'deletedAt' => (new \DateTime())->setTime(0, 5),
                        'children' => [
                            [
                                'author' => $this->users['elisabeth.vigee-le-brun@free-work.fr'],
                                'contentHtml' => '<p>Category 5.1 - Topic 1 - Post 2.1</p>',
                                'createdAt' => (new \DateTime())->setTime(0, 10),
                                'updatedAt' => (new \DateTime())->setTime(0, 10),
                            ],
                            [
                                'author' => $this->users['elisabeth.vigee-le-brun@free-work.fr'],
                                'contentHtml' => '<p>Category 5.1 - Topic 1 - Post 2 - Deleted and hidden</p>',
                                'createdAt' => (new \DateTime())->setTime(0, 15),
                                'updatedAt' => (new \DateTime())->setTime(0, 15),
                                'deletedAt' => (new \DateTime())->setTime(0, 15),
                            ],
                        ],
                        'upvotes' => [
                            $this->users['vincent.van-gogh@free-work.fr'],
                        ],
                    ],
                ],
                'traces' => [
                    [
                        'user' => $this->users['claude.monet@free-work.fr'],
                        'readAt' => new \DateTime('2021-01-02 13:30:00'),
                        'ip' => '1.2.3.4',
                    ],
                ],
            ],
            [
                'category' => $this->categories['Category 1.1'],
                'title' => 'Category 1.1 - Topic 4',
                'pinned' => false,
                'author' => $this->users['admin@free-work.fr'],
                'posts' => [
                    [
                        'author' => $this->users['admin@free-work.fr'],
                        'contentHtml' => '<p>Category 1.1 - Topic 4 - Post 1</p>',
                        'createdAt' => new \DateTime('2021-01-09 21:00:00'),
                        'updatedAt' => new \DateTime('2021-01-09 21:00:00'),
                    ],
                ],
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            ForumCategoriesFixtures::class,
            UsersFixtures::class,
        ];
    }
}
