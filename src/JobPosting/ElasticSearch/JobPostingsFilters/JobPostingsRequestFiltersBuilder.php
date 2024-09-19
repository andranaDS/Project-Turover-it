<?php

namespace App\JobPosting\ElasticSearch\JobPostingsFilters;

use Symfony\Component\HttpFoundation\Request;

class JobPostingsRequestFiltersBuilder
{
    public function build(Request $request): JobPostingsFilters
    {
        return (new JobPostingsFilters())
            ->setMinDuration(JobPostingsFilters::buildInteger($request->query->get('minDuration')))
            ->setMaxDuration(JobPostingsFilters::buildInteger($request->query->get('maxDuration')))
            ->setContracts(JobPostingsFilters::buildArray($request->query->get('contracts')))
            ->setPublishedSince(JobPostingsFilters::buildString($request->query->get('publishedSince')))
            ->setLocationKeys(JobPostingsFilters::buildArray($request->query->get('locationKeys')))
            ->setMinAnnualSalary(JobPostingsFilters::buildInteger($request->query->get('minAnnualSalary')))
            ->setMinDailySalary(JobPostingsFilters::buildInteger($request->query->get('minDailySalary')))
            ->setRemoteMode(JobPostingsFilters::buildArray($request->query->get('remoteMode')))
            ->setKeywords(JobPostingsFilters::buildArray($request->query->get('searchKeywords')))
            ->setJobs(JobPostingsFilters::buildArray($request->query->get('jobs')))
            ->setOrder($request->query->get('order', JobPostingsFilters::ORDER_RELEVANCE))
            ->setStartsAt(JobPostingsFilters::buildDatetime($request->query->get('startsAt')))
            ->setCompanyBusinessActivity($request->query->get('businessActivity'))
            ->setIntercontractOnly(JobPostingsFilters::buildBoolean($request->query->get('strict')))
        ;
    }
}
