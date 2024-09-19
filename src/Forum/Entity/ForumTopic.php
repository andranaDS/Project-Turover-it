<?php

namespace App\Forum\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Core\Doctrine\Filter\MultipleFieldsSearchFilter;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Validator as CoreAssert;
use App\Forum\Controller\FreeWork\ForumTopic\Favorite;
use App\Forum\Controller\FreeWork\ForumTopic\Trace;
use App\Forum\Repository\ForumTopicRepository;
use App\User\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      attributes={"order"={"lastPost.createdAt"="DESC"}},
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"forum_topic:get", "forum_topic:get:item"}},
 *          },
 *          "patch_favorite"={
 *              "security"="is_granted('ROLE_USER')",
 *              "method"="PATCH",
 *              "path"="/forum_topics/{slug}/favorite",
 *              "controller"=Favorite::class,
 *              "deserialize"=false,
 *          },
 *          "post_trace"={
 *              "method"="POST",
 *              "path"="/forum_topics/{slug}/trace",
 *              "controller"=Trace::class,
 *              "openapi_context"={
 *                  "summary"="Create a ForumTopicTrace resource for the ForumTopic resource.",
 *                  "description"="Create a ForumTopicTrace resource for the ForumTopic resource.",
 *              },
 *              "deserialize"=false,
 *          }
 *      },
 *      collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"forum_topic:get", "forum_topic:get:collection"}},
 *              "fetch_partial"=true,
 *          },
 *          "get_search"={
 *              "method"="GET",
 *              "path"="/forum_topics/search",
 *              "normalization_context"={"groups"={"forum_topic:get", "forum_topic:get:collection_search"}},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of ForumTopic resources with posts matching the query.",
 *                  "description"="Retrieves the collection of ForumTopic resources with posts matching the query.",
 *              },
 *          },
 *           "get_participations"={
 *              "method"="GET",
 *              "path"="/forum_topics/participations",
 *              "normalization_context"={"groups"={"forum_topic:get", "forum_topic:get:collection"}},
 *              "security"="is_granted('ROLE_USER')",
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of ForumTopic resources with posts matching the query.",
 *                  "description"="Retrieves the collection of ForumTopic resources with posts matching the query.",
 *              },
 *          },
 *          "get_favorites"={
 *              "security"="is_granted('ROLE_USER')",
 *              "method"="GET",
 *              "path"="/forum_topics/favorites",
 *              "normalization_context"={"groups"={"forum_topic:get", "forum_topic:get:collection"}},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of favorite ForumTopic resources.",
 *                  "description"="Retrieves the collection of favorite ForumTopic resources.",
 *              },
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"forum_topic:get", "forum_topic:get:item"}},
 *              "denormalization_context"={"groups"={"forum_topic:post"}},
 *              "validation_groups"={"forum_topic:post"},
 *          },
 *     },
 *     subresourceOperations={
 *          "api_forum_categories_topics_get_subresource"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"forum_topic:get", "forum_topic:get:collection"}},
 *              "openapi_context"={
 *                  "summary"="ROO etrieves the ForumTopic's collection resources of ForumCategory resource.",
 *                  "description"="Retrieves the ForumTopic's collection resources of ForumCategory resource.",
 *                  "tags"={"ForumCategory"}
 *               },
 *          }
 *     }
 * )
 * @ORM\Table(indexes={@ORM\Index(columns={"title"})})
 * @ORM\Entity(repositoryClass=ForumTopicRepository::class)
 * @ApiFilter(SearchFilter::class, properties={"category.slug"="exact", "title"="ipartial"})
 * @ApiFilter(OrderFilter::class, properties={"lastPost.createdAt"="DESC"})
 * @ApiFilter(MultipleFieldsSearchFilter::class, properties={"title", "posts.content"})
 */
class ForumTopic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"forum_topic:get", "forum_category:get", "forum_post:get:topic"})
     * @ApiProperty(identifier=false)
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldId;

    /**
     * @ORM\ManyToOne(targetEntity=ForumCategory::class, inversedBy="topics")
     * @ORM\JoinColumn()
     * @Groups({"forum_topic:get", "forum_topic:post", "forum_post:get:topic"})
     * @Assert\NotNull(message="generic.not_null", groups={"forum_topic:post"})
     */
    private ?ForumCategory $category;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"forum_topic:post"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"forum_topic:post"})
     * @CoreAssert\ForbiddenContent(groups={"forum_topic:post"})
     * @CoreAssert\SanitizeContent(groups={"forum_topic:post"})
     * @Groups({"forum_topic:get", "forum_topic:post", "forum_category:get", "forum_post:get:topic"})
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"forum_topic:post"})
     * @Gedmo\Slug(fields={"title"})
     * @Groups({"forum_topic:get", "forum_category:get", "forum_post:get:topic"})
     * @ApiProperty(identifier=true)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Groups({"forum_topic:get"})
     */
    private ?string $metaTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Groups({"forum_topic:get"})
     */
    private ?string $metaDescription;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"forum_topic:get"})
     */
    private bool $pinned = false;

    /**
     * @ORM\OneToOne(targetEntity=ForumPost::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"forum_topic:get:item"})
     */
    private ?ForumPost $initialPost;

    /**
     * @ORM\OneToOne(targetEntity=ForumPost::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"forum_topic:get"})
     */
    private ?ForumPost $lastPost;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"forum_topic:get"})
     * @Gedmo\Blameable(on="create")
     */
    private ?User $author;

    /**
     * @ORM\OneToMany(targetEntity=ForumPost::class, mappedBy="topic", cascade={"persist", "remove"})
     * @OrderBy({"createdAt"="ASC"})
     * @Groups({"forum_topic:post", "forum_topic:get:collection_search"})
     * @Assert\Count(minMessage="generic.count.min", min="1", groups={"forum_topic:post"})
     * @Assert\Valid(groups={"forum_topic:post"})
     */
    private Collection $posts;

    /**
     * @ORM\OneToMany(targetEntity=ForumTopicFavorite::class, mappedBy="topic", cascade={"persist", "remove"})
     */
    private Collection $favorites;

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
        $this->posts = new ArrayCollection();
        $this->favorites = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPinned(): ?bool
    {
        return $this->pinned;
    }

    public function setPinned(bool $pinned): self
    {
        $this->pinned = $pinned;

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

    public function getCategory(): ?ForumCategory
    {
        return $this->category;
    }

    public function setCategory(?ForumCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getInitialPost(): ?ForumPost
    {
        return $this->initialPost;
    }

    public function setInitialPost(?ForumPost $initialPost): self
    {
        $this->initialPost = $initialPost;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|ForumPost[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function setPosts(Collection $posts): self
    {
        $this->posts = $posts;

        return $this;
    }

    public function addPost(ForumPost $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setTopic($this);
        }

        return $this;
    }

    public function removePost(ForumPost $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getTopic() === $this) {
                $post->setTopic(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(ForumTopicFavorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setTopic($this);
        }

        return $this;
    }

    public function removeFavorite(ForumTopicFavorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getTopic() === $this) {
                $favorite->setTopic(null);
            }
        }

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
        return (string) $this->title;
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
