<?php

namespace App\Sync\Entity;

use App\Company\Entity\Company;
use App\JobPosting\Entity\JobPosting;
use App\Sync\Repository\SyncLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=SyncLogRepository::class)
 * @ORM\Table(indexes={
 *      @ORM\Index(columns={"old_company_id"}),
 *      @ORM\Index(columns={"old_job_posting_id"}),
 *      @ORM\Index(columns={"mode"}),
 *      @ORM\Index(columns={"requested_at"}),
 * })
 */
class SyncLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldCompanyId;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?Company $newCompany;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldJobPostingId;

    /**
     * @ORM\ManyToOne(targetEntity=JobPosting::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?JobPosting $newJobPosting;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private ?string $source;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private ?string $mode;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $inData;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $outData;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $warnings;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $errors;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $requestedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $processedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(?string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getInData(): ?array
    {
        return $this->inData;
    }

    public function setInData(?array $inData): self
    {
        $this->inData = $inData;

        return $this;
    }

    public function getOutData(): ?array
    {
        return $this->outData;
    }

    public function setOutData(?array $outData): self
    {
        $this->outData = $outData;

        return $this;
    }

    public function getWarnings(): ?array
    {
        return $this->warnings;
    }

    public function setWarnings(?array $warnings): self
    {
        $this->warnings = $warnings;

        return $this;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function setErrors(?array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRequestedAt(): ?\DateTimeInterface
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(?\DateTimeInterface $requestedAt): self
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getProcessedAt(): ?\DateTimeInterface
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeInterface $processedAt): self
    {
        $this->processedAt = $processedAt;

        return $this;
    }

    public function getOldCompanyId(): ?int
    {
        return $this->oldCompanyId;
    }

    public function setOldCompanyId(?int $oldCompanyId): self
    {
        $this->oldCompanyId = $oldCompanyId;

        return $this;
    }

    public function getOldJobPostingId(): ?int
    {
        return $this->oldJobPostingId;
    }

    public function setOldJobPostingId(?int $oldJobPostingId): self
    {
        $this->oldJobPostingId = $oldJobPostingId;

        return $this;
    }

    public function getNewCompany(): ?Company
    {
        return $this->newCompany;
    }

    public function setNewCompany(?Company $newCompany): self
    {
        $this->newCompany = $newCompany;

        return $this;
    }

    public function getNewJobPosting(): ?JobPosting
    {
        return $this->newJobPosting;
    }

    public function setNewJobPosting(?JobPosting $newJobPosting): self
    {
        $this->newJobPosting = $newJobPosting;

        return $this;
    }
}
