<?php

namespace App\Company\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Company\Repository\CompanyBusinessActivityRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CompanyBusinessActivityRepository::class)
 * @ApiResource(
 *     attributes={
 *          "pagination_enabled"=false,
 *          "order"={"name"="ASC"}
 *     },
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"company_business_activity:get", "company_business_activity:get:item"}},
 *          },
 *      },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"company_business_activity:get", "company_business_activity:get:collection"}},
 *          },
 *     },
 * )
 */
class CompanyBusinessActivity
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"company_business_activity:get", "company:get", "job_posting:get:item", "company:patch:account", "company:patch:directory", "job_posting_recruiter_search_filter:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=128)
     * @Groups({"company_business_activity:get", "company:get", "job_posting:get:item", "company:get:homepage", "company:patch:account", "company:patch:directory", "job_posting_recruiter_search_filter:get"})
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=128)
     * @Gedmo\Slug(fields={"name"})
     * @Groups({"company_business_activity:get", "company:get", "job_posting:get:item", "company:get:homepage", "company:patch:account", "company:patch:directory", "job_posting_recruiter_search_filter:get"})
     */
    private ?string $slug;

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

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }
}
