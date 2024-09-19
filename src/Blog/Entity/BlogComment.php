<?php

namespace App\Blog\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Blog\Repository\BlogCommentRepository;
use App\Core\Annotation\SensitiveContentEntity;
use App\Core\Annotation\SensitiveContentProperty;
use App\Core\Validator as CoreAssert;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BlogCommentRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"deleted_at"})})
 * @Gedmo\SoftDeleteable()
 * @ApiResource(
 *      attributes={
 *          "order"={"createdAt"="DESC"},
 *      },
 *      normalizationContext={
 *          "groups"={"blog_comment:get"},
 *      },
 *      collectionOperations={
 *          "freework_post"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"blog_comment:get", "blog_comment:get:item"}},
 *              "denormalization_context"={"groups"={"blog_comment:post", "blog_comment:put"}},
 *              "validation_groups"={"blog_comment:post", "blog_comment:put"},
 *          },
 *          "freework_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"blog_comment:get"}}
 *          },
 *      },
 *      itemOperations={
 *          "freework_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *          },
 *          "freework_delete"={
 *              "method"="DELETE",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="object.author == user",
 *          },
 *          "freework_put"={
 *              "method"="PUT",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="object.author == user",
 *              "denormalization_context"={"groups"={"blog_comment:put"}},
 *              "validation_groups"={"blog_comment:put"},
 *          },
 *      },
 *      subresourceOperations={
 *          "api_blog_posts_comments_get_subresource"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"blog_comment:get"}}
 *          }
 *      }
 * )
 * @SensitiveContentEntity()
 */
class BlogComment
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"blog_comment:get"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"blog_comment:post", "blog_comment:put"})
     * @Groups({"blog_comment:post", "blog_comment:put", "blog_comment:get"})
     * @SensitiveContentProperty()
     * @CoreAssert\ForbiddenContent(groups={"blog_comment:post", "blog_comment:put"})
     */
    private ?string $content;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blogComments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     * @Groups({"blog_comment:post", "blog_comment:put", "blog_comment:get"})
     */
    public ?User $author;

    /**
     * @ORM\ManyToOne(targetEntity=BlogPost::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull(message="generic.not_null", groups={"blog_comment:post", "blog_comment:put"})
     * @Groups({"blog_comment:post", "blog_comment:put", "blog_comment:get"})
     */
    public ?BlogPost $post;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"blog_comment:post", "blog_comment:put", "blog_comment:get"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups({"blog_comment:post", "blog_comment:put", "blog_comment:get"})
     */
    protected $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPost(): ?BlogPost
    {
        return $this->post;
    }

    public function setPost(?BlogPost $post): self
    {
        $this->post = $post;

        return $this;
    }
}
