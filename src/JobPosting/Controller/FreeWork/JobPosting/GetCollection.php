<?php

namespace App\JobPosting\Controller\FreeWork\JobPosting;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\JobPosting\ElasticSearch\JobPostingsFilters\JobPostingsRequestFiltersBuilder;
use App\JobPosting\Entity\JobPosting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class GetCollection
{
    public function __invoke(JobPostingsRequestFiltersBuilder $fb, Request $request, EntityManagerInterface $em, int $appItemsPerPage): PaginatorInterface
    {
        $page = 0 === ($page = (int) $request->query->get('page')) ? 1 : $page;
        $itemsPerPage = 0 === ($itemsPerPage = (int) $request->query->get('itemsPerPage')) ? $appItemsPerPage : $itemsPerPage;

        return $em->getRepository(JobPosting::class)->getPaginatorSearch($fb->build($request), $page, $itemsPerPage);
    }
}
