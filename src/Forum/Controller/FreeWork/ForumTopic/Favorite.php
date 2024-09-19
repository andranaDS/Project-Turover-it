<?php

namespace App\Forum\Controller\FreeWork\ForumTopic;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicFavorite;
use App\User\Contracts\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class Favorite
{
    public function __invoke(ForumTopic $data, Security $security, EntityManagerInterface $em): Response
    {
        if ((null === $user = $security->getUser()) || !$user instanceof UserInterface) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        if (null !== $topicFavorite = $em->getRepository(ForumTopicFavorite::class)->findOneBy([
            'topic' => $data,
            'user' => $user,
        ])) {
            $em->remove($topicFavorite);
            $status = Response::HTTP_NO_CONTENT;
        } else {
            $topicFavorite = (new ForumTopicFavorite())
                   ->setUser($user)
                    ->setTopic($data)
            ;

            $em->persist($topicFavorite);
            $status = Response::HTTP_CREATED;
        }

        $em->flush();

        return new Response(status: $status);
    }
}
