<?php

namespace App\JobPosting\ElasticSearch\JobPostingsFilters;

use App\Core\Entity\Location;
use App\JobPosting\Entity\JobPostingSearch;
use App\JobPosting\Entity\JobPostingSearchLocation;

class JobPostingsJobPostingSearchFiltersBuilder
{
    public function build(JobPostingSearch $jobPostingSearch): JobPostingsFilters
    {
        $filters = new JobPostingsFilters();

        if (null !== $jobPostingSearch->getSearchKeywords()) {
            $filters->setKeywords(JobPostingsFilters::buildArray($jobPostingSearch->getSearchKeywords()));
        }

        if (null !== $jobPostingSearch->getRemoteMode()) {
            $filters->setRemoteMode($jobPostingSearch->getRemoteMode());
        }

        if (!empty($jobPostingSearch->getContracts())) {
            $filters->setContracts($jobPostingSearch->getContracts());
        }

        if (null !== $jobPostingSearch->getMinAnnualSalary()) {
            $filters->setMinAnnualSalary($jobPostingSearch->getMinAnnualSalary());
        }

        if (null !== $jobPostingSearch->getMinDailySalary()) {
            $filters->setMinDailySalary($jobPostingSearch->getMinDailySalary());
        }

        if (null !== $jobPostingSearch->getMinduration()) {
            $filters->setMinDuration($jobPostingSearch->getMinduration());
        }

        if (null !== $jobPostingSearch->getMaxduration()) {
            $filters->setMaxDuration($jobPostingSearch->getMaxduration());
        }

        if ($jobPostingSearch->getLocations()->count() > 0) {
            $filters->setLocationKeys(array_values(array_filter(array_map(static function (JobPostingSearchLocation $jobPostingSearchLocation) {
                $location = $jobPostingSearchLocation->getLocation();

                return $location instanceof Location ? $location->getKey() : null;
            }, $jobPostingSearch->getLocations()->getValues()))));
        }

        return $filters;
    }
}
