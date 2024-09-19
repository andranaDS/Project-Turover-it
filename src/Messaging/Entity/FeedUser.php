<?php

namespace App\Messaging\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Messaging\Repository\FeedUserRepository;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FeedUserRepository::class)
 * @ApiResource(
 *      normalizationContext={
 *          "groups"={"feed_user:get"}
 *      },
 *      itemOperations={"get"={"security"="object.user == user"}},
 *      collectionOperations={"get"={"security"="object.user == user"}}
 * )
 */
class FeedUser
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"feed_user:get", "feed:get:collection", "feed:get:item", "feed:get"})
     */
    private bool $favorite = false;

    /**
     * @ORM\ManyToOne(targetEntity=Feed::class, inversedBy="feedUsers")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"feed_user:get"})
     */
    private ?Feed $feed;

    /**
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="feedUsers")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="generic.not_blank", groups={"feed:post"})
     * @Groups({"feed:get:collection", "feed:get:item", "feed:get"})
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"feed_user:get", "feed:get:collection"})
     */
    protected ?\DateTime $viewAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFavorite(): ?bool
    {
        return $this->favorite;
    }

    public function setFavorite(bool $favorite): self
    {
        $this->favorite = $favorite;

        return $this;
    }

    public function getViewAt(): ?\DateTime
    {
        return $this->viewAt;
    }

    public function setViewAt(?\DateTime $viewAt): self
    {
        $this->viewAt = $viewAt;

        return $this;
    }

    public function getFeed(): ?Feed
    {
        return $this->feed;
    }

    public function setFeed(?Feed $feed): self
    {
        $this->feed = $feed;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @Groups({"feed:get:collection"})
     */
    public function hasUnseenMessages(): bool
    {
        if (!$this->feed) {
            return false;
        }

        $lastMessage = $this->feed->getLastMessage();

        if ($lastMessage && $lastMessage->getCreatedAt() > $this->viewAt) {
            return true;
        }

        return false;
    }
}
