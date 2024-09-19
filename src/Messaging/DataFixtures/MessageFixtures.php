<?php

namespace App\Messaging\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\Core\Util\Files;
use App\Core\Util\HtmlGenerator;
use App\Messaging\Entity\Feed;
use App\Messaging\Entity\FeedUser;
use App\Messaging\Entity\Message;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Gaufrette\Filesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use ProseMirror\ProseMirror;

class MessageFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];
    private array $feeds = [];
    private array $files = [];
    private Filesystem $filesystem;

    public function __construct(string $env, FilesystemMap $filesystemMap)
    {
        parent::__construct($env);
        $this->filesystem = $filesystemMap->get('message_file_fs');
    }

    public function load(ObjectManager $manager): void
    {
        // fetch all users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // fetch all feeds
        foreach ($manager->getRepository(Feed::class)->findAll() as $feed) {
            /* @var Feed $feed */
            $this->feeds[$feed->getId()] = $feed;
        }

        // fetch files
        $this->files = [
            '1' => __DIR__ . '/files/message-1.jpg',
            '2' => __DIR__ . '/files/message-2.jpg',
            '3' => __DIR__ . '/files/message-3.txt',
        ];

        foreach ($this->getData() as $d) {
            try {
                $contentJson = Json::encode(ProseMirror::htmlToJson($d['contentHtml']));
            } catch (JsonException $exception) {
                continue;
            }

            $message = (new Message())
                ->setContentHtml($d['contentHtml'])
                ->setContentJson($contentJson)
                ->setAuthor($d['author'])
                ->setFeed($d['feed'])
                ->setCreatedAt($d['createdAt'])
                ->setDocumentOriginalName($d['documentOriginalName'] ?? null)
            ;

            // file
            if (null !== ($d['documentFile'] ?? null)) {
                $message->setDocumentFile(Files::getUploadedFile($d['documentFile']));
            } elseif (null !== ($d['file'] ?? null)) {
                $filePath = $d['file']['path'] ?? null;
                if (null === $filePath) {
                    throw new \InvalidArgumentException();
                }
                $fileBasename = $d['file']['basename'] ?? null;
                if (null === $fileBasename) {
                    throw new \InvalidArgumentException();
                }
                if (false === $fileContent = file_get_contents($filePath)) {
                    throw new \InvalidArgumentException();
                }

                $this->filesystem->write($fileBasename, $fileContent, true);
                $message->setDocument($fileBasename);
            }

            $manager->persist($message);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        $messageCount = mt_rand(5, 10);
        $faker = Faker::create('fr_FR');

        foreach ($this->feeds as $feed) {
            /* @var Feed $feed */
            $feedUsers = $feed->getFeedUsers()->getValues();
            $users = array_map(static function (FeedUser $feedUser) {
                return $feedUser->getUser();
            }, $feedUsers);

            for ($i = 0; $i <= $messageCount; ++$i) {
                $hasDocument = 0 === mt_rand(0, 10);
                $document = $hasDocument ? Arrays::getRandom($this->files) : null;

                $data[] = [
                    'documentFile' => $document,
                    'documentOriginalName' => $hasDocument ? substr($document, strrpos($document, '/') + 1) : null,
                    'contentHtml' => HtmlGenerator::generate(),
                    'author' => Arrays::getRandom($users),
                    'feed' => $feed,
                    'createdAt' => $faker->dateTimeBetween('- 6 months', '- 1 month'),
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'documentFile' => $this->files[1],
                'documentOriginalName' => 'message-1.jpg',
                'contentHtml' => '<p>Feed 1 - Message 1 - Content</p>',
                'author' => $this->users['claude.monet@free-work.fr'],
                'feed' => $this->feeds[1],
                'createdAt' => new \DateTime('2021-01-10 20:00:00'),
            ],
            [
                'contentHtml' => '<p>Feed 1 - Message 2 - Content</p>',
                'author' => $this->users['claude.monet@free-work.fr'],
                'feed' => $this->feeds[1],
                'createdAt' => new \DateTime('2021-01-10 21:00:00'),
            ],
            [
                'contentHtml' => '<p>Feed 1 - Message 3 - Content New</p>',
                'author' => $this->users['vincent.van-gogh@free-work.fr'],
                'feed' => $this->feeds[1],
                'createdAt' => new \DateTime('2021-02-10 20:00:00'),
            ],

            [
                'contentHtml' => '<p>Feed 2 - Message 1 - Content</p>',
                'author' => $this->users['claude.monet@free-work.fr'],
                'feed' => $this->feeds[2],
                'createdAt' => new \DateTime('2021-01-10 10:00:00'),
            ],
            [
                'contentHtml' => '<p>Feed 2 - Message 2 - Content</p>',
                'author' => $this->users['auguste.renoir@free-work.fr'],
                'feed' => $this->feeds[2],
                'createdAt' => new \DateTime('2021-01-10 11:00:00'),
            ],
            [
                'documentFile' => $this->files[2],
                'documentOriginalName' => 'message-2.jpg',
                'contentHtml' => '<p>Feed 2 - Message 3 - Content Not New</p>',
                'author' => $this->users['auguste.renoir@free-work.fr'],
                'feed' => $this->feeds[2],
                'createdAt' => new \DateTime('2021-01-10 17:00:00'),
            ],
            [
                'documentFile' => $this->files[1],
                'documentOriginalName' => 'message-1.jpg',
                'contentHtml' => '<p>Feed 3 - Message 1</p>',
                'author' => $this->users['claude.monet@free-work.fr'],
                'feed' => $this->feeds[3],
                'createdAt' => new \DateTime('2021-01-10 21:00:00'),
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            FeedFixtures::class,
            UsersFixtures::class,
        ];
    }
}
