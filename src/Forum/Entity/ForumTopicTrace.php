<?php

namespace App\Forum\Entity;

use App\Forum\Repository\ForumTopicTraceRepository;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ForumTopicTraceRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"last"}),
 *     @ORM\Index(columns={"topic_id"}),
 * })
 */
class ForumTopicTrace
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $topicId;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    private ?User $user;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\IpTraceable(on="create")
     */
    private ?string $ip;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $readAt;

    /**
     * @ORM\Column(type="boolean")
     */
    protected ?bool $last = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected ?bool $markAllAsRead = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected ?bool $created = false;

    public function __construct()
    {
        $this->readAt = new \DateTime();
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

    public function getReadAt(): ?\DateTimeInterface
    {
        return $this->readAt;
    }

    public function setReadAt(\DateTimeInterface $readAt): self
    {
        $this->readAt = $readAt;

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

    public function getLast(): ?bool
    {
        return $this->last;
    }

    public function setLast(bool $last): self
    {
        $this->last = $last;

        return $this;
    }

    public function getMarkAllAsRead(): ?bool
    {
        return $this->markAllAsRead;
    }

    public function setMarkAllAsRead(bool $markAllAsRead): self
    {
        $this->markAllAsRead = $markAllAsRead;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getOldId(): ?int
    {
        return $this->oldId;
    }

    public function setOldId(?int $oldId): self
    {
        $this->oldId = $oldId;

        return $this;
    }

    public function getCreated(): ?bool
    {
        return $this->created;
    }

    public function setCreated(bool $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getTopicId(): ?int
    {
        return $this->topicId;
    }

    public function setTopicId(?int $topicId): self
    {
        $this->topicId = $topicId;

        return $this;
    }
}
