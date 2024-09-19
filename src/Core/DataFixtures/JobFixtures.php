<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\Job;
use App\Core\Entity\JobCategory;
use App\Core\Util\Files;
use App\Core\Util\Strings;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JobFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $categories = [];

    public function load(ObjectManager $manager): void
    {
        // fetch all categories
        $categories = $manager->getRepository(JobCategory::class)->findAll();
        foreach ($categories as $category) {
            /* @var JobCategory $category */
            $this->categories[$category->getName()] = $category;
        }

        $jobs = [];
        foreach ($this->getData() as $d) {
            $job = (new Job())
                ->setName($d['name'])
                ->setCategory(null !== $d['category'] ? $this->categories[$d['category']] : null)
                ->setAvailableForUser($d['availableForUser'])
                ->setNameForUser($d['nameForUser'] ?? $d['name'])
                ->setAvailableForContribution($d['availableForContribution'])
                ->setNameForContribution($d['nameForContribution'] ?? $d['name'])
                ->setSalaryDescription($d['salaryDescription'])
                ->setSalaryFormation($d['salaryFormation'])
                ->setSalaryStandardMission($d['salaryStandardMission'])
                ->setSalarySkills($d['salarySkills'])
                ->setSalarySeoMetaTitle($d['salarySeoMetaTitle'])
                ->setSalarySeoMetaDescription($d['salarySeoMetaDescription'])
                ->setFaqDescription($d['faqDescription'])
                ->setFaqPrice($d['faqPrice'])
                ->setFaqDefinition($d['faqDefinition'])
                ->setFaqMissions($d['faqMissions'])
                ->setFaqSkills($d['faqSkills'])
                ->setFaqProfile($d['faqProfile'])
                ->setFaqSeoMetaTitle($d['faqSeoMetaTitle'])
                ->setFaqSeoMetaDescription($d['faqSeoMetaDescription'])
            ;
            $manager->persist($job);

            $jobs[$d['id']] = [
                'job' => $job,
                'jobIdParentForContribution' => $d['parentForContribution'],
            ];
        }

        foreach ($jobs as $j) {
            if (null === $jobIdParentForContribution = $j['jobIdParentForContribution']) {
                continue;
            }
            $job = $j['job'];
            $jobParentForContribution = $jobs[$jobIdParentForContribution]['job'];
            $job->setParentForContribution($jobParentForContribution);
        }

        $manager->flush();
    }

    public function getData(): array
    {
        $csvData = Files::getCsvData(__DIR__ . '/data/jobs.csv', true);

        $data = [];
        $i = 0;
        foreach ($csvData as $d) {
            $data[] = [
                'id' => ++$i,
                'name' => $d[0],
                'category' => $d[1],
                'availableForUser' => '1' === $d[2],
                'nameForUser' => $d[3],
                'availableForContribution' => '1' === $d[4],
                'nameForContribution' => $d[5],
                'parentForContribution' => $d[6],
                'salaryDescription' => $d[7],
                'salaryFormation' => $d[8],
                'salaryStandardMission' => $d[9],
                'salarySkills' => explode("\n", $d[10]),
                'salarySeoMetaTitle' => null === $d[11] ? null : Strings::substrToLength($d[11], 70),
                'salarySeoMetaDescription' => null === $d[12] ? null : Strings::substrToLength($d[12], 160),
                'faqDescription' => $d[13],
                'faqPrice' => $d[14],
                'faqDefinition' => $d[15],
                'faqMissions' => $d[16],
                'faqSkills' => $d[17],
                'faqProfile' => $d[18],
                'faqSeoMetaTitle' => null === $d[19] ? null : Strings::substrToLength($d[19], 70),
                'faqSeoMetaDescription' => null === $d[20] ? null : Strings::substrToLength($d[20], 160),
            ];
        }

        return $data;
    }

    public function getDependencies(): array
    {
        return [
            JobCategoriesFixtures::class,
        ];
    }
}
