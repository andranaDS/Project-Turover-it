<?php

namespace App\Core\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Repository\AlertRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AlertRepository::class)
 * @Gedmo\Loggable()
 * @ApiResource(
 *     itemOperations={
 *        "freework_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "cache_headers"={"max_age"=0, "shared_max_age"=0},
 *         },
 *     },
 *     collectionOperations={}
 * )
 */
class Alert
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private ?string $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Gedmo\Versioned()
     */
    private ?string $contentHtml;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Json(message="generic.json")
     */
    private ?string $contentJson;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull(message="generic.not_null")
     * @Gedmo\Versioned()
     */
    private \DateTimeInterface $startAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull(message="generic.not_null")
     * @Gedmo\Versioned()
     */
    private \DateTimeInterface $endAt;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned()
     */
    protected ?bool $blocking = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContentHtml(): ?string
    {
        return $this->contentHtml;
    }

    public function setContentHtml(?string $contentHtml): self
    {
        $this->contentHtml = $contentHtml;

        return $this;
    }

    public function getContentJson(): ?string
    {
        return $this->contentJson;
    }

    public function setContentJson(?string $contentJson): self
    {
        $this->contentJson = $contentJson;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startsAt): self
    {
        $this->startAt = $startsAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getBlocking(): ?bool
    {
        return $this->blocking;
    }

    public function setBlocking(bool $blocking): self
    {
        $this->blocking = $blocking;

        return $this;
    }

    public function isExpired(): bool
    {
        return Carbon::now() > $this->getEndAt();
    }
}
