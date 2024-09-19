<?php

namespace App\User\Entity;

use App\User\Repository\MailjetUnsubscribeLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=MailjetUnsubscribeLogRepository::class)
 */
class MailjetUnsubscribeLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTime $createdAt = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="json")
     */
    private ?array $payload = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $unsubscribed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function setPayload(?array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getUnsubscribed(): ?bool
    {
        return $this->unsubscribed;
    }

    public function setUnsubscribed(?bool $unsubscribed): self
    {
        $this->unsubscribed = $unsubscribed;

        return $this;
    }
}
