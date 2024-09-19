<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\User\Entity\User;
use App\User\Entity\UserLead;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserLeadsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        foreach ($this->getData() as $d) {
            $userLead = (new UserLead())
                ->setUser($d['user'])
                ->setContent($d['content'])
                ->setIsSuccess($d['isSuccess'])
            ;
            $manager->persist($userLead);
        }

        $manager->flush();
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'content' => [],
                'isSuccess' => false,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'content' => [],
                'isSuccess' => true,
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'content' => [],
                'isSuccess' => false,
            ],
        ];
    }

    public static function getGroups(): array
    {
        return ['user'];
    }

    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
        ];
    }
}
