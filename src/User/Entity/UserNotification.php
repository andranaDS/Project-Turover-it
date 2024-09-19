<?php

namespace App\User\Entity;

use App\User\Repository\UserNotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserNotificationRepository::class)
 */
class UserNotification
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * marketingNewsletter - true if the user consent to receive emails.
     *
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:post", "user:patch:notifications"})
     */
    private bool $marketingNewsletter = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:notifications"})
     */
    private bool $forumTopicReply = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:notifications"})
     */
    private bool $forumTopicFavorite = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:notifications"})
     */
    private bool $forumPostReply = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:notifications"})
     */
    private bool $forumPostLike = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:notifications"})
     */
    private bool $messagingNewMessage = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForumTopicReply(): ?bool
    {
        return $this->forumTopicReply;
    }

    public function setForumTopicReply(bool $forumTopicReply): self
    {
        $this->forumTopicReply = $forumTopicReply;

        return $this;
    }

    public function getForumTopicFavorite(): ?bool
    {
        return $this->forumTopicFavorite;
    }

    public function setForumTopicFavorite(bool $forumTopicFavorite): self
    {
        $this->forumTopicFavorite = $forumTopicFavorite;

        return $this;
    }

    public function getForumPostReply(): ?bool
    {
        return $this->forumPostReply;
    }

    public function setForumPostReply(bool $forumPostReply): self
    {
        $this->forumPostReply = $forumPostReply;

        return $this;
    }

    public function getForumPostLike(): ?bool
    {
        return $this->forumPostLike;
    }

    public function setForumPostLike(bool $forumPostLike): self
    {
        $this->forumPostLike = $forumPostLike;

        return $this;
    }

    public function getMessagingNewMessage(): ?bool
    {
        return $this->messagingNewMessage;
    }

    public function setMessagingNewMessage(bool $messagingNewMessage): self
    {
        $this->messagingNewMessage = $messagingNewMessage;

        return $this;
    }

    public function getMarketingNewsletter(): ?bool
    {
        return $this->marketingNewsletter;
    }

    public function setMarketingNewsletter(bool $marketingNewsletter): self
    {
        $this->marketingNewsletter = $marketingNewsletter;

        return $this;
    }
}
