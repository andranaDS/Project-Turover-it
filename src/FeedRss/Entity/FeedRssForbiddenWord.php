<?php

namespace App\FeedRss\Entity;

use App\FeedRss\Repository\FeedRssForbiddenWordRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FeedRssForbiddenWordRepository::class)
 * @UniqueEntity(
 *     fields={"name"},
 *     message="The same word cannot be used twice."
 * )
 */
class FeedRssForbiddenWord
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\Length(maxMessage="generic.length.max", max="25")
     * @Assert\NotBlank(message="generic.not_blank")
     */
    private ?string $name = null;

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

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }
}
