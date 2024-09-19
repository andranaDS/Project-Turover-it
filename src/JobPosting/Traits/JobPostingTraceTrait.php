<?php

namespace App\JobPosting\Traits;

use App\JobPosting\Entity\JobPosting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait JobPostingTraceTrait
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=JobPosting::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?JobPosting $jobPosting = null;

    /**
     * @ORM\Column(type="string")
     * @Gedmo\IpTraceable(on="create")
     */
    private ?string $ip = null;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $readAt;

    public function __construct()
    {
        $this->readAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReadAt(): \DateTimeInterface
    {
        return $this->readAt;
    }

    public function setReadAt(\DateTimeInterface $readAt): self
    {
        $this->readAt = $readAt;

        return $this;
    }

    public function getJobPosting(): ?JobPosting
    {
        return $this->jobPosting;
    }

    public function setJobPosting(?JobPosting $jobPosting): self
    {
        $this->jobPosting = $jobPosting;

        return $this;
    }
}
