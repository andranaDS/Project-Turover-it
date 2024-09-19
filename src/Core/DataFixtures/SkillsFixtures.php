<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\Skill;
use Doctrine\Persistence\ObjectManager;

class SkillsFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $data = $this->getData();

        foreach ($data as $d) {
            $skill = (new Skill())
                ->setName($d['name'])
                ->setJobUsageCount($d['countJobUsage'])
                ->setDisplayed($d['displayed'])
                ->setSynonymSlugs($d['synonymSlugs'])
            ;

            $manager->persist($skill);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        if (($handle = fopen(__DIR__ . '/data/skills.csv', 'r')) !== false) {
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                $data[] = [
                    'name' => $d[0],
                    'countJobUsage' => random_int(100, 3000),
                    'displayed' => (bool) random_int(0, 1),
                    'synonymSlugs' => [],
                ];
            }
            fclose($handle);
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'name' => 'php',
                'countJobUsage' => 3000,
                'displayed' => true,
                'synonymSlugs' => ['php-5', 'php-7', 'php-8'],
            ],
            [
                'name' => 'java',
                'countJobUsage' => 7100,
                'displayed' => true,
                'synonymSlugs' => [],
            ],
            [
                'name' => 'javascript',
                'countJobUsage' => 2150,
                'displayed' => true,
                'synonymSlugs' => [],
            ],
            [
                'name' => 'symfony',
                'countJobUsage' => 530,
                'displayed' => true,
                'synonymSlugs' => [],
            ],
            [
                'name' => 'api platform',
                'countJobUsage' => 310,
                'displayed' => true,
                'synonymSlugs' => [],
            ],
            [
                'name' => 'laravel',
                'countJobUsage' => 11,
                'displayed' => true,
                'synonymSlugs' => [],
            ],
            [
                'name' => 'docker',
                'countJobUsage' => 542,
                'displayed' => true,
                'synonymSlugs' => [],
            ],
            [
                'name' => 'J2EE',
                'countJobUsage' => 991,
                'displayed' => true,
                'synonymSlugs' => [],
            ],
            [
                'name' => 'flutter',
                'countJobUsage' => 103,
                'displayed' => true,
                'synonymSlugs' => [],
            ],
            [
                'name' => 'assembly',
                'countJobUsage' => 1,
                'displayed' => false,
                'synonymSlugs' => [],
            ],
        ];
    }
}
