<?php

namespace App\Recruiter\Controller\Turnover\Authentication;

use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Security\AccessTokenUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class Logout
{
    /**
     * @Route(name="api_recruiter_turnover_authentication_logout", path="/logout", methods={"GET"}, host="%api_turnover_base_url%")
     * @IsGranted("ROLE_RECRUITER")
     */
    public function __invoke(Request $request, ?UserInterface $recruiter, AccessTokenUtils $atu): Response
    {
        if (!$recruiter instanceof Recruiter) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $response = new Response();
        $atu->logout($recruiter, $response);

        return $response;
    }
}
