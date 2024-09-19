<?php

namespace App\User\Entity;

use App\User\Repository\UserFormationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserFormationRepository::class)
 */
class UserFormation
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"user:patch:education"})
     * @Groups({"user:get:private", "user:patch:education", "user:legacy"})
     */
    private ?string $diplomaTitle = null;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="2", groups={"user:patch:education"})
     * @Assert\NotNull(message="generic.not_blank", groups={"user:turnover_write"})
     * @Groups({"user:get:private", "user:patch:education", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     */
    private ?int $diplomaLevel = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"user:patch:education"})
     * @Groups({"user:get:private", "user:patch:education", "user:legacy"})
     */
    private ?string $school = null;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="4", groups={"user:patch:education"})
     * @Groups({"user:get:private", "user:patch:education", "user:legacy"})
     */
    private ?int $diplomaYear = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:education", "user:legacy"})
     */
    private bool $beingObtained = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:education", "user:legacy"})
     */
    private bool $selfTaught = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDiplomaTitle(): ?string
    {
        return $this->diplomaTitle;
    }

    public function setDiplomaTitle(?string $diplomaTitle): self
    {
        $this->diplomaTitle = $diplomaTitle;

        return $this;
    }

    public function getDiplomaLevel(): ?int
    {
        return $this->diplomaLevel;
    }

    public function setDiplomaLevel(?int $diplomaLevel): self
    {
        $this->diplomaLevel = $diplomaLevel;

        return $this;
    }

    public function getSchool(): ?string
    {
        return $this->school;
    }

    public function setSchool(?string $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function getDiplomaYear(): ?int
    {
        return $this->diplomaYear;
    }

    public function setDiplomaYear(?int $diplomaYear): self
    {
        $this->diplomaYear = $diplomaYear;

        return $this;
    }

    public function getBeingObtained(): ?bool
    {
        return $this->beingObtained;
    }

    public function setBeingObtained(bool $beingObtained): self
    {
        $this->beingObtained = $beingObtained;

        return $this;
    }

    public function getSelfTaught(): ?bool
    {
        return $this->selfTaught;
    }

    public function setSelfTaught(bool $selfTaught): self
    {
        $this->selfTaught = $selfTaught;

        return $this;
    }
}
