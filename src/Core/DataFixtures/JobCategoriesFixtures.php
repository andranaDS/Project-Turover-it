<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\JobCategory;
use App\Core\Util\Arrays;
use App\Core\Util\Files;
use Doctrine\Persistence\ObjectManager;

class JobCategoriesFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $d) {
            $category = (new JobCategory())
                ->setName($d)
            ;
            $manager->persist($category);
        }

        $manager->flush();
    }

    public function getData(): array
    {
        $data = array_unique(array_filter(Arrays::map(Files::getCsvData(__DIR__ . '/data/jobs.csv', true), static function (array $d) {
            return $d[1];
        })));
        sort($data);

        return $data;
    }
}
