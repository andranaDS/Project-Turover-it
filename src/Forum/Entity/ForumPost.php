<?php

namespace App\Forum\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Annotation\SensitiveContentEntity;
use App\Core\Annotation\SensitiveContentProperty;
use App\Core\Util\Strings;
use App\Core\Validator as CoreAssert;
use App\Forum\Controller\FreeWork\ForumPost\Delete;
use App\Forum\Controller\FreeWork\ForumPost\GetItem;
use App\Forum\Controller\FreeWork\ForumPost\GetPage;
use App\Forum\Controller\FreeWork\ForumPost\GetTopicReplies;
use App\Forum\Controller\FreeWork\ForumPost\Moderate;
use App\Forum\Controller\FreeWork\ForumPost\Upvote;
use App\Forum\Repository\ForumPostRepository;
use App\User\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ForumPostRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"created_at"}),
 *     @ORM\Index(columns={"deleted_at"}),
 * })
 * @Gedmo\Tree(type="nested")
 * @ApiResource(
 *      attributes={"order"={"createdAt"="DESC"}},
 *      normalizationContext={
 *          "groups"={"forum_post:get", "forum_post:get:parent", "forum_post:get:topic"},
 *          "enable_max_depth"=true,
 *      },
 *      collectionOperations={
 *          "get",
 *          "post"={
 *              "security"="is_granted('ROLE_USER') and user.banned == 0",
 *              "denormalization_context"={"groups"={"forum_post:post"}},
 *              "validation_groups"={"forum_post:post"},
 *          },
 *          "get_topic_replies"={
 *              "method"="GET",
 *              "path"="/forum_topics/{slug}/replies",
 *              "normalization_context"={"groups"={"forum_post:get", "forum_post:get:children"}},
 *              "controller"=GetTopicReplies::class,
 *              "openapi_context"={
 *                  "summary"="Retrieves the ForumPost's collection resources of ForumTopic resource.",
 *                  "description"="Retrieves the ForumPost's collection resources of ForumTopic resource.",
 *                  "tags"={"ForumTopic"}
 *              },
 *          },
 *      },
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"forum_post:get", "forum_post:get:children", "forum_post:get:topic"}},
 *              "controller"=GetItem::class,
 *          },
 *          "get_page"={
 *              "method"="GET",
 *              "controller"=GetPage::class,
 *              "path"="/forum_posts/{id}/page",
 *              "deserialize"=false,
 *          },
 *          "patch_upvote"={
 *              "security"="is_granted('ROLE_USER')",
 *              "method"="PATCH",
 *              "path"="/forum_posts/{id}/upvote",
 *              "controller"=Upvote::class,
 *              "denormalization_context"={"groups"={"forum_post:patch_upvote"}},
 *              "deserialize"=false,
 *          },
 *          "delete"={
 *              "security"="object.author == user",
 *              "controller"=Delete::class,
 *          },
 *          "put"={
 *              "security"="object.author == user",
 *              "denormalization_context"={"groups"={"forum_post:put"}},
 *              "validation_groups"={"forum_post:put"},
 *          },
 *          "patch_moderate"={
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "method"="PATCH",
 *              "path"="/forum_posts/{id}/moderate",
 *              "controller"=Moderate::class,
 *              "denormalization_context"={"groups"={"forum_post:patch_moderate"}},
 *              "deserialize"=false,
 *              "openapi_context"={
 *                  "summary"="Moderate a ForumPost resource.",
 *              },
 *          }
 *      },
 *      subresourceOperations={
 *          "api_users_forum_posts_get_subresource"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"forum_post:get", "forum_post:get:parent", "forum_post:get:topic"}},
 *          },
 *          "openapi_context"={
 *              "summary"="Retrieves the ForumPost's collection resources of User logged resource.",
 *              "description"="Retrieves the ForumPost's collection resources of User logged resource.",
 *              "tags"={"User"}
 *         },
 *      }
 * )
 * @SensitiveContentEntity()
 */
