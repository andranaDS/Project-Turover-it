<?php

namespace App\Messaging\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Mailer\Mailer;
use App\Messaging\Entity\Feed;
use App\Messaging\Entity\FeedUser;
use App\Messaging\Entity\Message;
use App\User\Email\UserNotificationMessagingNewMessageEmail;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SendNotificationMessageSubscriber implements EventSubscriberInterface
{
    private Mailer $userMailer;
    private LoggerInterface $logger;

    public function __construct(Mailer $userMailer, LoggerInterface $logger)
    {
        $this->userMailer = $userMailer;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', EventPriorities::POST_WRITE],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $object = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (
            !($object instanceof Message || $object instanceof Feed) ||
            Request::METHOD_POST !== $method ||
            !\in_array($event->getRequest()->get('_route'), ['api_messages_post_message_collection', 'api_feeds_post_collection'], true)
        ) {
            return;
        }

        if ($object instanceof Message) {
            $message = $object;
            $feed = $message->getFeed();
        } else {
            $feed = $object;
            $message = $feed->getMessages()->first();
        }

        if (null !== $feed) {
            $author = $message->getAuthor();
            foreach ($feed->getFeedUsers()->getValues() as $feedUser) {
                /** @var FeedUser $feedUser */
                $user = $feedUser->getUser();

                if ($user && $author && $author->getId() !== $user->getId()) {
                    try {
                        $email = (new UserNotificationMessagingNewMessageEmail())
                            ->context([
                                'user' => $user,
                                'feed' => $feed,
                            ])
                        ;

                        $this->userMailer->sendUser($email, $user);
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
            }
        }
    }
}
