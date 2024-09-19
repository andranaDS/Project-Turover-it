<?php

namespace App\JobPosting\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\JobPosting\Repository\JobPostingShareRepository;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobPostingShareRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"job_posting_share:get"}},
 *     itemOperations={
 *          "turnover_get"={
 *              "method"="GET",
 *               "controller"= NotFoundAction::class,
 *          },
 *     },
 *     collectionOperations={
 *          "turnover_post"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "denormalizationContext"={"groups"={"job_posting_share:post"}},
 *              "validation_groups"={"job_posting_share:post"},
 *          },
 *     }
 * )
 */
class JobPostingShare
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"job_posting_share:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"job_posting_share:post"})
     * @Assert\NotBlank(groups={"job_posting_share:post"},message="generic.not_blank")
     * @Assert\Email(groups={"job_posting_share:post"},message="generic.email")
     */
    private ?string $email = null;

    /**
     * @ORM\ManyToOne(targetEntity=JobPosting::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"job_posting_share:post"})
     * @Assert\NotNull(groups={"job_posting_share:post"}, message="generic.not_null")
     * @Assert\Valid(groups={"job_posting_share:post"})
     */
    private ?JobPosting $jobPosting;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     * @Gedmo\Blameable(on="create")
     * @Assert\NotNull(message="generic.not_null")
     */
    private ?Recruiter $sharedBy;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSharedBy(): ?Recruiter
    {
        return $this->sharedBy;
    }

    public function setSharedBy(?Recruiter $sharedBy): self
    {
        $this->sharedBy = $sharedBy;

        return $this;
    }
}
