<?php

namespace App\JobPosting\Controller\FreeWork\JobPosting;

use App\JobPosting\ElasticSearch\JobPostingsFilters\JobPostingsRequestFiltersBuilder;
use App\JobPosting\Entity\JobPosting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCount
{
    public function __invoke(JobPostingsRequestFiltersBuilder $fb, Request $request, EntityManagerInterface $em): Response
    {
        $count = $em->getRepository(JobPosting::class)->countSearch($fb->build($request));

        return new JsonResponse($count);
    }
}
