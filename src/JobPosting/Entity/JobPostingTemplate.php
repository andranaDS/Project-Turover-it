<?php

namespace App\JobPosting\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Entity\SoftSkill;
use App\Core\Util\Numbers;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\DurationPeriod;
use App\JobPosting\Repository\JobPostingTemplateRepository;
use App\Recruiter\Entity\Recruiter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobPostingTemplateRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ApiResource(
 *      denormalizationContext={
 *          "groups"={"job_posting_template:write"}
 *      },
 *      normalizationContext={
 *          "groups"={"job_posting_template:get"}
 *     },
 *     collectionOperations={
 *          "turnover_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *          },
 *          "turnover_post"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *          },
 *       },
 *       itemOperations={
 *          "turnover_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getCreatedBy().getCompany() == user.getCompany()"
 *          },
 *          "turnover_put"={
 *              "method"="PUT",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getCreatedBy() == user"
 *          },
 *          "turnover_delete"={
 *              "method"="DELETE",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getCreatedBy() == user"
 *          },
 *       }
 * )
 * @ApiFilter(OrderFilter::class, properties={"title"="ASC", "minDailySalary"="ASC", "maxDailySalary"="ASC", "createdAt"="ASC"})
 */
class JobPostingTemplate
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"job_posting_template:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class, inversedBy="jobPostingTemplates")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"job_posting_template:get"})
     * @Gedmo\Blameable(on="create")
     */
    private ?Recruiter $createdBy = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?string $title;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @EnumAssert(message="generic.enum.message", class=Contract::class, multiple=true, multipleMessage="generic.enum.multiple")
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?array $contracts;

    /**
     * @ORM\Embedded(class="App\Core\Entity\Location")
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?Location $location;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?int $minAnnualSalary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?int $maxAnnualSalary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?int $minDailySalary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?int $maxDailySalary = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?string $currency = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?int $durationValue = null;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @EnumAssert(message="generic.enum.message", class=DurationPeriod::class)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?string $durationPeriod = null;

    /**
     * @ORM\ManyToMany(targetEntity=Skill::class)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private Collection $skills;

    /**
     * @ORM\ManyToMany(targetEntity=SoftSkill::class)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private Collection $softSkills;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?string $applicationType = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email(message="generic.email")
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?string $applicationEmail = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="generic.url")
     * @Groups({"job_postin g_template:get", "job_posting_template:write"})
     */
    private ?string $applicationUrl = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"job_posting_template:get", "job_posting_template:write"})
     */
    private ?string $applicationContact = null;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"job_posting_template:get"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"job_posting_template:get"})
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->location = new Location();
        $this->skills = new ArrayCollection();
        $this->softSkills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

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

    public function getContracts(): ?array
    {
        return $this->contracts;
    }

    public function setContracts(array $contracts): self
    {
        $this->contracts = $contracts;

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

    public function getMaxAnnualSalary(): ?int
    {
        return $this->maxAnnualSalary;
    }

    public function setMaxAnnualSalary(?int $maxAnnualSalary): self
    {
        $this->maxAnnualSalary = $maxAnnualSalary;

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

    public function getMaxDailySalary(): ?int
    {
        return $this->maxDailySalary;
    }

    public function setMaxDailySalary(?int $maxDailySalary): self
    {
        $this->maxDailySalary = $maxDailySalary;

        return $this;
    }

    public function getApplicationType(): ?string
    {
        return $this->applicationType;
    }

    public function setApplicationType(?string $applicationType): self
    {
        $this->applicationType = $applicationType;

        return $this;
    }

    public function getApplicationEmail(): ?string
    {
        return $this->applicationEmail;
    }

    public function setApplicationEmail(?string $applicationEmail): self
    {
        $this->applicationEmail = $applicationEmail;

        return $this;
    }

    public function getApplicationUrl(): ?string
    {
        return $this->applicationUrl;
    }

    public function setApplicationUrl(?string $applicationUrl): self
    {
        $this->applicationUrl = $applicationUrl;

        return $this;
    }

    public function getApplicationContact(): ?string
    {
        return $this->applicationContact;
    }

    public function setApplicationContact(?string $applicationContact): self
    {
        $this->applicationContact = $applicationContact;

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        $this->skills->removeElement($skill);

        return $this;
    }

    /**
     * @return Collection<int, SoftSkill>
     */
    public function getSoftSkills(): Collection
    {
        return $this->softSkills;
    }

    public function addSoftSkill(SoftSkill $softSkill): self
    {
        if (!$this->softSkills->contains($softSkill)) {
            $this->softSkills[] = $softSkill;
        }

        return $this;
    }

    public function removeSoftSkill(SoftSkill $softSkill): self
    {
        $this->softSkills->removeElement($softSkill);

        return $this;
    }

    public function getDurationValue(): ?int
    {
        return $this->durationValue;
    }

    public function setDurationValue(?int $durationValue): self
    {
        $this->durationValue = $durationValue;

        return $this;
    }

    public function getDurationPeriod(): ?string
    {
        return $this->durationPeriod;
    }

    public function setDurationPeriod(?string $durationPeriod): self
    {
        $this->durationPeriod = $durationPeriod;

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

    /**
     * @Groups({"job_posting_template:get"})
     */
    public function getDailySalary(): ?string
    {
        return Numbers::formatRangeCurrency($this->minDailySalary, null, $this->currency ?? 'EUR');
    }

    /**
     * @Groups({"job_posting_template:get"})
     */
    public function getAnnualSalary(): ?string
    {
        return Numbers::formatRangeCurrency($this->minAnnualSalary, null, $this->currency ?? 'EUR');
    }

    public function getCreatedBy(): ?Recruiter
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Recruiter $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
