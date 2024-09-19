<?php

namespace App\JobPosting\Controller\Turnover\JobPosting;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingRecruiterTrace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class Trace
{
    public function __invoke(JobPosting $data, EntityManagerInterface $em): Response
    {
        $trace = (new JobPostingRecruiterTrace())
            ->setJobPosting($data)
        ;

        $em->persist($trace);
        $em->flush();

        return new Response(status: Response::HTTP_CREATED);
    }
}
