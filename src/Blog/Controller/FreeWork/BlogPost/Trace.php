<?php

namespace App\Blog\Controller\FreeWork\BlogPost;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostTrace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Trace
{
    public function __invoke(BlogPost $data, EntityManagerInterface $em): JsonResponse
    {
        $blogPostTrace = (new BlogPostTrace())
            ->setPost($data)
        ;

        $em->persist($blogPostTrace);
        $em->flush();

        return new JsonResponse([], Response::HTTP_CREATED);
    }
}
