<?php

namespace App\Company\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Company\Controller\Turnover\Site\Delete;
use App\Company\Controller\Turnover\Site\Post;
use App\Company\Repository\SiteRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SiteRepository::class)
 * @ApiResource(
 *      normalizationContext={
 *          "groups"={"site:get"}
 *      },
 *     collectionOperations={
 *          "get"={
 *              "controller"= NotFoundAction::class,
 *          },
 *          "turnover_post"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security_post_denormalize"="is_granted('SITE_POST', object)",
 *              "controller"=Post::class,
 *              "denormalization_context"={"groups"={"site:post"}},
 *              "validation_groups"={"site:post"},
 *          }
 *      },
 *      itemOperations={
 *          "turnover_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('SITE_MINE', object)",
 *          },
 *          "turnover_put"={
 *              "method"="PUT",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('SITE_MINE', object)",
 *              "denormalization_context"={"groups"={"site:put"}},
 *              "validation_groups"={"site:put"},
 *          },
 *          "turnover_delete"={
 *              "method"="DELETE",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('SITE_MINE', object)",
 *              "controller"=Delete::class,
 *          }
 *     },
 *     subresourceOperations={
 *          "api_companies_sites_get_subresource"={
 *              "security"="is_granted('COMPANY_MINE') and is_granted('RECRUITER_MAIN')",
 *          }
 *     }
 * )
 */
class Site
{
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"site:get", "recruiter:get", "recruiter:get:secondary"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"site:post", "site:put"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"site:post", "site:put"})
     * @Groups({"site:get", "site:post", "site:put", "recruiter:get", "recruiter:get:secondary"})
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Gedmo\Slug(fields={"name"})
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Groups({"site:get", "recruiter:get", "recruiter:get:secondary"})
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"site:post", "site:put"})
     * @Assert\Ip(message="generic.ip", version="4_public", groups={"site:post", "site:put"})
     * @Groups({"site:get", "site:post", "site:put"})
     */
    private ?string $ip;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"site:get"})
     */
    protected ?\DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups({"site:get"})
     */
    protected ?\DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="sites")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull(message="generic.not_null")
     */
    public ?Company $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
