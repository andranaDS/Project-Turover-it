<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\Alert;
use App\Core\Util\HtmlGenerator;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use ProseMirror\ProseMirror;

class AlertFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $data = $this->getData();

        foreach ($data as $d) {
            try {
                $contentJson = Json::encode(ProseMirror::htmlToJson($d['contentHtml']));
            } catch (JsonException $exception) {
                continue;
            }
            $alert = (new Alert())
                ->setContentHtml($d['contentHtml'])
                ->setContentJson($contentJson)
                ->setBlocking($d['blocking'])
                ->setStartAt($d['startAt'])
                ->setEndAt($d['endAt'])
            ;
            $manager->persist($alert);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        $alertCount = mt_rand(5, 10);
        $faker = Faker::create('fr_FR');

        $data[] = [
            'contentHtml' => HtmlGenerator::generate(),
            'blocking' => 0,
            'startAt' => $faker->dateTime(),
            'endAt' => $faker->dateTimeBetween('now', '+ 3 days'),
        ];

        for ($i = 0; $i <= $alertCount; ++$i) {
            $data[] = [
                'contentHtml' => HtmlGenerator::generate(),
                'blocking' => 0 === mt_rand(0, 3),
                'startAt' => $faker->dateTimeBetween('- 6 months', '- 3 month'),
                'endAt' => $faker->dateTimeBetween('- 3 months', '- 1 month'),
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'contentHtml' => '<p>Alert 1 - Content</p>',
                'blocking' => false,
                'startAt' => new \DateTime('2021-01-10 21:00:00'),
                'endAt' => new \DateTime('2021-01-20 21:00:00'),
            ],
            [
                'contentHtml' => '<p>Alert 2 - Content</p>',
                'blocking' => true,
                'startAt' => new \DateTime('2021-02-10 21:00:00'),
                'endAt' => new \DateTime('2021-02-20 21:00:00'),
            ],
            [
                'contentHtml' => '<p>Alert 3 - Content Active</p>',
                'blocking' => false,
                'startAt' => new \DateTime('2021-11-10 21:00:00'),
                'endAt' => (new \DateTime())->modify('+1 day')->setTime(0, 0),
            ],
        ];
    }
}
