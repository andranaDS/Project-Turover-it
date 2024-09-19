<?php

namespace App\JobPosting\Manager;

use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\JobPostingUserFavorite;
use App\JobPosting\Entity\JobPostingUserTrace;
use App\JobPosting\Enum\ApplicationStep;
use App\User\Contracts\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Arrays;

class JobPostingDataManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUserJobPostingData(UserInterface $user, array $scopes = []): array
    {
        $data = [];

        if (\in_array('job_posting_favorites', $scopes, true)) {
            $data['job_posting_favorites'] = $this->getUserJobPostingUserFavorites($user);
        }

        if (\in_array('job_posting_application_in_progress', $scopes, true)) {
            $data['job_posting_application_in_progress'] = $this->getUserJobPostingApplicationInProgress($user);
        }

        if (\in_array('job_posting_application_ko', $scopes, true)) {
            $data['job_posting_application_ko'] = $this->getUserJobPostingApplicationKo($user);
        }

        if (\in_array('job_posting_traces', $scopes, true)) {
            $data['job_posting_traces'] = $this->getUserJobPostingUserTraces($user);
        }

        return $data;
    }

    private function getUserJobPostingUserFavorites(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(JobPostingUserFavorite::class)->findJobPostingIdByUser($user), function (array $element) {
            return (int) $element['jobPostingId'];
        });
    }

    private function getUserJobPostingApplicationInProgress(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(Application::class)->findJobPostingIdByUser($user, [ApplicationStep::RESUME, ApplicationStep::SEEN]), function (array $element) {
            return (int) $element['jobPostingId'];
        });
    }

    private function getUserJobPostingApplicationKo(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(Application::class)->findJobPostingIdByUser($user, [ApplicationStep::KO, ApplicationStep::CANCELLED]), function (array $element) {
            return (int) $element['jobPostingId'];
        });
    }

    private function getUserJobPostingUserTraces(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(JobPostingUserTrace::class)->findJobPostingIdsByUser($user), function (array $element) {
            return (int) $element['jobPostingId'];
        });
    }
}
