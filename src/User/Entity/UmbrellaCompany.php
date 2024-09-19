<?php

namespace App\User\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Doctrine\Filter\SearchFilter;
use App\User\Repository\UmbrellaCompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UmbrellaCompanyRepository::class)
 * @ApiResource(
 *      normalizationContext={
 *          "groups"={"umbrella_company:get"},
 *      },
 *     itemOperations={"get"},
 *     collectionOperations={"get"},
 * )
 * @ApiFilter(SearchFilter::class, properties={"name"="partial"})
 */
class UmbrellaCompany
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get:private", "umbrella_company:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=55)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"user:patch:job_search_preferences"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:job_search_preferences"})
     * @Groups({"user:get:private", "umbrella_company:get", "user:patch:job_search_preferences", "user:legacy"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     * @Groups({"user:get:private", "umbrella_company:get"})
     */
    private string $slug;

    /**
     * @ORM\Column(type="integer")
     */
    private int $profileUsageCount = 0;

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

    public function getProfileUsageCount(): ?int
    {
        return $this->profileUsageCount;
    }

    public function setProfileUsageCount(int $profileUsageCount): self
    {
        $this->profileUsageCount = $profileUsageCount;

        return $this;
    }
}
