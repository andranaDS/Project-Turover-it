<?php

namespace App\JobPosting\Entity;

use App\JobPosting\Repository\JobPostingRecruiterTraceRepository;
use App\JobPosting\Traits\JobPostingTraceTrait;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=JobPostingRecruiterTraceRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"read_at"})})
 */
class JobPostingRecruiterTrace
{
    use JobPostingTraceTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     * @Gedmo\Blameable(on="create")
     */
    private ?Recruiter $recruiter;

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
