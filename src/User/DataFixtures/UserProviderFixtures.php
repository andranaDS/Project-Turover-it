<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\User\Entity\User;
use App\User\Entity\UserProvider;
use App\User\Enum\Provider;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\ByteString;

class UserProviderFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            if ($user->getProfileJobTitle()) {
                $this->users[$user->getEmail()] = $user;
            }
        }

        foreach ($this->getData() as $d) {
            $oAuthConnect = (new UserProvider())
                ->setUser($d['user'])
                ->setEmail($d['email'])
                ->setAccessToken(ByteString::fromRandom(255))
                ->setProvider($d['provider'])
                ->setProviderUserId(ByteString::fromRandom(255))
                ->setUpdatedAt($d['updatedAt'])
            ;
            $manager->persist($oAuthConnect);
        }

        $manager->flush();
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'email' => 'claude.monet-linkedin@free-work.fr',
                'provider' => Provider::LINKEDIN,
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'email' => 'claude.monet-google@free-work.fr',
                'provider' => Provider::GOOGLE,
                'updatedAt' => new \DateTime('2020-01-01 12:00:00'),
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'email' => 'vincent.van-gogh-google@free-work.fr',
                'provider' => Provider::GOOGLE,
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
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
