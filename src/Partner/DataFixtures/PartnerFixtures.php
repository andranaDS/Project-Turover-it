<?php

namespace App\Partner\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Partner\Entity\Partner as PartnerEntity;
use App\Partner\Enum\Partner as PartnerEnum;
use Doctrine\Persistence\ObjectManager;

class PartnerFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as [$partner, $distribution]) {
            $entity = (new PartnerEntity())
                ->setPartner($partner)
                ->setDistribution($distribution)
            ;

            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getData(): array
    {
        return [
            [PartnerEnum::NONE, 0],
            [PartnerEnum::FREELANCECOM, 100],
        ];
    }
}
