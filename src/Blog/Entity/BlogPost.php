<?php

namespace App\Blog\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Blog\Controller\FreeWork\BlogPost\Trace;
use App\Blog\Controller\FreeWork\BlogPost\Upvote;
use App\Blog\Repository\BlogPostRepository;
use App\Core\Annotation\ApiThumbnailUrls;
use App\Core\Doctrine\Filter\MultipleFieldsSearchFilter;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Interfaces\LocaleableInterface;
use App\Core\Traits\LocaleableTrait;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 * @Gedmo\Loggable()
 * @Vich\Uploadable()
 * @ApiResource(
 *      attributes={"order"={"publishedAt"="DESC"}},
 *      normalizationContext={
 *          "groups"={"blog_post:get"}
 *      },
 *      itemOperations={
 *          "freework_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"blog_post:get", "blog_post:get:item"}},
 *          },
 *          "freework_post_trace"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "path"="/blog_posts/{slug}/trace",
 *              "controller"=Trace::class,
 *              "openapi_context"={
 *                  "summary"="Create a BlogPostTrace resource for the BlogPost resource.",
 *              },
 *              "deserialize"=false,
 *              "validate"=false,
 *          },
 *          "freework_patch_upvote"={
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_USER')",
 *              "path"="/blog_posts/{slug}/upvote",
 *              "controller"=Upvote::class,
 *              "denormalization_context"={"groups"={"blog_post:patch_upvote"}},
 *              "deserialize"=false,
 *          },
 *      },
 *      collectionOperations={
 *          "freework_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"blog_post:get", "blog_post:get:collection"}},
 *          },
 *          "freework_get_most_viewed"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"blog_post:get"}},
 *              "path"="/blog_posts/most_viewed",
 *              "normalization_context"={"groups"={"blog_post:get", "blog_post:get:collection"}},
 *              "cache_headers"={"max_age"= 0, "shared_max_age"= 7200},
 *          },
 *      },
 *      subresourceOperations={
 *          "api_blog_categories_posts_get_subresource"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"blog_post:get"}},
 *          }
 *      }
 * )
 * @ApiFilter(SearchFilter::class, properties={"category.slug"="exact", "tags.slug"="exact"})
 * @ApiFilter(OrderFilter::class, properties={"publishedAt"="DESC"})
 * @ApiFilter(MultipleFieldsSearchFilter::class, properties={"title", "content", "tags.name"})
 * @ApiFilter(PropertyFilter::class)
 */
