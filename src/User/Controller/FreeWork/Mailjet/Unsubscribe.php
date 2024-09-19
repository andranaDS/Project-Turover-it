<?php

namespace App\User\Controller\FreeWork\Mailjet;

use App\User\Entity\MailjetUnsubscribeLog;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Unsubscribe
{
    private const UNSUB_EVENT = 'unsub';

    /**
     * @Route(
     *     name="api_user_mailjet_unsubscribe",
     *     path="/mailjet/unsubscribe",
     *     methods={"POST"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     *     )
     */
    public function __invoke(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse();
        }

        $email = $data[0]['email'] ?? null;
        $event = $data[0]['event'] ?? null;
        $mailjetUnsubscribeLog = (new MailjetUnsubscribeLog())
            ->setEmail($email)
            ->setPayload($data)
        ;

        if (
            self::UNSUB_EVENT === $event &&
            null !== $email &&
            (
                null !== $user = $entityManager->getRepository(User::class)->findOneByEmail($email)
            ) &&
            null !== $user->getNotification()
        ) {
            $mailjetUnsubscribeLog->setUnsubscribed(true);
            $user->getNotification()->setMarketingNewsletter(false);
        }

        $entityManager->persist($mailjetUnsubscribeLog);
        $entityManager->flush();

        return new JsonResponse();
    }
}
