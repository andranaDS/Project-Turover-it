<?php

namespace App\JobPosting\Controller\Turnover\JobPosting;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\JobPosting\Entity\JobPosting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class GetCollection
{
    public function __invoke(Request $request, EntityManagerInterface $em): PaginatorInterface
    {
        return $em->getRepository(JobPosting::class)->getPaginatorTurnoverSearch($request);
    }
}
