<?php

namespace App\JobPosting\Entity;

use App\JobPosting\Repository\JobPostingRecruiterFavoriteRepository;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=JobPostingRecruiterFavoriteRepository::class)
 */
class JobPostingRecruiterFavorite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=JobPosting::class, inversedBy="recruiterFavorites")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?JobPosting $jobPosting;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"job_posting:get", "job_posting:get:collection"})
     */
    protected \DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    private ?Recruiter $recruiter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getRecruiter(): ?Recruiter
    {
        return $this->recruiter;
    }

    public function setRecruiter(?Recruiter $recruiter): self
    {
        $this->recruiter = $recruiter;

        return $this;
    }
}
