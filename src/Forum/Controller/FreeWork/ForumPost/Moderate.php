<?php

namespace App\Forum\Controller\FreeWork\ForumPost;

use App\Forum\Entity\ForumPost;

class Moderate
{
    public function __invoke(ForumPost $data): ForumPost
    {
        $data
            ->setContentJson(null)
            ->setContentHtml(null)
            ->setModeratedAt(new \DateTime())
        ;

        return $data;
    }
}
