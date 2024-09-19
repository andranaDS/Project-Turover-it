<?php

namespace App\Notification\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use App\Core\Doctrine\Filter\SearchFilter;
use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\JobPosting;
use App\Notification\Repository\NotificationRepository;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"event"})})
 * @ApiResource(
 *     attributes={"order"={"id"="DESC"}},
 *     normalizationContext={"groups"={"notification:get"}},
 *     itemOperations={
 *         "turnover_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getRecruiter() == user",
 *          },
 *     },
 *     collectionOperations={
 *         "turnover_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *          },
 *     },
 * )
 * @ApiFilter(RangeFilter::class, properties={"id"})
 * @ApiFilter(SearchFilter::class, properties={"event"="exact"})
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"notification:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     */
    private Recruiter $recruiter;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"notification:get"})
     */
    private \DateTime $createdAt;

    /**
     * @ORM\Column(type="string")
     * @Groups({"notification:get"})
     */
    private string $event;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $readAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=JobPosting::class)
     */
    private ?JobPosting $jobPosting = null;

    /**
     * @ORM\ManyToOne(targetEntity=Application::class)
     */
    private ?Application $application = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $variables = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getReadAt(): ?\DateTime
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTime $readAt): self
    {
        $this->readAt = $readAt;

        return $this;
    }

    /**
     * @Groups({"notification:get"})
     */
    public function isRead(): bool
    {
        return null !== $this->readAt;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }

    public function getRecruiter(): ?Recruiter
    {
        return $this->recruiter;
    }

    public function setRecruiter(Recruiter $recruiter): self
    {
        $this->recruiter = $recruiter;

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

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @Groups({"notification:get"})
     */
    public function getData(): array
    {
        return array_filter($this->variables + [
            'jobPosting' => $this->jobPosting,
            'application' => $this->application,
        ]);
    }
}
