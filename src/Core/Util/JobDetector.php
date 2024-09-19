<?php

namespace App\Core\Util;

use App\Core\Entity\Job;
use App\Core\Entity\Skill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class JobDetector
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function detect(string $value): ?Job
    {
        $slugger = new AsciiSlugger();
        $separator = ' ';

        $synonyms = [
            'applications' => 'application',
            'applicatif' => 'application',
            'applicatifs' => 'application',
        ];

        $value = $slugger->slug($value, $separator)->lower()->toString();
        $valueParts = Arrays::map(array_filter(explode($separator, $value), static function (string $part) {
            return \strlen($part) > 2;
        }), static function (string $part) use ($synonyms) {
            return $synonyms[$part] ?? $part;
        });

        $skillNames = Arrays::map($this->em->getRepository(Skill::class)->findNames(), static function (string $skillName) {
            return Strings::lower(Strings::stripAccents($skillName));
        });

        $jobs = [];
        foreach ($this->em->getRepository(Job::class)->findAll() as $job) {
            $jobs[$job->getId()] = $job;
        }

        $scores = [];
        foreach ($jobs as $job) {
            $jobNames = Arrays::map([$job->getName(), $job->getNameForContribution(), $job->getNameForUser()], static function (string $name) use ($synonyms) {
                $part = Strings::stripAccents(Strings::lower($name));

                return $synonyms[$part] ?? $part;
            });

            $jobNameUniqueParts = array_unique(array_filter(explode($separator, $slugger->slug(implode(' ', $jobNames), $separator)->lower()->toString()), static function (string $part) {
                return \strlen($part) > 2;
            }));

            // exact match
            if (\in_array($value, $jobNames, true)) {
                return $jobs[$job->getId()];
            }

            // synonyms
            $matches = array_intersect($jobNameUniqueParts, $valueParts);

            // scoring match
            $scores[$job->getId()] = array_sum(Arrays::map($matches, static function (string $part) use ($skillNames) {
                return \in_array($part, $skillNames, true) ? 5 : 1;
            }));
        }

        if (0 === $maxScore = max($scores)) {
            return null;
        }

        return $jobs[array_search($maxScore, $scores, true)];
    }
}
