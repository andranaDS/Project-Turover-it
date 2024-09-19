<?php

namespace App\Resource\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Entity\Job;
use App\Core\Util\Numbers;
use App\JobPosting\Enum\Contract;
use App\Resource\Controller\Contribution\GetItem;
use App\Resource\Enum\Employer;
use App\Resource\Enum\FoundBy;
use App\Resource\Enum\Location;
use App\Resource\Enum\UserCompanyStatus;
use App\Resource\Repository\ContributionRepository;
use App\Resource\Validator\ContributionValidationGroups;
use App\User\Entity\User;
use App\User\Enum\ExperienceYear;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ContributionRepository::class)
 * @ApiResource(
 *     order={"createdAt"="DESC"},
 *     normalizationContext={
 *          "groups"={"contribution:get"},
 *      },
 *     itemOperations={
 *          "get"={
 *              "controller"=GetItem::class,
 *          },
 *      },
 *     collectionOperations={
 *          "post"={
 *              "security"="is_granted('ROLE_USER')",
 *              "validation_groups"={ContributionValidationGroups::class, "validationGroups"},
 *          },
 *          "get",
 *      },
 * )
 * @ApiFilter(OrderFilter::class, properties={"createdAt"="ASC"})
 * @ApiFilter(SearchFilter::class, properties={"contract"="exact", "job"="exact"})
 */
