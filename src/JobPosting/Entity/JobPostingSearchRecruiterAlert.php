<?php

namespace App\JobPosting\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\JobPosting\Repository\JobPostingSearchRecruiterAlertRepository;
use App\JobPosting\Traits\JobPostingRecruiterSearchFiltersTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobPostingSearchRecruiterAlertRepository::class)
 * @ApiResource(
 *     attributes={"order"={"createdAt"="DESC"}},
 *     normalizationContext={"groups"={"job_posting_recruiter_search_filter:get"}},
 *     denormalizationContext={"groups"={"job_posting_recruiter_search_filter:write"}},
 *     itemOperations={
 *          "turnover_get"={
 *              "method"="GET",
 *              "security"="object.recruiter == user",
 *          },
 *          "turnover_put"={
 *              "method"="PUT",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="object.recruiter == user",
 *              "denormalization_context"={"groups"={"job_posting_recruiter_search_filter:write"}},
 *              "validation_groups"={"job_posting_recruiter_search_filter:write"},
 *          },
 *          "turnover_delete"={
 *              "method"="DELETE",
 *              "security"="object.recruiter == user",
 *          },
 *     },
 *     collectionOperations={
 *          "get"={
 *              "controller"= NotFoundAction::class,
 *          },
 *          "turnover_post"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "denormalization_context"={"groups"={"job_posting_recruiter_search_filter:write"}},
 *              "validation_groups"={"job_posting_recruiter_search_filter:write"},
 *          },
 *          "turnover_get_recruiters_me_job_posting_search_recruiter_alerts"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/recruiters/me/job_posting_search_alerts",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "order"={"createdAt"="DESC"},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of JobPostingSearchRecruiterAlert resources of the logged Recruiter resource.",
 *                  "description"="Retrieves the collection of JobPostingSearchRecruiterAlert resources of the logged Recruiter resource."
 *              },
 *          },
 *     }
 * )
 */
class JobPostingSearchRecruiterAlert
{
    use JobPostingRecruiterSearchFiltersTrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"job_posting_recruiter_search_filter:write"})
     * @Assert\Length(maxMessage="generic.length.max", max=255, groups={"job_posting_recruiter_search_filter:write"})
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?bool $active = true;

    /**
     * @ORM\OneToMany(targetEntity=JobPostingSearchRecruiterAlertLocation::class, mappedBy="jobPostingRecruiterAlert", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(JobPostingSearchRecruiterAlertLocation $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setJobPostingSearchRecruiterAlert($this);
        }

        return $this;
    }

    public function removeLocation(JobPostingSearchRecruiterAlertLocation $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getJobPostingSearchRecruiterAlert() === $this) {
                $location->setJobPostingSearchRecruiterAlert(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
