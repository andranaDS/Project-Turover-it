<?php

namespace App\User\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Interfaces\LocaleableInterface;
use App\Core\Traits\LocaleableTrait;
use App\User\Repository\InsuranceCompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=InsuranceCompanyRepository::class)
 * @ApiResource(
 *      normalizationContext={
 *          "groups"={"insurance_company:get"},
 *      },
 *     itemOperations={"get"},
 *     collectionOperations={"get"},
 * )
 * @ApiFilter(SearchFilter::class, properties={"name"="partial"})
 */
class InsuranceCompany implements LocaleableInterface
{
    use LocaleableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get:private", "insurance_company:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=55)
     * @Groups({"user:get:private", "insurance_company:get", "user:patch:job_search_preferences"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     * @Groups({"user:get:private", "insurance_company:get"})
     */
    private string $slug;

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
}
