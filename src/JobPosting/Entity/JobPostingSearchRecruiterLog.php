<?php

namespace App\JobPosting\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\JobPosting\Repository\JobPostingSearchRecruiterLogRepository;
use App\JobPosting\Traits\JobPostingRecruiterSearchFiltersTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=JobPostingSearchRecruiterLogRepository::class)
 * @ApiResource(
 *     attributes={"order"={"createdAt"="DESC"}},
 *     normalizationContext={"groups"={"job_posting_recruiter_search_filter:get"}},
 *     itemOperations={
 *          "turnover_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="object.recruiter == user"
 *          },
 *     },
 *     collectionOperations={
 *          "get"={
 *              "controller"= NotFoundAction::class,
 *          },
 *          "turnover_get_recruiters_me_job_posting_search_recruiter_logs"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/recruiters/me/job_posting_search_logs",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "order"={"createdAt"="DESC"},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of JobPostingSearchRecruiterLog resources of the logged Recruiter resource.",
 *                  "description"="Retrieves the collection of JobPostingSearchRecruiterLog resources of the logged Recruiter resource."
 *              },
 *          },
 *     },
 * )
 */
class JobPostingSearchRecruiterLog
{
    use JobPostingRecruiterSearchFiltersTrait;

    /**
     * @ORM\OneToMany(targetEntity=JobPostingSearchRecruiterLogLocation::class, mappedBy="jobPostingRecruiterSearch", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(JobPostingSearchRecruiterLogLocation $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setJobPostingSearchRecruiterLog($this);
        }

        return $this;
    }

    public function removeLocation(JobPostingSearchRecruiterLogLocation $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getJobPostingSearchRecruiterLog() === $this) {
                $location->setJobPostingSearchRecruiterLog(null);
            }
        }

        return $this;
    }

    public function setLocations(Collection $locations): self
    {
        $this->locations = $locations;

        return $this;
    }
}
