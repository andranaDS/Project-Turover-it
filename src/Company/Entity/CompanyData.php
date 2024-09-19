<?php

namespace App\Company\Entity;

use App\Company\Repository\CompanyDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CompanyDataRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"job_postings_published_count"})})
 */
class CompanyData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\OneToOne(targetEntity=Company::class, mappedBy="data")
     */
    private ?Company $company;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "company:get"})
     */
    private int $jobPostingsTotalCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "company:get"})
     */
    private int $jobPostingsFreeTotalCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "company:get"})
     */
    private int $jobPostingsWorkTotalCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "job_posting:get:item", "company:get:homepage", "company:get"})
     */
    private int $jobPostingsPublishedCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "company:get"})
     */
    private int $jobPostingsFreePublishedCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "company:get"})
     */
    private int $jobPostingsWorkPublishedCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "company:get"})
     */
    private int $jobPostingsIntercontractTotalCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "company:get"})
     */
    private int $jobPostingsIntercontractPublishedCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "company:get"})
     */
    private int $usersCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"company:get:item", "company:get"})
     */
    private int $usersVisibleCount = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $lastJobPostingDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJobPostingsTotalCount(): ?int
    {
        return $this->jobPostingsTotalCount;
    }

    public function setJobPostingsTotalCount(int $jobPostingsTotalCount): self
    {
        $this->jobPostingsTotalCount = $jobPostingsTotalCount;

        return $this;
    }

    public function getJobPostingsFreeTotalCount(): ?int
    {
        return $this->jobPostingsFreeTotalCount;
    }

    public function setJobPostingsFreeTotalCount(int $jobPostingsFreeTotalCount): self
    {
        $this->jobPostingsFreeTotalCount = $jobPostingsFreeTotalCount;

        return $this;
    }

    public function getJobPostingsWorkTotalCount(): ?int
    {
        return $this->jobPostingsWorkTotalCount;
    }

    public function setJobPostingsWorkTotalCount(int $jobPostingsWorkTotalCount): self
    {
        $this->jobPostingsWorkTotalCount = $jobPostingsWorkTotalCount;

        return $this;
    }

    public function getJobPostingsPublishedCount(): ?int
    {
        return $this->jobPostingsPublishedCount;
    }

    public function setJobPostingsPublishedCount(int $jobPostingsPublishedCount): self
    {
        $this->jobPostingsPublishedCount = $jobPostingsPublishedCount;

        return $this;
    }

    public function getJobPostingsFreePublishedCount(): ?int
    {
        return $this->jobPostingsFreePublishedCount;
    }

    public function setJobPostingsFreePublishedCount(int $jobPostingsFreePublishedCount): self
    {
        $this->jobPostingsFreePublishedCount = $jobPostingsFreePublishedCount;

        return $this;
    }

    public function getJobPostingsWorkPublishedCount(): ?int
    {
        return $this->jobPostingsWorkPublishedCount;
    }

    public function setJobPostingsWorkPublishedCount(int $jobPostingsWorkPublishedCount): self
    {
        $this->jobPostingsWorkPublishedCount = $jobPostingsWorkPublishedCount;

        return $this;
    }

    public function getLastJobPostingDate(): ?\DateTimeInterface
    {
        return $this->lastJobPostingDate;
    }

    public function setLastJobPostingDate(?\DateTimeInterface $lastJobPostingDate): self
    {
        $this->lastJobPostingDate = $lastJobPostingDate;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        // unset the owning side of the relation if necessary
        if (null === $company && null !== $this->company) {
            $this->company->setData(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $company && $company->getData() !== $this) {
            $company->setData($this);
        }

        $this->company = $company;

        return $this;
    }

    public function getUsersCount(): ?int
    {
        return $this->usersCount;
    }

    public function setUsersCount(int $usersCount): self
    {
        $this->usersCount = $usersCount;

        return $this;
    }

    public function getJobPostingsIntercontractTotalCount(): ?int
    {
        return $this->jobPostingsIntercontractTotalCount;
    }

    public function setJobPostingsIntercontractTotalCount(int $jobPostingsIntercontractTotalCount): self
    {
        $this->jobPostingsIntercontractTotalCount = $jobPostingsIntercontractTotalCount;

        return $this;
    }

    public function getJobPostingsIntercontractPublishedCount(): ?int
    {
        return $this->jobPostingsIntercontractPublishedCount;
    }

    public function setJobPostingsIntercontractPublishedCount(int $jobPostingsIntercontractPublishedCount): self
    {
        $this->jobPostingsIntercontractPublishedCount = $jobPostingsIntercontractPublishedCount;

        return $this;
    }

    public function getUsersVisibleCount(): int
    {
        return $this->usersVisibleCount;
    }

    public function setUsersVisibleCount(int $usersVisibleCount): self
    {
        $this->usersVisibleCount = $usersVisibleCount;

        return $this;
    }
}
