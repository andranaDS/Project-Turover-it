<?php

namespace App\JobPosting\ElasticSearch\JobPostingsFilters;

use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\RemoteMode;
use App\User\Entity\User;
use App\User\Entity\UserJob;
use App\User\Entity\UserMobility;
use App\User\Entity\UserSkill;
use Doctrine\ORM\EntityManagerInterface;

class JobPostingsUserFiltersBuilder
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function build(User $user): JobPostingsFilters
    {
        $filters = (new JobPostingsFilters());

        $skills = $this->em->getRepository(UserSkill::class)->findForSuggested($user);
        if (!empty($skills)) {
            $filters->setSkills($skills);
        }

        if ($user->getJobs()->count() > 0) {
            $filters->setKeywords(array_filter(array_map(static function (UserJob $userJob) {
                $job = $userJob->getJob();

                return $job?->getName();
            }, $user->getJobs()->getValues())));
        }

        $mobilities = $this->em->getRepository(UserMobility::class)->findForSuggested($user);
        if (!empty($mobilities)) {
            $filters->setLocationKeys(array_values(array_filter(array_map(static function (UserMobility $userMobility) {
                $location = $userMobility->getLocation();

                return $location?->getKey();
            }, $mobilities))));
        }

        if (true === $user->getFulltimeTeleworking()) {
            $filters->setRemoteMode([RemoteMode::FULL]);
        }

        $filters->setContracts($this->getContracts($user));

        return $filters;
    }

    private function getContracts(User $user): array
    {
        $contracts = [];

        if (true === $user->getFreelance()) {
            $contracts[] = Contract::CONTRACTOR;
        }

        return array_merge($contracts, $user->getContracts() ?? []);
    }
}
