<?php

namespace App\JobPosting\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Doctrine\Filter\TimestampFilter;
use App\JobPosting\Repository\JobPostingUserTraceRepository;
use App\JobPosting\Traits\JobPostingTraceTrait;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=JobPostingUserTraceRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"read_at"})})
 * @ApiResource(
 *     normalizationContext={"groups"={"job_posting:get", "location"}},
 *     itemOperations={
 *          "get"={
 *              "controller"= NotFoundAction::class,
 *          },
 *     },
 *     collectionOperations={
 *          "get_legacy"={
 *              "method"="GET",
 *              "path"="/legacy/job_posting_traces",
 *              "security"="is_granted('ROLE_LEGACY')",
 *              "normalization_context"={"groups"={"job_posting_trace:legacy"}},
 *              "order"={"readAt"="ASC", "id"="ASC"},
 *              "cache_headers"={"max_age"=0, "shared_max_age"=0},
 *          },
 *     },
 * )
 * @ApiFilter(TimestampFilter::class, properties={"readAt"})
 */
class JobPostingUserTrace
{
    use JobPostingTraceTrait;

    /**
     * @ORM\ManyToOne(targetEntity=JobPosting::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"job_posting_trace:legacy"})
     */
    private ?JobPosting $jobPosting = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    private ?User $user;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @Groups({"job_posting_trace:legacy"})
     */
    public function getUserIdOrIp(): ?string
    {
        return null === $this->user ? $this->ip : (string) $this->user->getId();
    }

    /**
     * @Groups({"job_posting_trace:legacy"})
     */
    public function getReadAtTimestamp(): ?int
    {
        return $this->readAt->getTimestamp();
    }
}
