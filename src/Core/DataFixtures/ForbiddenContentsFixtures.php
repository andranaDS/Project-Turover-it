<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\ForbiddenContent;
use Doctrine\Persistence\ObjectManager;

class ForbiddenContentsFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $data = $this->getData();

        foreach ($data as $d) {
            $ForbiddenContent = (new ForbiddenContent())
                ->setText($d['text'])
            ;
            $manager->persist($ForbiddenContent);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        if (($handle = fopen(__DIR__ . '/data/forbidden_contents.csv', 'r')) !== false) {
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                $data[] = [
                    'text' => $d[0],
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
                'text' => 'Forbidden content 1',
            ],
            [
                'text' => 'Forbidden content 2',
            ],
            [
                'text' => 'Forbidden content 3',
            ],
        ];
    }
}
