<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\SoftSkill;
use Doctrine\Persistence\ObjectManager;

class SoftSkillsFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $data = $this->getData();

        foreach ($data as $d) {
            $softSkill = (new SoftSkill())
                ->setName($d['name'])
            ;

            $manager->persist($softSkill);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        if (($handle = fopen(__DIR__ . '/data/soft_skills.csv', 'r')) !== false) {
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                $data[] = [
                    'name' => $d[0],
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
                'name' => 'SoftSkill 1',
            ],
            [
                'name' => 'SoftSkill 2',
            ],
            [
                'name' => 'SoftSkill 3',
            ],
            [
                'name' => 'SoftSkill 4',
            ],
        ];
    }
}
