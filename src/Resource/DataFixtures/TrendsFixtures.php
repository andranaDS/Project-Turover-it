<?php

namespace App\Resource\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\JobFixtures;
use App\Core\DataFixtures\SkillsFixtures;
use App\Core\Entity\Job;
use App\Core\Entity\Skill;
use App\Core\Util\Arrays;
use App\Core\Util\Dates;
use App\Resource\Entity\Trend;
use App\Resource\Entity\TrendJobLine;
use App\Resource\Entity\TrendJobTable;
use App\Resource\Entity\TrendSkillLine;
use App\Resource\Entity\TrendSkillTable;
use App\Resource\Manager\TrendManager;
use App\User\DataFixtures\UsersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TrendsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private ObjectManager $manager;
    private ?array $skills = null;
    private ?array $jobs = null;
    private TrendManager $tm;

    public function __construct(string $env, TrendManager $tm)
    {
        parent::__construct($env);
        $this->tm = $tm;
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        foreach ($this->getData() as $d) {
            $trend = (new Trend())
                ->setDate($d['date'])
                ->setResumesCount($d['resumesCount'])
                ->setGenderDistribution($d['genderDistribution'])
                ->setStatusDistribution($d['statusDistribution'])
                ->setRemoteDistribution($d['remoteDistribution'])
                ->setCandidateSkillsTable($this->createTrendSkillTable($d['candidateSkillsTable']))
                ->setRecruiterSkillsTable($this->createTrendSkillTable($d['recruiterSkillsTable']))
                ->setCandidateJobsTable($this->createTrendJobTable($d['candidateJobsTable']))
                ->setRecruiterJobsTable($this->createTrendJobTable($d['recruiterJobsTable']))
            ;

            $manager->persist($trend);
        }

        $manager->flush();
    }

    public function createTrendJobTable(array $data): TrendJobTable
    {
        $table = new TrendJobTable();

        foreach ($data as $d) {
            $table->addLine((new TrendJobLine())
                ->setJob($d['job'])
                ->setPosition($d['position'])
                ->setCount($d['count'])
                ->setEvolution($d['evolution'])
            );
        }

        return $table;
    }

    public function createTrendSkillTable(array $data): TrendSkillTable
    {
        $table = new TrendSkillTable();

        foreach ($data as $d) {
            $table->addLine((new TrendSkillLine())
                ->setSkill($d['skill'])
                ->setPosition($d['position'])
                ->setCount($d['count'])
                ->setEvolution($d['evolution'])
            );
        }

        return $table;
    }

    public function getDevData(): array
    {
        $data = [];

        $datesCount = 1;
        for ($i = $datesCount; $i >= 0; --$i) {
            $resumesCount = 12000;
            $date = Dates::lastWeek($i);
            $startDate = clone $date;
            $endDate = (clone $date)->modify('+ 7 days - 1 second');

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'resumesCount' => $resumesCount,
                'genderDistribution' => $this->tm->getGenderDistribution($startDate, $endDate),
                'statusDistribution' => $this->tm->getStatusDistribution($startDate, $endDate),
                'remoteDistribution' => $this->tm->getRemoteDistribution($startDate, $endDate),
                'candidateSkillsTable' => $this->getSkillsTableData(__DIR__ . '/data/dev/trend-' . $i . '-candidate-skills.csv'),
                'recruiterSkillsTable' => $this->getSkillsTableData(__DIR__ . '/data/dev/trend-' . $i . '-recruiter-skills.csv'),
                'candidateJobsTable' => $this->getJobsTableData(__DIR__ . '/data/dev/trend-' . $i . '-candidate-jobs.csv'),
                'recruiterJobsTable' => $this->getJobsTableData(__DIR__ . '/data/dev/trend-' . $i . '-recruiter-jobs.csv'),
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'date' => Dates::lastWeek(1)->format('Y-m-d'),
                'resumesCount' => 12000,
                'genderDistribution' => [
                    'male' => ['count' => 1, 'percentage' => 1],
                ],
                'statusDistribution' => [],
                'remoteDistribution' => [
                    'false' => ['count' => 1, 'percentage' => 1],
                ],
                'candidateSkillsTable' => $this->getSkillsTableData(__DIR__ . '/data/test/trend-1-candidate-skills.csv'),
                'recruiterSkillsTable' => $this->getSkillsTableData(__DIR__ . '/data/test/trend-1-recruiter-skills.csv'),
                'candidateJobsTable' => $this->getJobsTableData(__DIR__ . '/data/test/trend-1-candidate-jobs.csv'),
                'recruiterJobsTable' => $this->getJobsTableData(__DIR__ . '/data/test/trend-1-recruiter-jobs.csv'),
            ],
            [
                'date' => Dates::lastWeek(0)->format('Y-m-d'),
                'resumesCount' => 13000,
                'genderDistribution' => [
                    'male' => ['count' => 4, 'percentage' => .8],
                    'female' => ['count' => 1, 'percentage' => .2],
                ],
                'statusDistribution' => [
                    'free' => ['count' => 2, 'percentage' => .5],
                    'work' => ['count' => 2, 'percentage' => .5],
                ],
                'remoteDistribution' => [
                    'false' => ['count' => 2, 'percentage' => .4],
                    'true' => ['count' => 3, 'percentage' => .6],
                ],
                'candidateSkillsTable' => $this->getSkillsTableData(__DIR__ . '/data/test/trend-0-candidate-skills.csv'),
                'recruiterSkillsTable' => $this->getSkillsTableData(__DIR__ . '/data/test/trend-0-recruiter-skills.csv'),
                'candidateJobsTable' => $this->getJobsTableData(__DIR__ . '/data/test/trend-0-candidate-jobs.csv'),
                'recruiterJobsTable' => $this->getJobsTableData(__DIR__ . '/data/test/trend-0-recruiter-jobs.csv'),
            ],
        ];
    }

    public function getSkillsTableData(string $file): array
    {
        return Arrays::map(self::getCsvData($file), function (array $a) {
            return [
                'skill' => $this->getSkill($a[0]),
                'position' => $a[1],
                'evolution' => $a[2],
                'count' => $a[3],
            ];
        });
    }

    public function getJobsTableData(string $file): array
    {
        return Arrays::map(self::getCsvData($file), function (array $a) {
            return [
                'job' => $this->getJob($a[0]),
                'position' => $a[1],
                'evolution' => $a[2],
                'count' => $a[3],
            ];
        });
    }

    public function getJobs(): array
    {
        if (null !== $this->jobs) {
            return $this->jobs;
        }

        $this->jobs = [];
        foreach ($this->manager->getRepository(Job::class)->findAll() as $job) {
            $this->jobs[$job->getName()] = $job;
        }

        return $this->jobs;
    }

    public function getJob(string $name): Job
    {
        $jobs = $this->getJobs();
        if (!isset($jobs[$name])) {
            throw new \InvalidArgumentException(sprintf('Job "%s" was not found', $name));
        }

        return $jobs[$name];
    }

    public function getSkills(): array
    {
        if (null !== $this->skills) {
            return $this->skills;
        }

        $this->skills = [];
        foreach ($this->manager->getRepository(Skill::class)->findAll() as $skill) {
            $this->skills[$skill->getName()] = $skill;
        }

        return $this->skills;
    }

    public function getSkill(string $name): Skill
    {
        $skills = $this->getSkills();

        if (!isset($skills[$name])) {
            throw new \InvalidArgumentException(sprintf('Skill "%s" was not found', $name));
        }

        return $skills[$name];
    }

    public static function getCsvData(string $file): array
    {
        $data = [];
        if (($handle = fopen($file, 'r')) !== false) {
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                $data[] = Arrays::map($d, function (string $e) {
                    return '' === $e ? null : trim($e);
                });
            }
            fclose($handle);
        }

        return $data;
    }

    public function getDependencies(): array
    {
        return [
            SkillsFixtures::class,
            JobFixtures::class,
            UsersFixtures::class,
        ];
    }
}
