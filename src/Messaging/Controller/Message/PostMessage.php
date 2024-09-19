<?php

namespace App\Messaging\Controller\Message;

use App\Messaging\Entity\Feed;
use App\Messaging\Entity\Message;
use App\Messaging\Manager\MessageManager;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

final class PostMessage
{
    public function __invoke(Request $request, Feed $feed, Security $security, EntityManagerInterface $em, MessageManager $messageManager): Message
    {
        /** @var ?User $user */
        $user = $security->getUser();

        if (null === $user || !$feed->hasUser($user)) {
            throw new UnauthorizedHttpException('unauthorized');
        }

        $uploadedFile = $request->files->get('documentFile');
        $contentHtml = $request->request->get('contentHtml');
        $contentJson = $request->request->get('contentJson');

        return $messageManager->postMessage($user, $feed, $uploadedFile, $contentHtml, $contentJson);
    }
}
