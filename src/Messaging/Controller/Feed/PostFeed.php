<?php

namespace App\Messaging\Controller\Feed;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Messaging\Entity\Feed;
use App\Messaging\Entity\FeedUser;
use App\Messaging\Manager\MessageManager;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

final class PostFeed
{
    public function __invoke(Request $request, Security $security, EntityManagerInterface $em, ValidatorInterface $validator, MessageManager $messageManager): Feed
    {
        $feed = null;
        /** @var ?User $author */
        $author = $security->getUser();
        $uploadedFile = $request->files->get('documentFile');
        $receiverId = $request->request->get('user');
        $contentHtml = $request->request->get('contentHtml');
        $contentJson = $request->request->get('contentJson');

        if (null !== $receiver = $em->getRepository(User::class)->findOneById($receiverId)) {
            $feed = $em->getRepository(Feed::class)->findOneFeedBetween($author, $receiver);
        }

        if (null === $feed) {
            $feed = new Feed();
            $authorFeedUser = (new FeedUser())
                ->setFeed($feed)
                ->setUser($author)
            ;
            $receiverFeedUser = (new FeedUser())
                ->setFeed($feed)
                ->setUser($receiver)
            ;

            $validator->validate($receiverFeedUser, ['groups' => ['Default', 'feed:post']]);

            $em->persist($feed);
            $em->persist($authorFeedUser);
            $em->persist($receiverFeedUser);
        }

        $messageManager->postMessage($author, $feed, $uploadedFile, $contentHtml, $contentJson);

        return $feed;
    }
}
