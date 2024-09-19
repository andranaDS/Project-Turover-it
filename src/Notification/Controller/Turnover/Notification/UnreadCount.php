<?php

namespace App\Notification\Controller\Turnover\Notification;

use App\Notification\Repository\NotificationRepository;
use App\Recruiter\Entity\Recruiter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class UnreadCount
{
    /**
     * @Route(
     *     name="api_notification_turnover_notification_unread_count",
     *     path="/notifications/unread/count",
     *     methods={"GET"},
     *     condition= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
     * )
     * @Security("is_granted('ROLE_RECRUITER')")
     */
    public function __invoke(?UserInterface $recruiter, NotificationRepository $repository): Response
    {
        if (!$recruiter instanceof Recruiter) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($repository->countUnreadNotifications($recruiter));
    }
}
