<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\Recruiter\Entity\Recruiter;
use App\User\Entity\User;
use App\User\Entity\UserTrace;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserTracesFixtures extends AbstractFixture implements DependentFixtureInterface
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
            $this->recruiters[$recruiter->getId()] = $recruiter;
        }

        foreach ($this->getData() as $d) {
            $userTrace = (new UserTrace())
                ->setRecruiter($d['recruiter'])
                ->setUser($d['user'] ?? null)
                ->setIp($d['ip'])
                ->setViewedAt($d['viewedAt'])
            ;

            $manager->persist($userTrace);
        }

        $manager->flush();
    }

    /**
     * get data in development mode.
     *
     * @throws \Exception
     */
    public function getDevData(): array
    {
        $faker = Factory::create('fr_FR');
        $data = [];

        $tracescount = 50;

        for ($i = 0; $i < $tracescount; ++$i) {
            $data[] = [
                'user' => Arrays::getRandom($this->users),
                'recruiter' => Arrays::getRandom($this->recruiters),
                'ip' => $faker->ipv4(),
                'viewedAt' => $faker->dateTimeBetween('-1 year'),
            ];
        }

        return $data;
    }

    /**
     * get data in test mode.
     *
     * @return array[]
     */
    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'recruiter' => $this->recruiters[1],
                'ip' => '1.2.3.4',
                'viewedAt' => (new \DateTime())->modify('-15 days'),
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'recruiter' => $this->recruiters[2],
                'ip' => '1.2.3.5',
                'viewedAt' => new \DateTime('2022-01-22 15:30:00'),
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'recruiter' => $this->recruiters[3],
                'ip' => '1.2.3.6',
                'viewedAt' => new \DateTime('2022-01-20 14:00:00'),
            ],
        ];
    }

    /**
     * Dependances du fixture user trace.
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
            RecruiterFixtures::class,
        ];
    }
}
