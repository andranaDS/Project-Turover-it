<?php

namespace App\Folder\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\Folder\Entity\Folder;
use App\Folder\Entity\FolderUser;
use App\Folder\Enum\FolderType;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FolderUserFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users;
    private array $folders;

    public function load(ObjectManager $manager): void
    {
        // fetch folders
        foreach ($manager->getRepository(Folder::class)->findAll() as $folder) {
            /** @var Folder $folder */
            if (null === $recruiter = $folder->getRecruiter()) {
                continue;
            }
            $this->folders[$recruiter->getEmail()][$folder->getType() . (FolderType::PERSONAL === $folder->getType() ? '-' . $folder->getId() : '')] = $folder;
        }

        // fetch users
        $this->users = $manager->getRepository(User::class)->findAll();

        // process data
        foreach ($this->getData() as $d) {
            foreach ($d['users'] as $user) {
                $folderUser = (new FolderUser())
                    ->setFolder($d['folder'])
                    ->setUser($user)
                ;
                $manager->persist($folderUser);
            }
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        foreach ($this->folders as $recruiterFolders) {
            foreach ($recruiterFolders as $folder) {
                $data[] = [
                    'folder' => $folder,
                    'users' => Arrays::getRandomSubarray($this->users, 0, 5),
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'folder' => $this->folders['walter.white@breaking-bad.com']['favorites'],
                'users' => [
                    $this->users[1],
                    $this->users[2],
                    $this->users[3],
                ],
            ],
            [
                'folder' => $this->folders['walter.white@breaking-bad.com']['commented'],
                'users' => [
                    $this->users[4],
                    $this->users[5],
                ],
            ],
            [
                'folder' => $this->folders['walter.white@breaking-bad.com']['emailing'],
                'users' => [
                    $this->users[6],
                    $this->users[7],
                    $this->users[8],
                    $this->users[9],
                ],
            ],
            [
                'folder' => $this->folders['walter.white@breaking-bad.com']['hidden'],
                'users' => [
                    $this->users[10],
                ],
            ],
            [
                'folder' => $this->folders['walter.white@breaking-bad.com']['viewed'],
                'users' => [
                    $this->users[15],
                    $this->users[16],
                    $this->users[17],
                    $this->users[18],
                ],
            ],
            [
                'folder' => $this->folders['walter.white@breaking-bad.com']['cart'],
                'users' => [
                    $this->users[19],
                    $this->users[20],
                    $this->users[21],
                ],
            ],
            [
                'folder' => $this->folders['walter.white@breaking-bad.com']['yesterday_cart'],
                'users' => [
                    $this->users[23],
                    $this->users[24],
                ],
            ],
            [
                'folder' => $this->folders['jesse.pinkman@breaking-bad.com']['favorites'],
                'users' => [
                    $this->users[11],
                    $this->users[12],
                    $this->users[13],
                ],
            ],
            [
                'folder' => $this->folders['arya.stark@got.com']['personal-122'],
                'users' => [
                    $this->users[14],
                ],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            FolderFixtures::class,
            UsersFixtures::class,
            RecruiterFixtures::class,
        ];
    }
}
