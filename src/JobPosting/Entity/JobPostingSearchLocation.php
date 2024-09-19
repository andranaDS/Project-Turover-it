<?php

namespace App\JobPosting\Entity;

use App\Core\Entity\Location;
use App\JobPosting\Repository\JobPostingSearchLocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=JobPostingSearchLocationRepository::class)
 */
class JobPostingSearchLocation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Embedded(class="App\Core\Entity\Location")
     * @Groups({"user:get:private", "location", "job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     */
    private ?Location $location;

    /**
     * @ORM\ManyToOne(targetEntity=JobPostingSearch::class, inversedBy="locations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    public ?JobPostingSearch $jobPostingSearch = null;

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

    public function getJobPostingSearch(): ?JobPostingSearch
    {
        return $this->jobPostingSearch;
    }

    public function setJobPostingSearch(?JobPostingSearch $jobPostingSearch): self
    {
        $this->jobPostingSearch = $jobPostingSearch;

        return $this;
    }
}
