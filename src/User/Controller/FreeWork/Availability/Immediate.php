<?php

namespace App\User\Controller\FreeWork\Availability;

use App\User\Entity\User;
use App\User\Enum\Availability;
use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Immediate
{
    /**
     * @Route(
     *     name="api_user_freework_availability_immediate",
     *     path="/availability/immediate/{userId}",
     *     methods={"GET"},
     *     host="%api_free_work_base_url%",
     * )
     * @Cache(smaxage="0", maxage="0")
     */
    public function __invoke(Request $request, EntityManagerInterface $em, RouterInterface $router, TranslatorInterface $translator, UrlSignerInterface $urlSigner, string $userId): RedirectResponse
    {
        if (false === $urlSigner->validate($request->getUri())) {
            $params = [
                'status' => 'error',
                'code' => '86a679f9',
                'message' => $translator->trans('user.availability.immediate.error.link_expired'),
            ];
        } elseif (null === $user = $em->find(User::class, $userId)) {
            $params = [
                'status' => 'error',
                'code' => 'a9d859cd',
                'message' => $translator->trans('user.availability.immediate.error.link_expired'),
            ];
        } else {
            $user
                ->setStatusUpdatedAt(new \DateTime())
                ->setNextAvailabilityAt(new \DateTime())
                ->setAvailability(Availability::IMMEDIATE)
            ;

            $em->flush();
            $params = [
                'status' => 'success',
                'message' => $translator->trans('user.availability.immediate.success'),
            ];
        }

        return new RedirectResponse($router->generate('candidates_availability_update', $params));
    }
}