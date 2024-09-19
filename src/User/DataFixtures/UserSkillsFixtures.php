<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\SkillsFixtures;
use App\Core\Entity\Skill;
use App\Core\Util\Arrays;
use App\User\Entity\User;
use App\User\Entity\UserSkill;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserSkillsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];
    private array $skills = [];

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            if ($user->getProfileJobTitle()) {
                $this->users[$user->getEmail()] = $user;
            }
        }

        // fetch skills
        foreach ($manager->getRepository(Skill::class)->findSome() as $skill) {
            /* @var Skill $skill */
            $this->skills[$skill->getId()] = $skill;
        }

        foreach ($this->getData() as $d) {
            $skill = (new UserSkill())
                ->setUser($d['user'])
                ->setSkill($d['skill'])
                ->setMain($d['main'])
            ;
            $manager->persist($skill);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        foreach ($this->users as $user) {
            $skillsCount = random_int(6, 16);
            $skillsMainCount = random_int(0, 5);
            for ($i = 0; $i < $skillsCount; ++$i) {
                $data[] = [
                    'user' => $user,
                    'skill' => Arrays::getRandom($this->skills),
                    'main' => $i < $skillsMainCount,
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'skill' => $this->skills[1],
                'main' => true,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'skill' => $this->skills[2],
                'main' => true,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'skill' => $this->skills[3],
                'main' => false,
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'skill' => $this->skills[1],
                'main' => true,
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
            SkillsFixtures::class,
            UsersFixtures::class,
        ];
    }
}
