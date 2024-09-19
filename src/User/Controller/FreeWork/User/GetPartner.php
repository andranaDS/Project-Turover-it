<?php

namespace App\User\Controller\FreeWork\User;

use App\User\Repository\UserLeadRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GetPartner
{
    /**
     * @Route(
     *     name="api_user_freework_partner_get",
     *     path="/user_partner",
     *     methods={"GET"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     * @Cache(smaxage="0", maxage="0")
     */
    public function __invoke(Request $request, Security $security, UserLeadRepository $userLeadRepository): JsonResponse
    {
        if (null === $user = $security->getUser()) {
            throw new UnauthorizedHttpException('unauthorized');
        }

        $userLead = null !== $userLeadRepository->findOneBy(['user' => $user, 'isSuccess' => true]);

        return new JsonResponse(['sent' => $userLead]);
    }
}
