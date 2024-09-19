<?php

namespace App\Messaging\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Core\Doctrine\Filter\MultipleFieldsSearchFilter;
use App\JobPosting\Entity\Application;
use App\Messaging\Controller\Feed\GetCollectionOrderFavorite;
use App\Messaging\Controller\Feed\GetCollectionOrderUnread;
use App\Messaging\Controller\Feed\GetItem;
use App\Messaging\Controller\Feed\GetUnreadCount;
use App\Messaging\Controller\Feed\PostFeed;
use App\Messaging\Controller\Feed\PutFavoriteItem;
use App\Messaging\Repository\FeedRepository;
use App\User\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FeedRepository::class)
 * @ApiResource(
 *      attributes={"order"={"lastMessage.createdAt"="DESC"}},
 *      normalizationContext={
 *          "groups"={"feed:get"}
 *      },
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"feed:get"}},
 *              "method"="GET",
 *              "controller"=GetItem::class,
 *          },
 *          "put_favorite"={
 *              "normalization_context"={"groups"={"feed:get", "feed:get:item"}},
 *              "method"="PUT",
 *              "path"="/feeds/{id}/favorite",
 *              "controller"=PutFavoriteItem::class,
 *              "deserialize"=false,
 *          }
 *      },
 *      collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"feed:get", "feed:get:collection"}},
 *          },
 *          "post"={
 *              "method"="POST",
 *              "path"="/feeds",
 *              "controller"=PostFeed::class,
 *              "security"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"feed:get", "feed:get:item"}},
 *              "denormalization_context"={"groups"={"feed:post"}},
 *              "validation_groups"={"feed:post"},
 *              "deserialize"=false,
 *              "openapi_context"={
 *                  "summary"="Add a Feed.",
 *                  "description"="Add a Feed.",
 *                  "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "documentFile"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *          },
 *          "get_order_by_unread_messages"={
 *              "security"="is_granted('ROLE_USER')",
 *              "method"="GET",
 *              "path"="/feeds/order/unread",
 *              "controller"=GetCollectionOrderUnread::class,
 *              "normalization_context"={"groups"={"feed:get", "feed:get:collection"}},
 *          },
 *          "get_order_by_favorite_messages"={
 *              "security"="is_granted('ROLE_USER')",
 *              "method"="GET",
 *              "path"="/feeds/order/favorite",
 *              "controller"=GetCollectionOrderFavorite::class,
 *              "normalization_context"={"groups"={"feed:get", "feed:get:collection"}},
 *          },
 *          "get_unread_count"={
 *              "method"="GET",
 *              "path"="/feeds/unread/count",
 *              "controller"=GetUnreadCount::class,
 *              "security"="is_granted('ROLE_USER')",
 *          },
 *     }
 * )
 * @ApiFilter(MultipleFieldsSearchFilter::class, properties={"messages.content", "messages.author.nickname"})
 * @ApiFilter(OrderFilter::class, properties={"createdAt", "updatedAt"})
 */
class Feed
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"feed:get", "feed_user:get", "feed:get:item"})
     */
    private ?int $id = null;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="feed", cascade={"persist", "remove"})
     * @OrderBy({"createdAt" = "DESC"})
     * @Groups({"feed:get:item"})
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $messages;

    /**
     * @ORM\OneToOne(targetEntity=Message::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"feed:get:collection"})
     */
    private ?Message $lastMessage;

    /**
     * @ORM\OneToMany(targetEntity=FeedUser::class, mappedBy="feed", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Count(minMessage="generic.count.min", min="2")
     */
    private Collection $feedUsers;

    /**
     * @ORM\ManyToOne(targetEntity=Application::class)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @Groups({"feed:get:collection", "feed:get:item", "feed:get"})
     */
    public ?Application $application;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"feed:get", "feed_user:get", "feed:get:item"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups({"feed:get", "feed_user:get", "feed:get:item"})
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->feedUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setFeed($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getFeed() === $this) {
                $message->setFeed(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FeedUser[]
     */
    public function getFeedUsers(): Collection
    {
        return $this->feedUsers;
    }

    public function addFeedUser(FeedUser $feedUser): self
    {
        if (!$this->feedUsers->contains($feedUser)) {
            $this->feedUsers[] = $feedUser;
            $feedUser->setFeed($this);
        }

        return $this;
    }

    public function removeFeedUser(FeedUser $feedUser): self
    {
        if ($this->feedUsers->removeElement($feedUser)) {
            // set the owning side to null (unless already changed)
            if ($feedUser->getFeed() === $this) {
                $feedUser->setFeed(null);
            }
        }

        return $this;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Message $lastMessage): void
    {
        $this->lastMessage = $lastMessage;
    }

    public function hasUser(User $user): bool
    {
        $users = array_map(function (FeedUser $feedUser) {
            return $feedUser->getUser();
        }, $this->feedUsers->getValues());

        return \in_array($user, $users, true);
    }

    public function getFeedUser(User $user): ?FeedUser
    {
        $feedUser = null;
        foreach ($this->feedUsers->getValues() as $feedUserValue) {
            /** @var FeedUser $feedUserValue */
            if ($user === $feedUserValue->getUser()) {
                $feedUser = $feedUserValue;
            }
        }

        return $feedUser;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }
}
