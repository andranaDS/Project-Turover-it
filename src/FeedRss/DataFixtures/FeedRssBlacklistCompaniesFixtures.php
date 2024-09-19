<?php

namespace App\FeedRss\DataFixtures;

use App\Company\DataFixtures\CompaniesFixtures;
use App\Company\Entity\Company;
use App\Core\DataFixtures\AbstractFixture;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Entity\FeedRssBlacklistCompany;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FeedRssBlacklistCompaniesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $companies = [];
    private array $feeds = [];

    public function load(ObjectManager $manager): void
    {
        foreach ($manager->getRepository(Company::class)->findAll() as $company) {
            /* @var Company $company */
            $this->companies[$company->getId()] = $company;
        }

        foreach ($manager->getRepository(FeedRss::class)->findAll() as $feedRss) {
            /* @var FeedRss $feedRss */
            $this->feeds[$feedRss->getId()] = $feedRss;
        }

        foreach ($this->getData() as [$id, $blacklistCompanyIds]) {
            foreach ($blacklistCompanyIds as $blacklistCompanyId) {
                $feedRssBlacklistCompany = (new FeedRssBlacklistCompany())
                    ->setFeedRss($this->feeds[$id])
                    ->setCompany($this->companies[$blacklistCompanyId])
                ;

                $manager->persist($feedRssBlacklistCompany);
            }
        }

        $manager->flush();
    }

    public function getData(): array
    {
        return [
            [
                1,
                [1, 3],
            ],
            [
                2,
                [2],
            ],
            [
                3,
                [1, 2, 3],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            FeedRssFixtures::class,
            CompaniesFixtures::class,
        ];
    }
}
