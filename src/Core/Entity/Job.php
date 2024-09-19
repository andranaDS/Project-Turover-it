<?php

namespace App\Core\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Repository\JobRepository;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 * @Gedmo\Loggable()
 * @ApiResource(
 *      normalizationContext={
 *          "enable_max_depth"=true,
 *      },
 *     itemOperations={
 *          "get" = {
 *              "normalization_context"={"groups"={"job:get", "job:get:item"}}
 *          },
 *      },
 *     collectionOperations={
 *          "get" = {
 *              "normalization_context"={"groups"={"job:get"}},
 *              "cache_headers"={"max_age"=0, "shared_max_age"=604800},
 *          },
 *     },
 * )
 * @ApiFilter(SearchFilter::class, properties={"name"="partial", "nameForContribution"="partial", "nameForUser"="partial"})
 * @ApiFilter(BooleanFilter::class, properties={"availableForContribution", "availableForUser"})
 * @ApiFilter(PropertyFilter::class)
 */
class Job
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get:private", "job:get", "contribution:get", "job:get:item", "job_category:get", "job_contribution_statistics:get", "trend:get", "user:legacy", "job_posting:get", "application:get", "job_posting:legacy", "user:get:candidates"})
     * @ApiProperty(identifier=false)
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:job_search_preferences"})
     * @Assert\Length(maxMessage="generic.length.max", max=255, groups={"user:patch:job_search_preferences"})
     * @Groups({"user:get:private", "job:get", "user:patch:job_search_preferences", "contribution:get", "job:get:item", "job_category:get", "job_contribution_statistics:get", "trend:get", "user:legacy", "job_posting:get", "application:get", "job_posting:legacy", "user:get:candidates", "user:get_turnover:collection"})
     * @Gedmo\Versioned()
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     * @Groups({"user:get:private", "job:get", "user:patch:job_search_preferences", "contribution:get", "job:get:item", "job_category:get", "trend:get", "job_posting:get", "application:get", "job_posting:legacy", "user:get:candidates"})
     * @Gedmo\Versioned()
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "job:get", "contribution:get", "job:get:item", "job_category:get", "trend:get"})
     * @Gedmo\Versioned()
     */
    private bool $availableForContribution = true;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:get:private", "job:get", "user:patch:job_search_preferences", "contribution:get", "job_posting:get", "job:get:item", "job_category:get"})
     * @Gedmo\Versioned()
     */
    private ?string $nameForContribution;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"nameForContribution"})
     * @Groups({"user:get:private", "job:get", "user:patch:job_search_preferences", "contribution:get", "job_posting:get", "job:get:item", "job_category:get", "trend:get"})
     * @ApiProperty(identifier=true)
     * @Gedmo\Versioned()
     */
    private ?string $nameForContributionSlug;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "job:get", "contribution:get", "job:get:item", "job_category:get"})
     * @Gedmo\Versioned()
     */
    private bool $availableForUser = true;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:get:private", "job:get", "user:patch:job_search_preferences", "contribution:get", "job:get:item", "job_category:get"})
     * @Gedmo\Versioned()
     */
    private ?string $nameForUser;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"nameForUser"})
     * @Groups({"user:get:private", "job:get", "user:patch:job_search_preferences", "contribution:get", "job:get:item", "job_category:get"})
     * @Gedmo\Versioned()
     */
    private ?string $nameForUserSlug;

    /**
     * @ORM\ManyToOne(targetEntity=JobCategory::class, inversedBy="jobs")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"job:get"})
     * @MaxDepth(1)
     * @Gedmo\Versioned()
     */
    private ?JobCategory $category;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Versioned()
     */
    private ?Job $parentForContribution;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $salaryDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $salaryFormation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $salaryStandardMission;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned
     */
    private array $salarySkills = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $salarySeoMetaTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $salarySeoMetaDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $faqDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $faqPrice;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $faqDefinition;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $faqMissions;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $faqSkills;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $faqProfile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $faqSeoMetaTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $faqSeoMetaDescription;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=16)
     * @Gedmo\Versioned()
     */
    private ?string $RomeCode;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=16)
     * @Gedmo\Versioned()
     */
    private ?string $OgrCode;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=128)
     * @Gedmo\Versioned()
     */
    private ?string $OgrLabel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAvailableForContribution(): ?bool
    {
        return $this->availableForContribution;
    }

    public function setAvailableForContribution(bool $availableForContribution): self
    {
        $this->availableForContribution = $availableForContribution;

        return $this;
    }

    public function getNameForContribution(): ?string
    {
        return $this->nameForContribution;
    }

    public function setNameForContribution(?string $nameForContribution): self
    {
        $this->nameForContribution = $nameForContribution;

        return $this;
    }

    public function getNameForContributionSlug(): ?string
    {
        return $this->nameForContributionSlug;
    }

    public function setNameForContributionSlug(?string $nameForContributionSlug): self
    {
        $this->nameForContributionSlug = $nameForContributionSlug;

        return $this;
    }

    public function getAvailableForUser(): ?bool
    {
        return $this->availableForUser;
    }

    public function setAvailableForUser(bool $availableForUser): self
    {
        $this->availableForUser = $availableForUser;

        return $this;
    }

    public function getNameForUser(): ?string
    {
        return $this->nameForUser;
    }

    public function setNameForUser(?string $nameForUser): self
    {
        $this->nameForUser = $nameForUser;

        return $this;
    }

    public function getNameForUserSlug(): ?string
    {
        return $this->nameForUserSlug;
    }

    public function setNameForUserSlug(?string $nameForUserSlug): self
    {
        $this->nameForUserSlug = $nameForUserSlug;

        return $this;
    }

    public function getSalaryDescription(): ?string
    {
        return $this->salaryDescription;
    }

    public function setSalaryDescription(?string $salaryDescription): self
    {
        $this->salaryDescription = $salaryDescription;

        return $this;
    }

    public function getSalaryFormation(): ?string
    {
        return $this->salaryFormation;
    }

    public function setSalaryFormation(?string $salaryFormation): self
    {
        $this->salaryFormation = $salaryFormation;

        return $this;
    }

    public function getSalaryStandardMission(): ?string
    {
        return $this->salaryStandardMission;
    }

    public function setSalaryStandardMission(?string $salaryStandardMission): self
    {
        $this->salaryStandardMission = $salaryStandardMission;

        return $this;
    }

    public function getSalarySkills(): ?array
    {
        return $this->salarySkills;
    }

    public function setSalarySkills(array $salarySkills): self
    {
        $this->salarySkills = $salarySkills;

        return $this;
    }

    public function getSalarySeoMetaTitle(): ?string
    {
        return $this->salarySeoMetaTitle;
    }

    public function setSalarySeoMetaTitle(?string $salarySeoMetaTitle): self
    {
        $this->salarySeoMetaTitle = $salarySeoMetaTitle;

        return $this;
    }

    public function getSalarySeoMetaDescription(): ?string
    {
        return $this->salarySeoMetaDescription;
    }

    public function setSalarySeoMetaDescription(?string $salarySeoMetaDescription): self
    {
        $this->salarySeoMetaDescription = $salarySeoMetaDescription;

        return $this;
    }

    public function getFaqPrice(): ?string
    {
        return $this->faqPrice;
    }

    public function setFaqPrice(?string $faqPrice): self
    {
        $this->faqPrice = $faqPrice;

        return $this;
    }

    public function getFaqDefinition(): ?string
    {
        return $this->faqDefinition;
    }

    public function setFaqDefinition(?string $faqDefinition): self
    {
        $this->faqDefinition = $faqDefinition;

        return $this;
    }

    public function getFaqMissions(): ?string
    {
        return $this->faqMissions;
    }

    public function setFaqMissions(?string $faqMissions): self
    {
        $this->faqMissions = $faqMissions;

        return $this;
    }

    public function getFaqSkills(): ?string
    {
        return $this->faqSkills;
    }

    public function setFaqSkills(?string $faqSkills): self
    {
        $this->faqSkills = $faqSkills;

        return $this;
    }

    public function getFaqProfile(): ?string
    {
        return $this->faqProfile;
    }

    public function setFaqProfile(?string $faqProfile): self
    {
        $this->faqProfile = $faqProfile;

        return $this;
    }

    public function getFaqSeoMetaTitle(): ?string
    {
        return $this->faqSeoMetaTitle;
    }

    public function setFaqSeoMetaTitle(?string $faqSeoMetaTitle): self
    {
        $this->faqSeoMetaTitle = $faqSeoMetaTitle;

        return $this;
    }

    public function getFaqSeoMetaDescription(): ?string
    {
        return $this->faqSeoMetaDescription;
    }

    public function setFaqSeoMetaDescription(?string $faqSeoMetaDescription): self
    {
        $this->faqSeoMetaDescription = $faqSeoMetaDescription;

        return $this;
    }

    public function getCategory(): ?JobCategory
    {
        return $this->category;
    }

    public function setCategory(?JobCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getFaqDescription(): ?string
    {
        return $this->faqDescription;
    }

    public function setFaqDescription(?string $faqDescription): self
    {
        $this->faqDescription = $faqDescription;

        return $this;
    }

    public function getParentForContribution(): ?self
    {
        return $this->parentForContribution;
    }

    public function setParentForContribution(?self $parentForContribution): self
    {
        $this->parentForContribution = $parentForContribution;

        return $this;
    }

    public function getRomeCode(): ?string
    {
        return $this->RomeCode;
    }

    public function setRomeCode(?string $RomeCode): self
    {
        $this->RomeCode = $RomeCode;

        return $this;
    }

    public function getOgrCode(): ?string
    {
        return $this->OgrCode;
    }

    public function setOgrCode(?string $OgrCode): self
    {
        $this->OgrCode = $OgrCode;

        return $this;
    }

    public function getOgrLabel(): ?string
    {
        return $this->OgrLabel;
    }

    public function setOgrLabel(?string $OgrLabel): self
    {
        $this->OgrLabel = $OgrLabel;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName() ?: '';
    }
}
