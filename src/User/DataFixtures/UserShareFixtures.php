<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\Recruiter\Entity\Recruiter;
use App\User\Entity\User;
use App\User\Entity\UserShare;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserShareFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];
    private array $recruiters = [];

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // fetch recruiters
        foreach ($manager->getRepository(Recruiter::class)->findAll() as $recruiter) {
            /* @var Recruiter $recruiter */
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
        }

        // process data
        foreach ($this->getData() as $d) {
            $share = (new UserShare())
                ->setUser($d['user'])
                ->setSharedBy($d['recruiter'])
                ->setEmail($d['recruiter']->getEmail())
            ;

            $manager->persist($share);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        $sharesCount = 10;
        for ($i = 0; $i < $sharesCount; ++$i) {
            $data[] = [
                'user' => Arrays::getRandom($this->users),
                'recruiter' => Arrays::getRandom($this->recruiters),
                'sharedBy' => Arrays::getRandom($this->recruiters),
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'sharedBy' => $this->recruiters['robb.stark@got.com'],
                'recruiter' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'sharedBy' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'sharedBy' => $this->recruiters['walter.white@breaking-bad.com'],
                'recruiter' => $this->recruiters['gustavo.fring@breaking-bad.com'],
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'sharedBy' => $this->recruiters['robb.stark@got.com'],
                'recruiter' => $this->recruiters['eddard.stark@got.com'],
            ],
            [
                'user' => $this->users['henri.matisse@free-work.fr'],
                'sharedBy' => $this->recruiters['walter.white@breaking-bad.com'],
                'recruiter' => $this->recruiters['robb.stark@got.com'],
            ],
            [
                'user' => $this->users['pablo.picasso@free-work.fr'],
                'sharedBy' => $this->recruiters['walter.white@breaking-bad.com'],
                'recruiter' => $this->recruiters['sansa.stark@got.com'],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
        ];
    }
}
