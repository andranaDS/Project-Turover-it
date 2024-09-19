<?php

namespace App\JobPosting\Controller\FreeWork\JobPosting;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingUserTrace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class Trace
{
    public function __invoke(JobPosting $data, EntityManagerInterface $em): Response
    {
        $trace = (new JobPostingUserTrace())
            ->setJobPosting($data)
        ;

        $em->persist($trace);
        $em->flush();

        return new Response(status: Response::HTTP_CREATED);
    }
}
