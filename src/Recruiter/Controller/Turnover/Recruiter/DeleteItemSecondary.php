<?php

namespace App\Recruiter\Controller\Turnover\Recruiter;

use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Manager\RecruiterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class DeleteItemSecondary
{
    public function __invoke(EntityManagerInterface $em, Recruiter $data, RecruiterManager $recruiterManager): Response
    {
        $recruiterManager->delete($data);

        $em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
