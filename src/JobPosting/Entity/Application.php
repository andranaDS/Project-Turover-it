<?php

namespace App\JobPosting\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Company\Entity\Company;
use App\Core\Annotation\ApiEnum;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Doctrine\Filter\TimestampFilter;
use App\Core\Validator as CoreAssert;
use App\JobPosting\Enum\ApplicationState;
use App\JobPosting\Enum\ApplicationStep;
use App\JobPosting\Repository\ApplicationRepository;
use App\JobPosting\Validator as AppAssert;
use App\User\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"created_at"}),
 *     @ORM\Index(columns={"step"}),
 * })
 * @ApiResource(
 *      attributes={"order"={"createdAt"="DESC"}},
 *      normalizationContext={
 *          "groups"={"application:get", "location"}
 *      },
 *     itemOperations={
 *          "get"={
 *              "security"="object.user == user"
 *          },
 *          "put"={
 *              "security"="object.user == user",
 *              "denormalization_context"={"groups"={"application:put"}},
 *              "validation_groups"={"application:put"},
 *          }
 *     },
 *     collectionOperations={
 *          "post"={
 *              "security"="is_granted('ROLE_USER')",
 *              "denormalization_context"={"groups"={"application:post"}},
 *              "validation_groups"={"application:post"},
 *          },
 *          "get_legacy"={
 *              "method"="GET",
 *              "path"="/legacy/applications",
 *              "security"="is_granted('ROLE_LEGACY')",
 *              "normalization_context"={"groups"={"application:legacy"}},
 *              "order"={"createdAt"="ASC", "id"="ASC"},
 *              "cache_headers"={"max_age"=0, "shared_max_age"=0},
 *          }
 *     },
 *     subresourceOperations={
 *         "api_users_applications_get_subresource"={
 *             "security"="is_granted('ROLE_USER')",
 *         }
 *     },
 * )
 * @ApiFilter(SearchFilter::class, properties={"step"="exact", "state"="exact"})
 * @ApiFilter(TimestampFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(PropertyFilter::class)
 */
class Application
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"application:get", "application:legacy", "feed:get:collection", "feed:get:item"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=20)
     * @EnumAssert(message="generic.enum.message", class=ApplicationStep::class, groups={"application:put"})
     * @Groups({"application:get", "application:put", "application:legacy"})
     */
    private string $step = ApplicationStep::RESUME;

    /**
     * @ORM\Column(type="string", length=20)
     * @EnumAssert(message="generic.enum.message", class=ApplicationState::class)
     * @Groups({"application:get", "application:legacy"})
     * @ApiEnum(class=ApplicationState::class, translationDomain="enums")
     */
    private string $state;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=600, groups={"application:post"})
     * @Groups({"application:get", "application:post", "application:legacy"})
     * @CoreAssert\ForbiddenContent(groups={"application:post"})
     */
    private ?string $content = null;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"application:get"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups({"application:get"})
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"application:get"})
     */
    private ?\DateTimeInterface $favoriteAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"application:get"})
     */
    private ?\DateTimeInterface $seenAt = null;

    /**
     * @ORM\OneToMany(targetEntity=ApplicationDocument::class, mappedBy="application", cascade={"persist", "remove"})
     * @Groups({"application:get", "application:post", "application:legacy"})
     * @Assert\Valid(groups={"application:post"})
     * @AppAssert\ApplicationDocument(
     *     groups={"application:post"}
     * )
     */
    private Collection $documents;

    /**
     * @ORM\ManyToOne(targetEntity=JobPosting::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"application:get", "application:post", "application:legacy"})
     */
    private ?JobPosting $jobPosting = null;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"application:get", "application:post", "application:legacy"})
     */
    private ?Company $company = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="applications")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     * @Groups({"application:legacy"})
     */
    public ?User $user = null;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    /**
     * @Assert\Callback(groups={"application:post"})
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (null === $this->company && null === $this->jobPosting) {
            $context->addViolation('Application must a have at least a company or a jobPosting.');
        } elseif (null !== $this->company && null !== $this->jobPosting) {
            $context->addViolation('Application cannot have a company and a jobPosting.');
        }
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

    public function getStep(): ?string
    {
        return $this->step;
    }

    public function setStep(string $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getFavoriteAt(): ?\DateTimeInterface
    {
        return $this->favoriteAt;
    }

    public function setFavoriteAt(?\DateTimeInterface $favoriteAt): self
    {
        $this->favoriteAt = $favoriteAt;

        return $this;
    }

    public function getSeenAt(): ?\DateTimeInterface
    {
        return $this->seenAt;
    }

    public function setSeenAt(?\DateTimeInterface $seenAt): self
    {
        $this->seenAt = $seenAt;

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
     * @return Collection|ApplicationDocument[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(ApplicationDocument $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setApplication($this);
        }

        return $this;
    }

    public function removeDocument(ApplicationDocument $document): self
    {
        // set the owning side to null (unless already changed)
        if ($this->documents->removeElement($document) && $document->getApplication() === $this) {
            $document->setApplication(null);
        }

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @Groups({"application:legacy"})
     */
    public function getCreatedAtTimestamp(): ?int
    {
        return null === $this->createdAt ? null : $this->createdAt->getTimestamp();
    }

    /**
     * @Groups({"application:legacy"})
     */
    public function getUpdatedAtTimestamp(): ?int
    {
        return null === $this->updatedAt ? null : $this->updatedAt->getTimestamp();
    }
}
