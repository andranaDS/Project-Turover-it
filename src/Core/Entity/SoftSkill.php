<?php

namespace App\Core\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Repository\SoftSkillRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SoftSkillRepository::class)
 * @ApiResource(
 *     itemOperations={"get"},
 *     collectionOperations={"get"},
 * )
 */
class SoftSkill
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:legacy", "job_posting_template:get", "user:get:candidates", "company:get", "company:patch:directory"})
     */
    private ?int $id = 0;

    /**
     * @ORM\Column(type="string", length=55)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"user:put:skills_and_languages"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:put:skills_and_languages"})
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:legacy", "job_posting_template:get", "user:get:candidates", "company:get", "company:patch:directory"})
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     * @Groups({"user:get:private", "user:put:skills_and_languages", "job_posting_template:get", "user:get:candidates", "company:get", "company:patch:directory"})
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

    public function getName(): string
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
        return $this->name ?? '';
    }
}
