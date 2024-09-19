<?php

namespace App\Forum\Controller\FreeWork\ForumPost;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Forum\Entity\ForumTopic;
use App\Forum\Repository\ForumPostRepository;
use Symfony\Component\HttpFoundation\Request;

class GetTopicReplies
{
    public function __invoke(ForumTopic $topic, Request $request, ForumPostRepository $postRepository, int $itemsPerPageDefault): Paginator
    {
        $page = (int) $request->query->get('page', '1');
        $itemsPerPage = (int) $request->query->get('itemsPerPage', (string) $itemsPerPageDefault);

        return $postRepository->getTopicReplies($topic, $page, $itemsPerPage);
    }
}
