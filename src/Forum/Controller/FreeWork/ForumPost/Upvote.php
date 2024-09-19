<?php

namespace App\Forum\Controller\FreeWork\ForumPost;

use App\Core\Mailer\Mailer;
use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumPostUpvote;
use App\User\Contracts\UserInterface;
use App\User\Email\UserNotificationForumPostLikeEmail;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class Upvote
{
    public function __invoke(ForumPost $data, Security $security, EntityManagerInterface $em, Mailer $userMailer, LoggerInterface $logger): Response
    {
        if ((null === $user = $security->getUser()) || !$user instanceof UserInterface) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        if (null !== $postUpvote = $em->getRepository(ForumPostUpvote::class)->findOneBy([
            'post' => $data,
            'user' => $user,
        ])) {
            $em->remove($postUpvote);
            $status = Response::HTTP_NO_CONTENT;
        } else {
            $postUpvote = (new ForumPostUpvote())
                ->setUser($user)
                ->setPost($data)
            ;

            $em->persist($postUpvote);
            $status = Response::HTTP_CREATED;

            if (
                null !== $author = $data->getAuthor()
            ) {
                try {
                    $email = (new UserNotificationForumPostLikeEmail())
                        ->context([
                            'user' => $author,
                            'topic' => $data->getTopic(),
                            'post' => $data,
                        ])
                    ;

                    $userMailer->sendUser($email, $author);
                } catch (\Exception $e) {
                    $logger->error($e->getMessage());
                }
            }
        }

        $em->flush();

        return new Response(status: $status);
    }
}
