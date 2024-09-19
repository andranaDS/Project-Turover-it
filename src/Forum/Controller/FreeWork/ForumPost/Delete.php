<?php

namespace App\Forum\Controller\FreeWork\ForumPost;

use App\Forum\Entity\ForumPost;
use App\Forum\Manager\ForumPostManager;
use Symfony\Component\HttpFoundation\Response;

class Delete
{
    public function __invoke(ForumPost $data, ForumPostManager $fpm): Response
    {
        $fpm->delete($data);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
