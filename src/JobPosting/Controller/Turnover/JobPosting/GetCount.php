<?php

namespace App\JobPosting\Controller\Turnover\JobPosting;

use App\JobPosting\Entity\JobPosting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCount
{
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $count = $em->getRepository(JobPosting::class)->countTurnoverSearch($request);

        return new JsonResponse($count);
    }
}
