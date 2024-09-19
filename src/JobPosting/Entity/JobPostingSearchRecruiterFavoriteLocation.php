<?php

namespace App\JobPosting\Entity;

use App\Core\Entity\Location;
use App\JobPosting\Repository\JobPostingSearchRecruiterFavoriteLocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=JobPostingSearchRecruiterFavoriteLocationRepository::class)
 */
class JobPostingSearchRecruiterFavoriteLocation
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
     * @ORM\ManyToOne(targetEntity=JobPostingSearchRecruiterFavorite::class, inversedBy="locations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    public ?JobPostingSearchRecruiterFavorite $jobPostingRecruiterFavorite = null;

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

    public function getJobPostingSearchRecruiterFavorite(): ?JobPostingSearchRecruiterFavorite
    {
        return $this->jobPostingRecruiterFavorite;
    }

    public function setJobPostingSearchRecruiterFavorite(?JobPostingSearchRecruiterFavorite $jobPostingSearch): self
    {
        $this->jobPostingRecruiterFavorite = $jobPostingSearch;

        return $this;
    }
}
