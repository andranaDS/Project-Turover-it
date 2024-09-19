<?php

namespace App\Company\DataFixtures;

use App\Company\Entity\CompanyBusinessActivity;
use App\Core\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class CompanyBusinessActivitiesFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        // process data
        foreach ($this->getData() as $data) {
            $activity = (new CompanyBusinessActivity())
                ->setName($data['name'])
            ;
            $manager->persist($activity);
        }
        $manager->flush();
    }

    public function getDevData(): array
    {
        return [
            [
                'name' => 'Agence WEB / Communication',
            ],
            [
                'name' => 'Cabinet de conseil',
            ],
            [
                'name' => 'Cabinet de recrutement / placement',
            ],
            [
                'name' => 'Commercial indépendant',
            ],
            [
                'name' => 'DSI / Client final',
            ],
            [
                'name' => 'Editeur de logiciels',
            ],
            [
                'name' => 'Société de portage',
            ],
            [
                'name' => 'Sourcing / chasseur de têtes',
            ],
            [
                'name' => 'ESN',
            ],
            [
                'name' => 'Start-up',
            ],
            [
                'name' => 'Centre de formation',
            ],
            [
                'name' => 'Ecole IT / Université',
            ],
        ];
    }

    public function getTestData(): array
    {
        return [
            [
                'name' => 'Business activity 1',
            ],
            [
                'name' => 'Business activity 2',
            ],
            [
                'name' => 'Business activity 3',
            ],
        ];
    }
}
