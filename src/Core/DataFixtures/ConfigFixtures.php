<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\Config;
use Doctrine\Persistence\ObjectManager;

class ConfigFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $data = $this->getData();

        foreach ($data as $d) {
            $config = (new Config())
                ->setName($d['name'])
                ->setValue($d['value'])
            ;
            $manager->persist($config);
        }

        $manager->flush();
    }

    public function getData(): array
    {
        return [
            [
                'name' => 'turnover_it_recruiters_count',
                'value' => 1000,
            ],
            [
                'name' => 'sync_execute_companies_last_datetime',
                'value' => null,
            ],
            [
                'name' => 'sync_execute_job_postings_last_datetime',
                'value' => null,
            ],
            [
                'name' => 'sync_applications_last_datetime',
                'value' => null,
            ],
            [
                'name' => 'emails_authorized',
                'value' => $this->getEmailsAuthorized(),
            ],
        ];
    }

    private function getEmailsAuthorized(): string
    {
        $emailsRegex = [
            'lx.vignal([a-zA-Z0-9+])*@gmail.com',
            'haj.randria([a-zA-Z0-9+])*@gmail.com',
            '@agsi-net.com$',
            '@free-work.fr$',
            '^jmleglise',
            'jndl([a-zA-Z0-9+])*@gmail.com',
            'jacques.delamballerie([a-zA-Z0-9+])*@gmail.com',
            'jb.chateaux([a-zA-Z0-9+])*@gmail.com',
            'kasoutami([a-zA-Z0-9+])*@gmail.com',
            'laurent.fouks([a-zA-Z0-9+])*@gmail.com',
        ];

        return json_encode($emailsRegex, \JSON_THROW_ON_ERROR);
    }
}
