<?php

namespace App\Recruiter\Controller\Turnover\Recruiter;

use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Manager\RecruiterManager;
use App\Recruiter\Security\AccessTokenUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class DeleteItem
{
    public function __invoke(Recruiter $data, AccessTokenUtils $atu, EntityManagerInterface $em, RecruiterManager $rm): Response
    {
        // soft-delete recruiter and company if recruiter is main
        $rm->delete($data);
        $em->flush();

        $response = new Response(status: Response::HTTP_NO_CONTENT);

        // logout
        $atu->logout($data, $response);

        return $response;
    }
}
