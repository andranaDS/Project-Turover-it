<?php

namespace App\Forum\Controller\FreeWork\ForumPost;

use App\Forum\Entity\ForumPost;
use App\Forum\Repository\ForumPostRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetItem
{
    public function __invoke(ForumPost $data, ForumPostRepository $postRepository): ForumPost
    {
        if ((null === $postId = $data->getId()) || (null === $post = $postRepository->findOneByIdWithChildren($postId))) {
            throw new NotFoundHttpException('Not found');
        }

        return $post;
    }
}
