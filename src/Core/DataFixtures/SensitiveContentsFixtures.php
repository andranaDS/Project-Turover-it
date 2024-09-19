<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\SensitiveContent;
use Doctrine\Persistence\ObjectManager;

class SensitiveContentsFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $data = $this->getData();

        foreach ($data as $d) {
            $sensitiveContent = (new SensitiveContent())
                ->setText($d['text'])
            ;
            $manager->persist($sensitiveContent);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        if (($handle = fopen(__DIR__ . '/data/sensitive_contents.csv', 'r')) !== false) {
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
                'text' => 'Sensitive content 1',
            ],
            [
                'text' => 'Sensitive content 2',
            ],
            [
                'text' => 'Sensitive content 3',
            ],
        ];
    }
}
