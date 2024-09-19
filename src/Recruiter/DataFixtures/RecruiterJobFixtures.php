<?php

namespace App\Recruiter\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Strings;
use App\Recruiter\Entity\RecruiterJob;
use Doctrine\Persistence\ObjectManager;

class RecruiterJobFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        // process data
        foreach ($this->getData() as $data) {
            $recruiterJob = (new RecruiterJob())
                ->setName($data)
            ;
            $manager->persist($recruiterJob);
        }

        $manager->flush();
    }

    public function getData(): array
    {
        $data = [];
        if (($handle = fopen(__DIR__ . '/data/recruiter_jobs.csv', 'r')) !== false) {
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                $data[] = Strings::jobCase($d[0]);
            }
            fclose($handle);
        }

        return $data;
    }
}
