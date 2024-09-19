<?php

namespace App\Messaging\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Messaging\Entity\Feed;
use App\Messaging\Entity\FeedUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class UpdateViewAtSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['process', EventPriorities::PRE_READ],
            ],
        ];
    }

    public function process(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ('api_feeds_get_item' !== $request->attributes->get('_route')) {
            return;
        }

        $feedId = $request->attributes->get('id');
        /** @var Feed $feed */
        $feed = $this->em->getRepository(Feed::class)->findOneById($feedId);

        if (null !== $feed) {
            $feedUsers = $feed->getFeedUsers()->getValues();
            foreach ($feedUsers as $feedUser) {
                /** @var FeedUser $feedUser */
                if ($feedUser->getUser() === $this->security->getUser()) {
                    $feedUser->setViewAt(new \DateTime());
                    $this->em->flush();

                    return;
                }
            }
        }
    }
}
