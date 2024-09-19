<?php

namespace App\Core\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Core\Doctrine\Filter\MultipleFieldsSearchFilter;
use App\Core\Repository\SkillRepository;
use App\JobPosting\Entity\JobPosting;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SkillRepository::class)
 * @ApiResource(
 *     itemOperations={"get"},
 *     collectionOperations={
 *          "get",
 *           "get_legacy"={
 *              "method"="GET",
 *              "security"="is_granted('ROLE_LEGACY')",
 *              "path"="/legacy/skills",
 *              "normalization_context"={"groups"={"skill:legacy"}},
 *              "order"={"id"="ASC"},
 *              "cache_headers"={"max_age"=0, "shared_max_age"=0},
 *          },
 *      },
 *     normalizationContext={"groups"={"skill:get"}}
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "jobUsageCount" = "DESC"
 *     }
 * )
 * @ApiFilter(MultipleFieldsSearchFilter::class, properties={"slug", "synonymSlugs"})
 */
class Skill
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"skill:get", "job_posting:get", "company:get", "user:get:private", "application:get", "job_posting_search:get", "trend:get", "user:legacy", "skill:legacy", "job_posting_template:get", "user:get:candidates", "company:patch:directory"})
     */
    private ?int $id = 0;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\Length(maxMessage="generic.length.max", max="128", groups={"user:put", "user:patch", "user:put:skills_and_languages"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:put", "user:patch", "user:put:skills_and_languages"})
     * @Groups({
     *     "skill:get",
     *     "job_posting:get",
     *     "company:get",
     *     "user:get:private",
     *     "application:get",
     *     "job_posting_search:get",
     *     "user:put:skills_and_languages",
     *     "trend:get",
     *     "user:legacy",
     *     "skill:legacy",
     *     "job_posting_template:get",
     *     "user:get:candidates",
     *     "company:patch:directory",
     *     "user:get_turnover:collection"
     * })
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     * @Groups({
     *     "skill:get",
     *     "job_posting:get",
     *     "company:get",
     *     "user:get:private",
     *     "application:get",
     *     "job_posting_search:get",
     *     "trend:get",
     *     "job_posting_template:get",
     *     "user:get:candidates",
     *     "company:patch:directory"
     * })
     */
    private string $slug;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"skill:get"})
     */
    private int $jobUsageCount = 0;

    /**
     * @ORM\ManyToMany(targetEntity=JobPosting::class, mappedBy="skills")
     */
    private Collection $jobPostings;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $displayed = false;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private array $synonymSlugs = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $processed = false;

    public function __construct()
    {
        $this->jobPostings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getJobUsageCount(): int
    {
        return $this->jobUsageCount;
    }

    public function setJobUsageCount(int $jobUsageCount): self
    {
        $this->jobUsageCount = $jobUsageCount;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return Collection|JobPosting[]
     */
    public function getJobPostings(): Collection
    {
        return $this->jobPostings;
    }

    public function addJobPosting(JobPosting $jobPosting): self
    {
        if (!$this->jobPostings->contains($jobPosting)) {
            $this->jobPostings[] = $jobPosting;
            $jobPosting->addSkill($this);
        }

        return $this;
    }

    public function removeJobPosting(JobPosting $jobPosting): self
    {
        if ($this->jobPostings->removeElement($jobPosting)) {
            $jobPosting->removeSkill($this);
        }

        return $this;
    }

    public function getDisplayed(): bool
    {
        return $this->displayed;
    }

    public function setDisplayed(bool $displayed): self
    {
        $this->displayed = $displayed;

        return $this;
    }

    public function getSynonymSlugs(): array
    {
        return $this->synonymSlugs;
    }

    public function setSynonymSlugs(array $synonymSlugs): self
    {
        $this->synonymSlugs = $synonymSlugs;

        return $this;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): self
    {
        $this->processed = $processed;

        return $this;
    }
}
