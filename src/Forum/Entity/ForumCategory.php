<?php

namespace App\Forum\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Core\Interfaces\LocaleableInterface;
use App\Core\Traits\LocaleableTrait;
use App\Forum\Controller\FreeWork\ForumCategory\Trace;
use App\Forum\Repository\ForumCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass=ForumCategoryRepository::class)
 * @Gedmo\Loggable()
 * @ApiResource(
 *      attributes={"order"={"position"="ASC","root"="ASC"}},
 *      normalizationContext={"groups"={"forum_category:get"}},
 *      collectionOperations={"get"},
 *      itemOperations={
 *          "get",
 *          "post_trace"={
 *              "method"="POST",
 *              "path"="/forum_categories/{slug}/trace",
 *              "security"="is_granted('ROLE_USER')",
 *              "controller"=Trace::class,
 *              "openapi_context"={
 *                  "summary"="Create a ForumTopicTrace resource for all ForumTopic resources of the ForumCategory resource.",
 *                  "description"="Create a ForumTopicTrace resource for all ForumTopic resources of the ForumCategory resource.",
 *              },
 *              "deserialize"=false,
 *          }
 *      },
 * )
 * @ApiFilter(PropertyFilter::class)
 */
class ForumCategory implements LocaleableInterface
{
    use LocaleableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"forum_category:get", "forum_topic:get", "forum_post:get:topic"})
     * @ApiProperty(identifier=false)
     * @Gedmo\Versioned()
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldId;

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
     * @ORM\ManyToOne(targetEntity=ForumCategory::class)
     * @ORM\JoinColumn(name="root", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?ForumCategory $root;

    /**
     * @ORM\ManyToOne(targetEntity=ForumCategory::class, inversedBy="children")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\SortableGroup()
     * @Gedmo\TreeParent()
     */
    private ?ForumCategory $parent;

    /**
     * @ORM\OneToMany(targetEntity=ForumCategory::class, mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"position"="ASC"})
     * @Groups({"forum_category:get"})
     */
    private Collection $children;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"forum_category:get", "forum_topic:get", "forum_post:get:topic"})
     * @Gedmo\Versioned()
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Gedmo\Slug(fields={"title"})
     * @Groups({"forum_category:get", "forum_topic:get", "forum_post:get:topic"})
     * @ApiProperty(identifier=true)
     * @Gedmo\Versioned()
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"forum_category:get"})
     * @Gedmo\Versioned()
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Groups({"forum_category:get"})
     */
    private ?string $metaTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Groups({"forum_category:get"})
     */
    private ?string $metaDescription;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(minMessage="generic.range.min", min="0")
     * @Gedmo\SortablePosition()
     * @Gedmo\Versioned()
     */
    private ?int $position;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"forum_category:get"})
     */
    private int $topicsCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"forum_category:get"})
     */
    private int $postsCount = 0;

    /**
     * @ORM\OneToOne(targetEntity=ForumPost::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Groups({"forum_category:get"})
     */
    private ?ForumPost $lastPost;

    /**
     * @ORM\OneToMany(targetEntity=ForumTopic::class, mappedBy="category", cascade={"persist", "remove"})
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $topics;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->topics = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getTopicsCount(): ?int
    {
        return $this->topicsCount;
    }

    public function setTopicsCount(int $topicsCount): self
    {
        $this->topicsCount = $topicsCount;

        return $this;
    }

    public function getPostsCount(): ?int
    {
        return $this->postsCount;
    }

    public function setPostsCount(int $postsCount): self
    {
        $this->postsCount = $postsCount;

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
     * @return Collection|ForumCategory[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
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

    public function getLastPost(): ?ForumPost
    {
        return $this->lastPost;
    }

    public function setLastPost(?ForumPost $lastPost): self
    {
        $this->lastPost = $lastPost;

        return $this;
    }

    /**
     * @return Collection|ForumTopic[]
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(ForumTopic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setCategory($this);
        }

        return $this;
    }

    public function removeTopic(ForumTopic $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getCategory() === $this) {
                $topic->setCategory(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getOldId(): ?int
    {
        return $this->oldId;
    }

    public function setOldId(?int $oldId): self
    {
        $this->oldId = $oldId;

        return $this;
    }

    public function __toString(): string
    {
        return $this->title ?: '';
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
}
