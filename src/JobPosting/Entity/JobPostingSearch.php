<?php

namespace App\JobPosting\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Util\Numbers;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\PublishedSince;
use App\JobPosting\Enum\RemoteMode;
use App\JobPosting\Repository\JobPostingSearchRepository;
use App\User\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobPostingSearchRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"active_alert"}),
 * })
 * @ApiResource(
 *      attributes={"order"={"createdAt"="DESC"}},
 *      normalizationContext={
 *          "groups"={"job_posting_search:get"},
 *      },
 *     itemOperations={
 *          "get"={
 *              "security"="object.user == user"
 *          },
 *          "put"={
 *              "security"="object.user == user",
 *              "denormalization_context"={"groups"={"job_posting_search:put"}},
 *              "validation_groups"={"job_posting_search:put"},
 *          },
 *          "delete"={
 *              "security"="object.user == user",
 *          },
 *     },
 *     collectionOperations={
 *          "get"= {
 *              "security"="object.user == user",
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_USER')",
 *              "denormalization_context"={"groups"={"job_posting_search:post"}},
 *              "validation_groups"={"job_posting_search:post"},
 *          }
 *     },
 *     subresourceOperations={
 *         "api_users_job_posting_searches_get_subresource"={
 *             "security"="is_granted('ROLE_USER')",
 *         }
 *     },
 * )
 */
class JobPostingSearch
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=true)
     * @Groups({"job_posting_search:get"})
     */
    private ?int $id = 0;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"job_posting_search:post", "job_posting_search:put"})
     * @Assert\Length(maxMessage="generic.length.max", max=255, groups={"job_posting_search:post", "job_posting_search:put"})
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     */
    private ?string $searchKeywords = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     * @EnumAssert(message="generic.enum.message", class=RemoteMode::class, groups={"job_posting_search:post", "job_posting_search:put"}, multiple=true, multipleMessage="generic.enum.multiple")
     */
    private ?array $remoteMode = null;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     * @EnumAssert(message="generic.enum.message", class=PublishedSince::class, groups={"job_posting_search:post", "job_posting_search:put"})
     */
    private ?string $publishedSince = null;

    /**
     * @ORM\Column(type="json")
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     * @EnumAssert(message="generic.enum.message", class=Contract::class, multiple=true, multipleMessage="generic.enum.multiple", groups={"job_posting_search:post", "job_posting_search:put"})
     */
    private array $contracts = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     */
    private ?int $minAnnualSalary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     */
    private ?int $minDailySalary = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     */
    private ?string $currency = null;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     */
    private ?int $minDuration;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     */
    private ?int $maxDuration;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"job_posting_search:get", "job_posting_search:put"})
     */
    private ?bool $activeAlert = true;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="jobPostingSearches")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     * @Groups({"job_posting_search:get"})
     */
    public User $user;

    /**
     * @ORM\OneToMany(targetEntity=JobPostingSearchLocation::class, mappedBy="jobPostingSearch", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"job_posting_search:get", "job_posting_search:post", "job_posting_search:put"})
     */
    private Collection $locations;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldId = null;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of id.
     */
    public function getId(): ?int
    {
        return $this->id;
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

    public function setLocations(array $locations): self
    {
        $this->locations = new ArrayCollection($locations);

        return $this;
    }

    public function addLocation(JobPostingSearchLocation $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setJobPostingSearch($this);
        }

        return $this;
    }

    public function removeLocation(JobPostingSearchLocation $location): self
    {
        $this->locations->removeElement($location);

        return $this;
    }

    public function getActiveAlert(): ?bool
    {
        return $this->activeAlert;
    }

    public function setActiveAlert(?bool $activeAlert): self
    {
        $this->activeAlert = $activeAlert;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMinAnnualSalary(): ?int
    {
        return $this->minAnnualSalary;
    }

    public function setMinAnnualSalary(?int $minAnnualSalary): self
    {
        $this->minAnnualSalary = $minAnnualSalary;

        return $this;
    }

    public function getMinDailySalary(): ?int
    {
        return $this->minDailySalary;
    }

    public function setMinDailySalary(?int $minDailySalary): self
    {
        $this->minDailySalary = $minDailySalary;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getMinDuration(): ?int
    {
        return $this->minDuration;
    }

    public function setMinDuration(?int $minDuration): self
    {
        $this->minDuration = $minDuration;

        return $this;
    }

    public function getMaxDuration(): ?int
    {
        return $this->maxDuration;
    }

    public function setMaxDuration(?int $maxDuration): self
    {
        $this->maxDuration = $maxDuration;

        return $this;
    }

    public function getPublishedSince(): ?string
    {
        return $this->publishedSince;
    }

    public function setPublishedSince(?string $publishedSince): self
    {
        $this->publishedSince = $publishedSince;

        return $this;
    }

    public function getRemoteMode(): ?array
    {
        return $this->remoteMode;
    }

    public function setRemoteMode(?array $remoteMode): self
    {
        $this->remoteMode = $remoteMode;

        return $this;
    }

    public function getSearchKeywords(): ?string
    {
        return $this->searchKeywords;
    }

    public function setSearchKeywords(?string $searchKeywords): self
    {
        $this->searchKeywords = $searchKeywords;

        return $this;
    }

    public function getContracts(): ?array
    {
        return $this->contracts;
    }

    public function setContracts(array $contracts): self
    {
        $this->contracts = $contracts;

        return $this;
    }

    public function getLocations(): Collection
    {
        return $this->locations;
    }

    /**
     * @Groups({"job_posting_search:get"})
     */
    public function getAnnualSalary(): ?string
    {
        return Numbers::formatRangeCurrency($this->minAnnualSalary, null, $this->currency ?? 'EUR');
    }

    /**
     * @Groups({"job_posting_search:get"})
     */
    public function getDailySalary(): ?string
    {
        return Numbers::formatRangeCurrency($this->minDailySalary, null, $this->currency ?? 'EUR');
    }

    public function getOldId(): ?int
    {
        return $this->oldId;
    }

    public function setOldId(?int $oldId): self
    {
        $this->oldId = $oldId;

        return $this;
    }
}