class Contribution
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"contribution:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull(message="generic.not_null")
     * @Groups({"contribution:get"})
     */
    private ?Job $job = null;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"contribution:post:free"})
     * @Assert\Length(maxMessage="generic.length.max", max=16, groups={"contribution:post:free"})
     * @EnumAssert(message="generic.enum.message", class=UserCompanyStatus::class, groups={"contribution:post:free"})
     * @Groups({"contribution:get"})
     */
    private ?string $userCompanyStatus = null;

    /**
     * @ORM\Column(type="string", length=24)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=24)
     * @EnumAssert(message="generic.enum.message", class=Contract::class)
     * @Groups({"contribution:get"})
     */
    private ?string $contract = null;

    /**
     * @ORM\Column(type="string", length=24)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=24)
     * @EnumAssert(message="generic.enum.message", class=Location::class)
     * @Groups({"contribution:get"})
     */
    private ?string $location = null;

    /**
     * @ORM\Column(type="string", length=24)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=24)
     * @EnumAssert(message="generic.enum.message", class=ExperienceYear::class)
     * @Groups({"contribution:get"})
     */
    private ?string $experienceYear = null;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=24)
     * @EnumAssert(message="generic.enum.message", class=Employer::class)
     * @Groups({"contribution:get"})
     */
    private ?string $employer = null;

    /**
     * @ORM\Column(type="string", length=12)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=12)
     * @EnumAssert(message="generic.enum.message", class=FoundBy::class)
     * @Groups({"contribution:get"})
     */
    private ?string $foundBy = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\NotNull(message="generic.not_null")
     * @Assert\Type(type="bool")
     * @Groups({"contribution:get"})
     */
    private ?bool $onCall = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"contribution:post:work"})
     * @Assert\Positive(message="generic.positive", groups={"contribution:post:work"})
     * @Groups({"contribution:get"})
     */
    private ?int $annualSalary = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Assert\PositiveOrZero(message="generic.positive_or_zero", groups={"contribution:post:work"})
     * @Assert\NotNull(message="generic.not_null", groups={"contribution:post:work"})
     * @Groups({"contribution:get"})
     */
    private ?int $variableAnnualSalary = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Assert\Positive(message="generic.positive", groups={"contribution:post:free"})
     * @Assert\NotNull(message="generic.not_null", groups={"contribution:post:free"})
     * @Groups({"contribution:get"})
     */
    private ?int $dailySalary = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    private ?User $createdBy = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Range(notInRangeMessage="generic.range.not_in_range_message", min=0, max=5)
     * @Groups({"contribution:get"})
     */
    private ?int $remoteDaysPerWeek = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"contribution:post:none-permanent"})
     * @Assert\Positive(message="generic.positive", groups={"contribution:post:none-permanent"})
     * @Groups({"contribution:get"})
     */
    private ?int $contractDuration = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\PositiveOrZero(message="generic.positive_or_zero")
     * @Groups({"contribution:get"})
     */
    private ?int $searchJobDuration = null;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"contribution:get"})
     */
    protected \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldFreelanceInfoId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setContract(string $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getContract(): ?string
    {
        return $this->contract;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setExperienceYear(?string $experienceYear): self
    {
        $this->experienceYear = $experienceYear;

        return $this;
    }

    public function getExperienceYear(): ?string
    {
        return $this->experienceYear;
    }

    public function setOnCall(?bool $onCall): self
    {
        $this->onCall = $onCall;

        return $this;
    }

    public function getAnnualSalary(): ?int
    {
        return $this->annualSalary;
    }

    /**
     * @Groups({"contribution:get"})
     */
    public function getFormattedAnnualSalary(): ?string
    {
        return Numbers::formatCurrency($this->annualSalary);
    }

    public function setAnnualSalary(?int $annualSalary): self
    {
        $this->annualSalary = $annualSalary;

        return $this;
    }

    public function getVariableAnnualSalary(): ?int
    {
        return $this->variableAnnualSalary;
    }

    /**
     * @Groups({"contribution:get"})
     */
    public function getFormattedVariableAnnualSalary(): ?string
    {
        return Numbers::formatCurrency($this->variableAnnualSalary);
    }

    public function setVariableAnnualSalary(?int $variableAnnualSalary): self
    {
        $this->variableAnnualSalary = $variableAnnualSalary;

        return $this;
    }

    public function getDailySalary(): ?int
    {
        return $this->dailySalary;
    }

    /**
     * @Groups({"contribution:get"})
     */
    public function getFormattedDailySalary(): ?string
    {
        return Numbers::formatCurrency($this->dailySalary);
    }

    public function setDailySalary(?int $dailySalary): self
    {
        $this->dailySalary = $dailySalary;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getRemoteDaysPerWeek(): ?int
    {
        return $this->remoteDaysPerWeek;
    }

    public function setRemoteDaysPerWeek(?int $remoteDaysPerWeek): self
    {
        $this->remoteDaysPerWeek = $remoteDaysPerWeek;

        return $this;
    }

    public function getContractDuration(): ?int
    {
        return $this->contractDuration;
    }

    public function setContractDuration(?int $contractDuration): self
    {
        $this->contractDuration = $contractDuration;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getSearchJobDuration(): ?int
    {
        return $this->searchJobDuration;
    }

    public function setSearchJobDuration(?int $searchJobDuration): self
    {
        $this->searchJobDuration = $searchJobDuration;

        return $this;
    }

    public function getUserCompanyStatus(): ?string
    {
        return $this->userCompanyStatus;
    }

    public function setUserCompanyStatus(?string $userCompanyStatus): self
    {
        $this->userCompanyStatus = $userCompanyStatus;

        return $this;
    }

    public function getEmployer(): ?string
    {
        return $this->employer;
    }

    public function setEmployer(?string $employer): self
    {
        $this->employer = $employer;

        return $this;
    }

    public function getFoundBy(): ?string
    {
        return $this->foundBy;
    }

    public function setFoundBy(?string $foundBy): self
    {
        $this->foundBy = $foundBy;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $dateTime): self
    {
        $this->createdAt = $dateTime;

        return $this;
    }

    public function getOnCall(): ?bool
    {
        return $this->onCall;
    }

    /**
     * @Groups({"contribution:get"})
     */
    public function getIsFreelance(): bool
    {
        return Contract::isFree($this->contract);
    }

    public function getOldFreelanceInfoId(): ?int
    {
        return $this->oldFreelanceInfoId;
    }

    public function setOldFreelanceInfoId(?int $oldFreelanceInfoId): self
    {
        $this->oldFreelanceInfoId = $oldFreelanceInfoId;

        return $this;
    }
}
