<?php

namespace App\Resource\Manager;

use App\Core\Entity\Job;
use App\Core\Entity\Skill;
use App\Resource\Entity\Trend;
use App\Resource\Entity\TrendJobLine;
use App\Resource\Entity\TrendJobTable;
use App\Resource\Entity\TrendSkillLine;
use App\Resource\Entity\TrendSkillTable;
use App\Sync\Turnover\Client;
use App\User\Entity\User;
use App\User\Entity\UserDocument;
use App\User\Entity\UserJob;
use App\User\Entity\UserSkill;
use Doctrine\ORM\EntityManagerInterface;

class TrendManager
{
    private EntityManagerInterface $em;
    private Client $turnover;

    public function __construct(EntityManagerInterface $em, Client $turnover)
    {
        $this->em = $em;
        $this->turnover = $turnover;
    }

    public function createTrend(\DateTime $date): bool
    {
        if (true === $this->trendExists($date)) {
            return false;
        }

        $lastTrend = $this->getLastTrend(true, 100);

        $start = (clone $date)->modify('-14 days');
        $end = (clone $date)->modify('-1 second');

        $trend = (new Trend())
            ->setDate($date->format('Y-m-d'))
            ->setResumesCount($this->getResumesCount())
            ->setGenderDistribution($this->getGenderDistribution($start, $end))
            ->setStatusDistribution($this->getStatusDistribution($start, $end))
            ->setRemoteDistribution($this->getRemoteDistribution($start, $end))
            ->setCandidateSkillsTable($this->getCandidateSkillsTable($start, $end, $lastTrend))
            ->setRecruiterSkillsTable($this->getRecruiterSkillsTable($start, $end, $lastTrend))
            ->setCandidateJobsTable($this->getCandidateJobsTable($start, $end, $lastTrend))
            ->setRecruiterJobsTable($this->getRecruiterJobsTable($start, $end, $lastTrend))
        ;

        $this->em->persist($trend);
        $this->em->flush();

        return true;
    }

    public function trendExists(\DateTime $date): bool
    {
        return null !== $this->getDateTrend($date);
    }

    public function getLastTrend(bool $withJoins = false, ?int $maxResults = null): ?Trend
    {
        if (null === $trend = $this->em->getRepository(Trend::class)->findLastWithData()) {
            return null;
        }

        if (true === $withJoins) {
            $this->loadTrendTables($trend, $maxResults);
        }

        return $trend;
    }

    public function getDateTrend(\DateTime $date, bool $withJoins = false, ?int $maxResults = null): ?Trend
    {
        if (null === $trend = $this->em->getRepository(Trend::class)->findOneByDateWithData($date->format('Y-m-d'))) {
            return null;
        }

        if (true === $withJoins) {
            $this->loadTrendTables($trend, $maxResults);
        }

        return $trend;
    }

    public function loadTrendTables(Trend $trend, ?int $maxResults): void
    {
        if ((null !== $candidateSkillsTable = $trend->getCandidateSkillsTable()) && (null !== $candidateSkillsTableId = $candidateSkillsTable->getId())) {
            $this->em->getRepository(TrendSkillTable::class)->findOneByIdWithData($candidateSkillsTableId, $maxResults);
        }

        if ((null !== $recruiterSkillsTable = $trend->getRecruiterSkillsTable()) && (null !== $recruiterSkillsTableId = $recruiterSkillsTable->getId())) {
            $this->em->getRepository(TrendSkillTable::class)->findOneByIdWithData($recruiterSkillsTableId, $maxResults);
        }

        if ((null !== $candidateJobsTable = $trend->getCandidateJobsTable()) && (null !== $candidateJobsTableId = $candidateJobsTable->getId())) {
            $this->em->getRepository(TrendJobTable::class)->findOneByIdWithData($candidateJobsTableId, $maxResults);
        }

        if ((null !== $recruiterJobsTable = $trend->getRecruiterJobsTable()) && (null !== $recruiterJobsTableId = $recruiterJobsTable->getId())) {
            $this->em->getRepository(TrendJobTable::class)->findOneByIdWithData($recruiterJobsTableId, $maxResults);
        }
    }

    public function getResumesCount(): int
    {
        return $this->em->getRepository(UserDocument::class)->countUserResume();
    }

    public function getGenderDistribution(\DateTime $start, \DateTime $end): array
    {
        $data = $this->em->getRepository(User::class)->countByGender($start, $end);

        return self::formatDistribution($data);
    }

    public function getStatusDistribution(\DateTime $start, \DateTime $end): array
    {
        $data = $this->em->getRepository(User::class)->countByType($start, $end);

        return self::formatDistribution($data);
    }

    public function getRemoteDistribution(\DateTime $start, \DateTime $end): array
    {
        $data = $this->em->getRepository(User::class)->countByRemote($start, $end);

        return self::formatDistribution($data);
    }

