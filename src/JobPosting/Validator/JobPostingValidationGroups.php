<?php

namespace App\JobPosting\Validator;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\ApplicationType;
use App\JobPosting\Enum\Status;

class JobPostingValidationGroups
{
    /**
     * @return string[]
     */
    public static function validationGroups(JobPosting $jobPosting): array
    {
        $groups = [];
        $status = [Status::PUBLISHED, Status::PRIVATE];

        if (\in_array($jobPosting->getStatus(), $status, true)) {
            $groups[] = 'job_posting:post:status-published';

            if (true === $jobPosting->hasFreeContract()) {
                $groups[] = 'job_posting:post:status-published:contract-free';
            }

            if (true === $jobPosting->hasWorkContract()) {
                $groups[] = 'job_posting:post:status-published:contract-work';
            }

            if (true === $jobPosting->hasTemporaryContract()) {
                $groups[] = 'job_posting:post:status-published:contract-temporary';
            }

            if (true === $jobPosting->hasPermanentContract()) {
                $groups[] = 'job_posting:post:status-published:contract-permanent';
            }

            if (ApplicationType::TURNOVER === $jobPosting->getApplicationType()) {
                $groups[] = 'job_posting:post:status-published:type-turnover';
            }

            if (ApplicationType::CONTACT === $jobPosting->getApplicationType()) {
                $groups[] = 'job_posting:post:status-published:type-contact';
            }

            if (ApplicationType::URL === $jobPosting->getApplicationType()) {
                $groups[] = 'job_posting:post:status-published:type-url';
            }
        }

        return $groups;
    }
}
