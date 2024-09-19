<?php

namespace App\Core\DataFixtures;

use Cron\CronBundle\Entity\CronJob;
use Doctrine\Persistence\ObjectManager;

class CronFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $data = $this->getData();

        foreach ($data as $d) {
            $config = (new CronJob())
                ->setName($d['name'])
                ->setCommand($d['command'])
                ->setSchedule($d['schedule'])
                ->setEnabled($d['enabled'])
                ->setDescription($d['description'])
            ;
            $manager->persist($config);
        }

        $manager->flush();
    }

    public function getData(): array
    {
        return [
            [
                'name' => 'Sync - Companies & JobPostings',
                'command' => 'app:sync:execute -n',
                'schedule' => '*/2 * * * *',
                'enabled' => false,
                'description' => 'Sync all Companies & JobPostings',
            ],
            [
                'name' => 'Sync - Unpublish deleted JobPostings',
                'command' => 'app:sync:unpublish -n',
                'schedule' => '*/2 * * * *',
                'enabled' => false,
                'description' => 'Unpublish deleted JobPostings',
            ],
            [
                'name' => 'Resource - Create trend of the last week',
                'command' => 'app:resource:trend -n',
                'schedule' => '1 0 * * 1',
                'enabled' => true,
                'description' => 'Creation of the trend of the last week',
            ],
            [
                'name' => 'Resource - Create jobs statistics of the day',
                'command' => 'app:resource:job-contribution-statistics -n',
                'schedule' => '3 0 * * *',
                'enabled' => true,
                'description' => 'Creation of the job statistics of the day',
            ],
            [
                'name' => 'User - Create user profile views of the day',
                'command' => 'app:user:user-profile-views:fetch -n',
                'schedule' => '7 0 * * *',
                'enabled' => true,
                'description' => 'Creation of user profile views of the day',
            ],
        ];
    }
}
