<?php

namespace App\JobPosting\Traits;

use App\Company\Entity\CompanyBusinessActivity;
use App\JobPosting\Enum\PublishedSince;
use App\JobPosting\Enum\RemoteMode;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait JobPostingRecruiterSearchFiltersTrait
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"job_posting_recruiter_search_filter:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     * @Assert\Length(maxMessage="generic.length.max", max=255, groups={"job_posting_recruiter_search_filter:write"})
     */
    private ?string $keywords = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @EnumAssert(message="generic.enum.message", class=RemoteMode::class, multiple=true, multipleMessage="generic.enum.multiple", groups={"job_posting_recruiter_search_filter:write"})
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?array $remoteMode = null;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @EnumAssert(message="generic.enum.message", class=PublishedSince::class, groups={"job_posting_recruiter_search_filter:write"})
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?string $publishedSince = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?int $minDailySalary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?int $maxDailySalary = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?string $currency = null;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?int $minDuration;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?int $maxDuration;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?bool $intercontractOnly = true;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?\DateTimeInterface $startsAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=CompanyBusinessActivity::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"job_posting_recruiter_search_filter:get", "job_posting_recruiter_search_filter:write"})
     */
    private ?CompanyBusinessActivity $businessActivity = null;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    public ?Recruiter $recruiter = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\IpTraceable(on="create")
     */
    private ?string $ip = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

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

    public function getPublishedSince(): ?string
    {
        return $this->publishedSince;
    }

    public function setPublishedSince(?string $publishedSince): self
    {
        $this->publishedSince = $publishedSince;

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

    public function getIntercontractOnly(): ?bool
    {
        return $this->intercontractOnly;
    }

    public function setIntercontractOnly(?bool $intercontractOnly): self
    {
        $this->intercontractOnly = $intercontractOnly;

        return $this;
    }

    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(?\DateTimeInterface $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getBusinessActivity(): ?CompanyBusinessActivity
    {
        return $this->businessActivity;
    }

    public function setBusinessActivity(?CompanyBusinessActivity $companyBusinessActivity): self
    {
        $this->businessActivity = $companyBusinessActivity;

        return $this;
    }

    public function getRecruiter(): ?Recruiter
    {
        return $this->recruiter;
    }

    public function setRecruiter(?Recruiter $recruiter): self
    {
        $this->recruiter = $recruiter;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }
}
