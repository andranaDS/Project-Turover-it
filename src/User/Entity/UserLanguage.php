<?php

namespace App\User\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\User\Enum\Language;
use App\User\Enum\LanguageLevel;
use App\User\Repository\UserLanguageRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserLanguageRepository::class)
 * @ApiResource(
 *     itemOperations={
 *          "get"={
 *              "controller"= NotFoundAction::class,
 *              "read"= false,
 *              "output"= false,
 *          }
 *     },
 *      collectionOperations={},
 * )
 */
class UserLanguage
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:get:candidates"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=2)
     * @EnumAssert(message="generic.enum.message", class=Language::class, groups={"user:put:skills_and_languages", "user:turnover_write"})
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     */
    private string $language = Language::LANGUAGE_FR;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @EnumAssert(message="generic.enum.message", class=LanguageLevel::class, groups={"user:put:skills_and_languages", "user:turnover_write"})
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     */
    private ?string $languageLevel = LanguageLevel::NATIVE_OR_BILINGUAL;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="languages")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguageLevel(): ?string
    {
        return $this->languageLevel;
    }

    public function setLanguageLevel(?string $languageLevel): self
    {
        $this->languageLevel = $languageLevel;

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

    public function __toString(): string
    {
        return $this->language ?? '';
    }
}
