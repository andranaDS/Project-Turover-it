<?php

namespace App\Forum\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Mailer\Mailer;
use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumTopicFavorite;
use App\User\Email\UserNotificationForumPostReplyEmail;
use App\User\Email\UserNotificationForumTopicFavoriteEmail;
use App\User\Email\UserNotificationForumTopicReplyEmail;
use App\User\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SendNotificationForumPostSubscriber implements EventSubscriberInterface
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
        $post = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $route = $event->getRequest()->get('_route');

        if (!$post instanceof ForumPost || Request::METHOD_POST !== $method || 'api_forum_posts_post_collection' !== $route) {
            return;
        }

        // vars
        $postAuthor = $post->getAuthor();
        $topic = $post->getTopic();

        if (null === $topic) {
            return;
        }

        if (null !== $topicAuthor = $topic->getAuthor()) {
            // UserNotification::forumTopicFavorite
            $topicFavoriteUsers = array_map(static function (ForumTopicFavorite $forumTopicFavorite) {
                return $forumTopicFavorite->getUser();
            }, $topic->getFavorites()->getValues());

            $usersReceivedFavoriteNotification = [];
            foreach ($topicFavoriteUsers as $user) {
                $userReceivedFavoriteNotification = null;
                /** @var User $user */
                if ($postAuthor !== $user) {
                    try {
                        $email = (new UserNotificationForumTopicFavoriteEmail())
                            ->context([
                                'user' => $user,
                                'topic' => $topic,
                            ])
                        ;

                        $userReceivedFavoriteNotification = $this->userMailer->sendUser($email, $user);
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }

                    // used to not sent Topic Favorite + Reply emails
                    if ($userReceivedFavoriteNotification instanceof User) {
                        $usersReceivedFavoriteNotification[] = $user;
                    }
                }
            }

            // UserNotification::forumTopicReply
            if (
                $postAuthor !== $topicAuthor
                && !\in_array($topicAuthor, $usersReceivedFavoriteNotification, true)
            ) {
                try {
                    $email = (new UserNotificationForumTopicReplyEmail())
                        ->context([
                            'user' => $topicAuthor,
                            'topic' => $topic,
                        ])
                    ;
                    $this->userMailer->sendUser($email, $topicAuthor);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }

        // UserNotification::forumPostReply
        if (
            (null !== $parent = $post->getParent())
            && (null !== $parentAuthor = $parent->getAuthor())
            && $postAuthor !== $parentAuthor
        ) {
            try {
                $email = (new UserNotificationForumPostReplyEmail())
                    ->context([
                        'user' => $parentAuthor,
                        'topic' => $parent->getTopic(),
                        'post' => $parent,
                    ])
                ;

                $this->userMailer->sendUser($email, $parentAuthor);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
