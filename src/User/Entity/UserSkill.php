<?php

namespace App\User\Entity;

use App\Core\Entity\Skill;
use App\User\Repository\UserSkillRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserSkillRepository::class)
 */
class UserSkill
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get:private"})
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Skill::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\Valid(groups={"user:put:skills_and_languages"})
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get", "user:get_turnover:collection"})
     */
    private ?Skill $skill = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="skills")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?User $user;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:legacy"})
     */
    private bool $main = false;

    public function getMain(): ?bool
    {
        return $this->main;
    }

    public function setMain(bool $main): self
    {
        $this->main = $main;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): self
    {
        $this->skill = $skill;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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

    public function __toString(): string
    {
        return $this->skill ? $this->skill->getName() : '';
    }
}
