<?php

namespace App\Blog\Controller\FreeWork\BlogPost;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostUpvote;
use App\User\Contracts\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class Upvote
{
    public function __invoke(Security $security, EntityManagerInterface $em, BlogPost $data): Response
    {
        if ((null === $user = $security->getUser()) || !$user instanceof UserInterface) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        if (null !== $postUpvote = $em->getRepository(BlogPostUpvote::class)->findOneBy([
            'post' => $data,
            'user' => $user,
        ])) {
            $em->remove($postUpvote);
            $status = Response::HTTP_NO_CONTENT;
        } else {
            $postUpvote = (new BlogPostUpvote())
                ->setUser($user)
                ->setPost($data)
            ;

            $em->persist($postUpvote);
            $status = Response::HTTP_CREATED;
        }

        $em->flush();

        return new Response(status: $status);
    }
}