class BlogPost implements LocaleableInterface
{
    use LocaleableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"blog_post:get"})
     * @ApiProperty(identifier=false)
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"blog_post:get"})
     * @Gedmo\Versioned()
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"title"})
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Groups({"blog_post:get"})
     * @ApiProperty(identifier=true)
     * @Gedmo\Versioned()
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="text", length=255)
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"blog_post:get"})
     * @Gedmo\Versioned()
     */
    private ?string $excerpt;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private ?string $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"blog_post:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $contentHtml;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Json(message="generic.json")
     * @Groups({"blog_post:get:item"})
     */
    private ?string $contentJson;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Groups({"blog_post:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $metaTitle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Groups({"blog_post:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $metaDescription;

    /**
     * @ORM\ManyToOne(targetEntity=BlogCategory::class, inversedBy="posts")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull(message="generic.not_null")
     * @Groups({"blog_post:get"})
     * @Gedmo\Versioned()
     */
    private ?BlogCategory $category;

    /**
     * @ORM\ManyToMany(targetEntity=BlogTag::class, inversedBy="posts")
     * @Assert\Count(minMessage="generic.count.min", maxMessage="generic.count.max", min=1, max=5)
     * @Groups({"blog_post:get"})
     */
    private Collection $tags;

    /**
     * @ORM\OneToMany(targetEntity=BlogComment::class, mappedBy="post", cascade={"persist", "remove"})
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $comments;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"blog_post:get"})
     */
    private int $readingTimeMinutes = 0;

    /**
     * @Vich\UploadableField(mapping="blog_post_image", fileNameProperty="image")
     * @Assert\Image(
     *     maxSizeMessage="generic.file.max_size",
     *     minWidthMessage="generic.file.image.min_width",
     *     minHeightMessage="generic.file.image.min_height",
     *     maxWidth="generic.file.image.max_width",
     *     maxHeight="generic.file.image.max_height",
     *     mimeTypesMessage="generic.file.mime_type",
     *     maxSize="5M",
     *     minWidth=500,
     *     minHeight=500,
     *     maxWidth=2048,
     *     maxHeight=2048,
     *     mimeTypes={"image/jpeg","image/png","image/gif", "image/jpg"}
     * )
     */
    private ?File $imageFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"blog_post:get"})
     * @ApiThumbnailUrls({
     *     { "name"="medium", "filter"="blog_post_image_medium" },
     *     { "name"="large", "filter"="blog_post_image_large" },
     * })
     * @Gedmo\Versioned()
     */
    private ?string $image = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Groups({"blog_post:get"})
     * @Gedmo\Versioned()
     */
    private ?string $imageAlt = null;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull(message="generic.not_null")
     * @Gedmo\Versioned()
     */
    private bool $published = false;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"blog_post:get"})
     * @Gedmo\Versioned()
     */
    protected ?\DateTimeInterface $publishedAt;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"blog_post:get"})
     * @Gedmo\Versioned()
     */
    protected ?\DateTimeInterface $updatedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldFreelanceInfoId = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldCarriereInfoId = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $oldUrl = null;

    /**
     * @ORM\OneToMany(targetEntity=BlogPostUpvote::class, mappedBy="post", cascade={"persist", "remove"})
     */
    private Collection $upvotes;

    public function __construct()
    {
        $now = Carbon::now();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->publishedAt = $now;
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->upvotes = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): self
    {
        $this->excerpt = $excerpt;

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

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getReadingTimeMinutes(): ?int
    {
        return $this->readingTimeMinutes;
    }

    public function setReadingTimeMinutes(int $readingTimeMinutes): self
    {
        $this->readingTimeMinutes = $readingTimeMinutes;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImageAlt(): ?string
    {
        return $this->imageAlt;
    }

    public function setImageAlt(?string $imageAlt): self
    {
        $this->imageAlt = $imageAlt;

        return $this;
    }

    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    public function setCategory(?BlogCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|BlogTag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(BlogTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(BlogTag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection|BlogComment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(BlogComment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(BlogComment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BlogPostUpvote[]
     */
    public function getUpvotes(): Collection
    {
        return $this->upvotes;
    }

    public function addUpvote(BlogPostUpvote $upvote): self
    {
        if (!$this->upvotes->contains($upvote)) {
            $this->upvotes[] = $upvote;
            $upvote->setPost($this);
        }

        return $this;
    }

    public function removeUpvote(BlogPostUpvote $upvote): self
    {
        if ($this->upvotes->removeElement($upvote)) {
            // set the owning side to null (unless already changed)
            if ($upvote->getPost() === $this) {
                $upvote->setPost(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getVisible(): bool
    {
        return true === $this->published && $this->publishedAt <= new \DateTime('now');
    }

    /**
     * @Groups({"blog_post:get"})
     */
    public function getModified(): bool
    {
        return null !== $this->updatedAt && null !== $this->createdAt && $this->updatedAt !== $this->createdAt;
    }

    public function getOldFreelanceInfoId(): ?int
    {
        return $this->oldFreelanceInfoId;
    }

    public function setOldFreelanceInfoId(?int $oldFreelanceInfoId): self
    {
        $this->oldFreelanceInfoId = $oldFreelanceInfoId;

        return $this;
    }

    public function getOldCarriereInfoId(): ?int
    {
        return $this->oldCarriereInfoId;
    }

    public function setOldCarriereInfoId(?int $oldCarriereInfoId): self
    {
        $this->oldCarriereInfoId = $oldCarriereInfoId;

        return $this;
    }

    public function getOldUrl(): ?string
    {
        return $this->oldUrl;
    }

    public function setOldUrl(?string $oldUrl): self
    {
        $this->oldUrl = $oldUrl;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }
}
