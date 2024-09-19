<?php

namespace App\JobPosting\Entity;

use App\Core\Entity\Location;
use App\JobPosting\Repository\JobPostingSearchRecruiterAlertLocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=JobPostingSearchRecruiterAlertLocationRepository::class)
 */
class JobPostingSearchRecruiterAlertLocation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Embedded(class="App\Core\Entity\Location")
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?Location $location;

    /**
     * @ORM\ManyToOne(targetEntity=JobPostingSearchRecruiterAlert::class, inversedBy="locations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    public ?JobPostingSearchRecruiterAlert $jobPostingRecruiterAlert = null;

    public function __construct()
    {
        $this->location = new Location();
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

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

    public function getJobPostingSearchRecruiterAlert(): ?JobPostingSearchRecruiterAlert
    {
        return $this->jobPostingRecruiterAlert;
    }

    public function setJobPostingSearchRecruiterAlert(?JobPostingSearchRecruiterAlert $jobPostingSearch): self
    {
        $this->jobPostingRecruiterAlert = $jobPostingSearch;

        return $this;
    }
}
