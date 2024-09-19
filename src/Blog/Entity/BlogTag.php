<?php

namespace App\Blog\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Blog\Repository\BlogTagRepository;
use App\Core\Enum\Locale;
use App\Core\Traits\LocaleableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BlogTagRepository::class)
 * @Gedmo\Loggable()
 * @ApiResource(
 *     itemOperations={
 *          "freework_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"blog_tag:get", "blog_tag:get:item"}}
 *          }
 *     },
 *     collectionOperations={
 *          "freework_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"blog_tag:get", "blog_tag:get:collection"}}
 *          }
 *     },
 * )
 * @ApiFilter(PropertyFilter::class)
 */
class BlogTag
{
    use LocaleableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"blog_tag:get", "blog_post:get", "blog_category:get"})
     * @ApiProperty(identifier=false)
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"blog_tag:get", "blog_post:get"})
     * @Gedmo\Versioned()
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Groups({"blog_tag:get", "blog_post:get"})
     * @ApiProperty(identifier=true)
     * @Gedmo\Versioned()
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Groups({"blog_tag:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $metaTitle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Groups({"blog_tag:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $metaDescription;

    /**
     * @ORM\ManyToMany(targetEntity=BlogPost::class, mappedBy="tags")
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $posts;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @EnumAssert(message="generic.enum.message", Locale::class, multiple=true, multipleMessage="generic.enum.multiple")
     * @Groups({"blog_tag:get", "blog_post:get"})
     * @Gedmo\Versioned
     */
    private ?array $locales = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function __toString(): string
    {
        return $this->name ?: (string) $this->id;
    }

    /**
     * @return Collection|BlogPost[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(BlogPost $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->addTag($this);
        }

        return $this;
    }

    public function removePost(BlogPost $post): self
    {
        if ($this->posts->removeElement($post)) {
            $post->removeTag($this);
        }

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
}
