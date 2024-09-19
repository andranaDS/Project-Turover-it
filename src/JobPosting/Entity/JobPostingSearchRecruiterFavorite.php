<?php

namespace App\JobPosting\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\JobPosting\Repository\JobPostingSearchRecruiterFavoriteRepository;
use App\JobPosting\Traits\JobPostingRecruiterSearchFiltersTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=JobPostingSearchRecruiterFavoriteRepository::class)
 * @ApiResource(
 *     attributes={"order"={"createdAt"="DESC"}},
 *     normalizationContext={"groups"={"job_posting_recruiter_search_filter:get"}},
 *     itemOperations={
 *          "turnover_get"={
 *              "method"="GET",
 *              "security"="object.recruiter == user",
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
 *          "turnover_get_recruiters_me_job_posting_search_recruiter_favorites"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/recruiters/me/job_posting_search_favorites",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "order"={"createdAt"="DESC"},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of JobPostingSearchRecruiterFavorite resources of the logged Recruiter resource.",
 *                  "description"="Retrieves the collection of JobPostingSearchRecruiterFavorite resources of the logged Recruiter resource."
 *              },
 *          },
 *     },
 * )
 */
class JobPostingSearchRecruiterFavorite
{
    use JobPostingRecruiterSearchFiltersTrait;

    /**
     * @ORM\OneToMany(targetEntity=JobPostingSearchRecruiterFavoriteLocation::class, mappedBy="jobPostingRecruiterFavorite", cascade={"persist", "remove"}, orphanRemoval=true)
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

    public function addLocation(JobPostingSearchRecruiterFavoriteLocation $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setJobPostingSearchRecruiterFavorite($this);
        }

        return $this;
    }

    public function removeLocation(JobPostingSearchRecruiterFavoriteLocation $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getJobPostingSearchRecruiterFavorite() === $this) {
                $location->setJobPostingSearchRecruiterFavorite(null);
            }
        }

        return $this;
    }
}
