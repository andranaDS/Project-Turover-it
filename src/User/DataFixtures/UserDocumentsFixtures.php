<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\Core\Util\Files;
use App\User\Entity\User;
use App\User\Entity\UserDocument;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Gaufrette\Filesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;

class UserDocumentsFixtures extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private array $users = [];
    private array $files = [];
    private Filesystem $filesystem;

    public function __construct(string $env, FilesystemMap $filesystemMap)
    {
        parent::__construct($env);
        $this->filesystem = $filesystemMap->get('user_document_file_fs');
    }

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            if ($user->getProfileJobTitle()) {
                $this->users[$user->getEmail()] = $user;
            }
        }

        // fetch files
        $this->files = [
            '1' => __DIR__ . '/files/user-document-1.docx',
            '2' => __DIR__ . '/files/user-document-2.docx',
            '3' => __DIR__ . '/files/user-document-3.pdf',
        ];

        // process data
        foreach ($this->getData() as $d) {
            $document = (new UserDocument())
                ->setUser($d['user'])
                ->setOriginalName($d['originalName'])
                ->setResume($d['resume'])
                ->setDefaultResume($d['defaultResume'])
                ->setCreatedAt($d['createdAt'])
                ->setUpdatedAt($d['updatedAt'])
            ;

            // file
            if (null !== ($d['documentFile'] ?? null)) {
                $document->setDocumentFile(Files::getUploadedFile($d['documentFile']));
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
                $document->setDocument($fileBasename);
            }

            $manager->persist($document);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        $faker = Faker::create('fr_FR');

        foreach ($this->users as $user) {
            $documentCount = mt_rand(1, 3);
            $resume = mt_rand(0, 1);
            $countDefault = 0;

            if (mt_rand(0, 1)) {
                for ($i = 0; $i < $documentCount; ++$i) {
                    $defaultResume = $resume && 0 === $countDefault;
                    $createdAt = $faker->dateTimeBetween('- 6 months', '- 1 month');
                    $file = Arrays::getRandom($this->files);

                    $data[] = [
                        'user' => $user,
                        'originalName' => substr($file, strrpos($file, '/') + 1),
                        'documentFile' => $file,
                        'resume' => $resume,
                        'defaultResume' => $defaultResume,
                        'createdAt' => $createdAt,
                        'updatedAt' => $faker->dateTimeBetween($createdAt),
                    ];
                    if ($defaultResume) {
                        ++$countDefault;
                    }
                }
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'originalName' => 'user-document-1.docx',
                'file' => [
                    'path' => $this->files['1'],
                    'basename' => 'document1-cm.docx',
                ],
                'resume' => true,
                'defaultResume' => true,
                'createdAt' => new \DateTime('2021-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 20:00:00'),
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'originalName' => 'user-document-2.docx',
                'file' => [
                    'path' => $this->files['2'],
                    'basename' => 'document2-cm.docx',
                ],
                'resume' => true,
                'defaultResume' => false,
                'createdAt' => new \DateTime('2021-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-02 20:00:00'),
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'originalName' => 'user-document-3.pdf',
                'file' => [
                    'path' => $this->files['3'],
                    'basename' => 'document3-cm.pdf',
                ],
                'resume' => false,
                'defaultResume' => false,
                'createdAt' => new \DateTime('2021-01-03 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-03 20:00:00'),
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'originalName' => 'user-document-3.pdf',
                'file' => [
                    'path' => $this->files['3'],
                    'basename' => 'document3-vvg.pdf',
                ],
                'resume' => true,
                'defaultResume' => true,
                'createdAt' => new \DateTime('2021-01-04 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-04 20:00:00'),
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}