    private static function formatDistribution(array $data): array
    {
        $total = array_sum(array_map(static function (array $d) {
            return $d['count'];
        }, $data));

        $distribution = [];
        foreach ($data as $d) {
            $distribution[$d['value']] = [
                'count' => $d['count'],
                'percentage' => round($d['count'] / $total, 2),
            ];
        }

        return $distribution;
    }

    public function getCandidateSkillsTable(\DateTime $start, \DateTime $end, ?Trend $previousTrend = null): TrendSkillTable
    {
        $data = $this->em->getRepository(UserSkill::class)->countForTrend($start, $end);
        $data = \array_slice($data, 0, 100);

        $table = new TrendSkillTable();
        $i = 0;
        foreach ($data as $d) {
            if (null === $skill = $this->em->find(Skill::class, $d['skill'])) {
                continue;
            }

            $line = (new TrendSkillLine())
                ->setSkill($skill)
                ->setPosition(++$i)
                ->setCount($d['count'])
            ;

            if (null !== $previousTrend && null !== ($previousTable = $previousTrend->getCandidateSkillsTable())) {
                /** @var ?TrendSkillLine $oldLine */
                $oldLine = $previousTable->getLines()->filter(static function (TrendSkillLine $line) use ($skill) {
                    return $line->getSkill() === $skill;
                })->first();

                if ($oldLine instanceof TrendSkillLine) {
                    $line->setEvolution($oldLine->getPosition() - $line->getPosition());
                }
            }

            $table->addLine($line);
        }

        return $table;
    }

    public function getCandidateJobsTable(\DateTime $start, \DateTime $end, ?Trend $previousTrend = null): TrendJobTable
    {
        $data = $this->em->getRepository(UserJob::class)->countForTrend($start, $end);
        $data = \array_slice($data, 0, 100);

        $table = new TrendJobTable();
        $i = 0;
        foreach ($data as $d) {
            if (null === $job = $this->em->find(Job::class, $d['job'])) {
                continue;
            }

            $line = (new TrendJobLine())
                ->setJob($job)
                ->setPosition(++$i)
                ->setCount($d['count'])
            ;

            if (null !== $previousTrend && null !== ($previousTable = $previousTrend->getCandidateJobsTable())) {
                /** @var ?TrendJobLine $oldLine */
                $oldLine = $previousTable->getLines()->filter(static function (TrendJobLine $line) use ($job) {
                    return $line->getJob() === $job;
                })->first();

                if ($oldLine instanceof TrendJobLine) {
                    $line->setEvolution($oldLine->getPosition() - $line->getPosition());
                }
            }

            $table->addLine($line);
        }

        return $table;
    }

    public function getRecruiterSkillsTable(\DateTime $start, \DateTime $end, ?Trend $previousTrend = null): TrendSkillTable
    {
        $data = $this->turnover->getTrendSkills($this->em->getRepository(Skill::class)->findNames(), $start, $end);
        $data = \array_slice($data, 0, 100);

        $table = new TrendSkillTable();
        $i = 0;
        foreach ($data as $d) {
            if (null === $skill = $this->em->getRepository(Skill::class)->findOneBy(['name' => $d['skill']])) {
                continue;
            }

            $line = (new TrendSkillLine())
                ->setSkill($skill)
                ->setPosition(++$i)
                ->setCount($d['count'])
            ;

            if (null !== $previousTrend && null !== ($previousTable = $previousTrend->getRecruiterSkillsTable())) {
                /** @var ?TrendSkillLine $oldLine */
                $oldLine = $previousTable->getLines()->filter(static function (TrendSkillLine $line) use ($skill) {
                    return $line->getSkill() === $skill;
                })->first();

                if ($oldLine instanceof TrendSkillLine) {
                    $line->setEvolution($oldLine->getPosition() - $line->getPosition());
                }
            }

            $table->addLine($line);
        }

        return $table;
    }

    public function getRecruiterJobsTable(\DateTime $start, \DateTime $end, ?Trend $previousTrend = null): TrendJobTable
    {
        $data = $this->turnover->getTrendJobs($this->em->getRepository(Job::class)->findNames(), $start, $end);
        $data = \array_slice($data, 0, 100);

        $table = new TrendJobTable();
        $i = 0;
        foreach ($data as $d) {
            if (null === $job = $this->em->getRepository(Job::class)->findOneBy(['name' => $d['job']])) {
                continue;
            }

            $line = (new TrendJobLine())
                ->setJob($job)
                ->setPosition(++$i)
                ->setCount($d['count'])
            ;

            if (null !== $previousTrend && null !== ($previousTable = $previousTrend->getRecruiterJobsTable())) {
                /** @var ?TrendJobLine $oldLine */
                $oldLine = $previousTable->getLines()->filter(static function (TrendJobLine $line) use ($job) {
                    return $line->getJob() === $job;
                })->first();

                if ($oldLine instanceof TrendJobLine) {
                    $line->setEvolution($oldLine->getPosition() - $line->getPosition());
                }
            }

            $table->addLine($line);
        }

        return $table;
    }
}
