<?php

namespace App\FeedRss\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use Doctrine\Persistence\ObjectManager;

class FeedRssFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as [$type, $partner, $name]) {
            $feed = (new FeedRss())
                ->setType($type)
                ->setPartner($partner)
                ->setName($name)
            ;

            $manager->persist($feed);
        }

        $manager->flush();
    }

    public function getData(): array
    {
        return [
            [FeedRssType::CONTRACTOR, FeedRssPartner::NEUVOO, 'Neuvoo Contractor'],
            [FeedRssType::CONTRACTOR, FeedRssPartner::LINKEDIN, 'Linkedin Contractor'],
            [FeedRssType::CONTRACTOR, FeedRssPartner::INDEED, 'Indeed Contractor'],
            [FeedRssType::WORKER, FeedRssPartner::NEUVOO, 'Neuvoo Worker'],
            [FeedRssType::WORKER, FeedRssPartner::JOBIJOBA, 'Jobijoba Worker'],
            [FeedRssType::CONTRACTOR, FeedRssPartner::JOBIJOBA, 'Jobijoba Contractor'],
            [FeedRssType::WORKER, FeedRssPartner::POLEEMPLOI, 'Pole Emploi Worker'],
            [FeedRssType::WORKER, FeedRssPartner::JOBRAPIDO, 'Jobrapido Worker'],
            [FeedRssType::CONTRACTOR, FeedRssPartner::JOBRAPIDO, 'Jobrapido Contractor'],
            [FeedRssType::WORKER, FeedRssPartner::METEOJOBRHONESALPES, 'Meteojob Rhones Alpes Worker'],
            [FeedRssType::WORKER, FeedRssPartner::LINKEDIN, 'Linkedin Worker'],
            [FeedRssType::WORKER, FeedRssPartner::INDEED, 'Indeed Worker'],
            [FeedRssType::WORKER, FeedRssPartner::JOBLIFT, 'jobflit Worker'],
            [FeedRssType::WORKER, FeedRssPartner::JOOBLE, 'Jooble Worker'],
            [FeedRssType::PREMIUM, FeedRssPartner::LINKEDIN, 'Linkedin Premium'],
        ];
    }
}