class ForumPost
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"forum_post:get", "forum_category:get", "forum_topic:get", "forum_post:user_posts"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldId;

    /**
     * @ORM\ManyToOne(targetEntity=ForumTopic::class, inversedBy="posts")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"forum_post:get:topic", "forum_post:post", "forum_category:get", "forum_post:user_posts"})
     * @Assert\NotNull(message="generic.not_null", groups={"forum_post:post", "forum_topic:post"})
     * @MaxDepth(1)
     */
    private ?ForumTopic $topic;

    /**
     * @Gedmo\TreeLeft()
     * @ORM\Column(name="lft", type="integer")
     */
    private ?int $left;

    /**
     * @Gedmo\TreeLevel()
     * @ORM\Column(name="lvl", type="integer")
     */
    private ?int $level;

    /**
     * @Gedmo\TreeRight()
     * @ORM\Column(name="rgt", type="integer")
     */
    private ?int $right;

    /**
     * @Gedmo\TreeRoot()
     * @ORM\ManyToOne(targetEntity=ForumPost::class)
     * @ORM\JoinColumn(name="root", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?ForumPost $root;

    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity=ForumPost::class, inversedBy="children")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"forum_post:get:parent", "forum_post:post", "forum_post:user_posts"})
     * @MaxDepth(1)
     */
    private ?ForumPost $parent = null;

    /**
     * @ORM\OneToMany(targetEntity=ForumPost::class, mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt"="ASC"})
     * @Groups({"forum_post:get:children"})
     */
    private Collection $children;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="forumPosts")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"forum_post:get", "forum_category:get", "forum_topic:get", "forum_post:user_posts"})
     * @Gedmo\Blameable(on="create")
     */
    public ?User $author;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"forum_post:get", "forum_post:post", "forum_topic:get", "forum_topic:post", "forum_post:put", "forum_post:user_posts"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"forum_post:post", "forum_topic:post", "forum_post:put"})
     * @CoreAssert\ForbiddenContent(groups={"forum_post:post", "forum_topic:post", "forum_post:put"})
     * @CoreAssert\SanitizeContent(groups={"forum_post:post", "forum_topic:post", "forum_post:put"})
     */
    private ?string $contentHtml;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"forum_post:post", "forum_topic:post", "forum_post:put"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"forum_post:post", "forum_topic:post", "forum_post:put"})
     * @Assert\Json(message="generic.json", groups={"forum_post:post", "forum_topic:post", "forum_post:put"})
     */
    private ?string $contentJson;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @SensitiveContentProperty()
     */
    private ?string $content;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\IpTraceable(on="create")
     */
    private ?string $ip;

    /**
     * @ORM\OneToMany(targetEntity=ForumPostUpvote::class, mappedBy="post", cascade={"persist", "remove"})
     */
    private Collection $upvotes;

    /**
     * @ORM\OneToMany(targetEntity=ForumPostReport::class, mappedBy="post", cascade={"persist", "remove"})
     */
    private Collection $reports;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"forum_post:get", "forum_topic:get", "forum_post:user_posts"})
     */
    private int $upvotesCount = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected ?bool $hidden = false;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"forum_post:get", "forum_category:get", "forum_topic:get", "forum_post:user_posts"})
     */
    protected \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="change", field={"contentHtml", "contentJson"})
     * @ORM\Column(type="datetime")
     * @Groups({"forum_post:get", "forum_category:get", "forum_topic:get"})
     */
    protected \DateTimeInterface $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $deletedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $moderatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->children = new ArrayCollection();
        $this->upvotes = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLeft(): ?int
    {
        return $this->left;
    }

    public function setLeft(int $left): self
    {
        $this->left = $left;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getRight(): ?int
    {
        return $this->right;
    }

    public function setRight(int $right): self
    {
        $this->right = $right;

        return $this;
    }

    public function getContentHtml(): ?string
    {
        return $this->contentHtml;
    }

    public function setContentHtml(?string $contentHtml): self
    {
        $this->contentHtml = $contentHtml;

        return $this;
    }

    public function getContentJson(): ?string
    {
        return $this->contentJson;
    }

    public function setContentJson(?string $contentJson): self
    {
        $this->contentJson = $contentJson;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTopic(): ?ForumTopic
    {
        return $this->topic;
    }

    public function setTopic(?ForumTopic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getRoot(): ?self
    {
        return $this->root;
    }

    public function setRoot(?self $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|ForumPost[]
     */
    public function getChildren(): Collection
    {
        $children = clone $this->children;
        $criteria = new Criteria();

        return $children->matching(
            $criteria
                ->where($criteria->expr()->eq('hidden', false))
        );
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getUpvotesCount(): ?int
    {
        return $this->upvotesCount;
    }

    public function setUpvotesCount(int $upvotesCount): self
    {
        $this->upvotesCount = $upvotesCount;

        return $this;
    }

    /**
     * @return Collection|ForumPostUpvote[]
     */
    public function getUpvotes(): Collection
    {
        return $this->upvotes;
    }

    public function addUpvote(ForumPostUpvote $upvote): self
    {
        if (!$this->upvotes->contains($upvote)) {
            $this->upvotes[] = $upvote;
            $upvote->setPost($this);
        }

        return $this;
    }

    public function removeUpvote(ForumPostUpvote $upvote): self
    {
        if ($this->upvotes->removeElement($upvote)) {
            // set the owning side to null (unless already changed)
            if ($upvote->getPost() === $this) {
                $upvote->setPost(null);
            }
        }

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @Groups({"forum_post:get", "forum_category:get", "forum_topic:get"})
     */
    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }

    public function getModeratedAt(): ?\DateTimeInterface
    {
        return $this->moderatedAt;
    }

    public function setModeratedAt(?\DateTimeInterface $moderatedAt): self
    {
        $this->moderatedAt = $moderatedAt;

        return $this;
    }

    /**
     * @Groups({"forum_post:get", "forum_category:get", "forum_topic:get", "forum_post:user_posts"})
     */
    public function isModerated(): bool
    {
        return !(null === $this->moderatedAt);
    }

    /**
     * @return Collection|ForumPostReport[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(ForumPostReport $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setPost($this);
        }

        return $this;
    }

    public function removeReport(ForumPostReport $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getPost() === $this) {
                $report->setPost(null);
            }
        }

        return $this;
    }

    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getOldId(): ?int
    {
        return $this->oldId;
    }

    public function setOldId(?int $oldId): self
    {
        $this->oldId = $oldId;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function __toString(): string
    {
        return html_entity_decode(Strings::substrToLength((string) $this->content, 60), \ENT_QUOTES);
    }
}
