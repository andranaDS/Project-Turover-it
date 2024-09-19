<?php

namespace App\Forum\Controller\FreeWork\ForumPost;

use App\Forum\Entity\ForumPost;
use App\Forum\Repository\ForumPostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetPage
{
    public function __invoke(ForumPost $data, Request $request, ForumPostRepository $fpr, int $itemsPerPageDefault): Response
    {
        $itemsPerPage = (int) $request->query->get('itemsPerPage', (string) $itemsPerPageDefault);
        // get ForumPost root in order to always query on root ForumPost
        $root = $data->getRoot() ?: $data;

        if (null !== $topic = $data->getTopic()) {
            // count ForumPosts created/placed before this current
            $countBefore = $fpr->countBefore($topic, $root);
        } else {
            return new Response('no topic found', Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'page' => (int) ceil($countBefore / $itemsPerPage),
        ], Response::HTTP_OK);
    }
}
