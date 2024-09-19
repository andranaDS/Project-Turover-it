<?php

namespace App\Resource\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Entity\Job;
use App\Core\Util\Numbers;
use App\Resource\Controller\JobContributionStatistics\GetItem;
use App\Resource\Repository\JobContributionStatisticsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=JobContributionStatisticsRepository::class)
 * @UniqueEntity(
 *     fields={"day", "job"},
 *     message="There cannot be 2 jobs with the same date."
 * )
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "get_job_contribution_statistics"={
 *              "method"="GET",
 *              "path"="/jobs/{nameForContributionSlug}/statistics",
 *              "controller"=GetItem::class,
 *              "read"=false,
 *              "normalization_context"={"groups"={"job_contribution_statistics:get"}}
 *          },
 *     },
 *     collectionOperations={},
 * )
 */
class JobContributionStatistics
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="date")
     */
    protected \DateTimeInterface $day;

    /**
     * @ORM\ManyToOne (targetEntity=Job::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?Job $job = null;

    /**
     * @ORM\Column(type="json")
     * @Groups({"job_contribution_statistics:get"})
     */
    private array $remoteDaysPerWeekDistributionFree = [];

    /**
     * @ORM\Column(type="json")
     * @Groups({"job_contribution_statistics:get"})
     */
    private array $remoteDaysPerWeekDistributionWork = [];

    /**
     * @ORM\Column(type="json")
     * @Groups({"job_contribution_statistics:get"})
     */
    private array $experienceYearDistributionFree = [];

    /**
     * @ORM\Column(type="json")
     * @Groups({"job_contribution_statistics:get"})
     */
    private array $experienceYearDistributionWork = [];

    /**
     * @ORM\Column(type="json")
     * @Groups({"job_contribution_statistics:get"})
     */
    private array $employerDistributionWork = [];

    /**
     * @ORM\Column(type="json")
     * @Groups({"job_contribution_statistics:get"})
     */
    private array $foundByDistributionFree = [];

    /**
     * @ORM\Column(type="json")
     * @Groups({"job_contribution_statistics:get"})
     */
    private array $contractDurationDistributionFree = [];

    /**
     * @ORM\Column(type="json")
     * @Groups({"job_contribution_statistics:get"})
     */
    private array $contractDistributionWork = [];

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?int $onCallPercentageFree = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?int $onCallPercentageWork = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?int $averageSearchJobDurationFree = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true))
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?int $averageSearchJobDurationWork = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true))
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?int $averageDailySalaryDirectly = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?int $averageDailySalaryWithIntermediary = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true))
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?int $averageAnnualSalaryFinalClient = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true))
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?int $averageAnnualSalaryNonFinalClient = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?array $salaryExperienceDistributionFree = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?array $salaryExperienceDistributionWork = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?array $salaryExperienceLocationDistributionFree = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"job_contribution_statistics:get"})
     */
    private ?array $salaryExperienceLocationDistributionWork = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

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

    public function getDay(): \DateTimeInterface
    {
        return $this->day;
    }

    public function setDay(\DateTimeInterface $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getExperienceYearDistributionFree(): array
    {
        return $this->experienceYearDistributionFree;
    }

    public function setExperienceYearDistributionFree(array $experienceYearDistributionFree): self
    {
        $this->experienceYearDistributionFree = $experienceYearDistributionFree;

        return $this;
    }

    public function getExperienceYearDistributionWork(): array
    {
        return $this->experienceYearDistributionWork;
    }

    public function setExperienceYearDistributionWork(array $experienceYearDistributionWork): self
    {
        $this->experienceYearDistributionWork = $experienceYearDistributionWork;

        return $this;
    }

    public function getEmployerDistributionWork(): array
    {
        return $this->employerDistributionWork;
    }

    public function setEmployerDistributionWork(array $employerDistributionWork): self
    {
        $this->employerDistributionWork = $employerDistributionWork;

        return $this;
    }

    public function getContractDurationDistributionFree(): array
    {
        return $this->contractDurationDistributionFree;
    }

    public function setContractDurationDistributionFree(array $contractDurationDistributionFree): self
    {
        $this->contractDurationDistributionFree = $contractDurationDistributionFree;

        return $this;
    }

    public function getAverageDailySalaryDirectly(): ?int
    {
        return $this->averageDailySalaryDirectly;
    }

    /**
     * @Groups({"job_contribution_statistics:get"})
     */
    public function getFormattedAverageDailySalaryDirectly(): ?string
    {
        return Numbers::formatCurrency($this->averageDailySalaryDirectly);
    }

    public function setAverageDailySalaryDirectly(?int $averageDailySalaryDirectly): self
    {
        $this->averageDailySalaryDirectly = $averageDailySalaryDirectly;

        return $this;
    }

    public function getAverageDailySalaryWithIntermediary(): ?int
    {
        return $this->averageDailySalaryWithIntermediary;
    }

    /**
     * @Groups({"job_contribution_statistics:get"})
     */
    public function getFormattedAverageDailySalaryWithIntermediary(): ?string
    {
        return Numbers::formatCurrency($this->averageDailySalaryWithIntermediary);
    }

    public function setAverageDailySalaryWithIntermediary(?int $averageDailySalaryWithIntermediary): self
    {
        $this->averageDailySalaryWithIntermediary = $averageDailySalaryWithIntermediary;

        return $this;
    }

    public function getAverageAnnualSalaryFinalClient(): ?int
    {
        return $this->averageAnnualSalaryFinalClient;
    }

    /**
     * @Groups({"job_contribution_statistics:get"})
     */
    public function getFormattedAverageAnnualSalaryFinalClient(): ?string
    {
        return Numbers::formatCurrency($this->averageAnnualSalaryFinalClient);
    }

    public function setAverageAnnualSalaryFinalClient(?int $averageAnnualSalaryFinalClient): self
    {
        $this->averageAnnualSalaryFinalClient = $averageAnnualSalaryFinalClient;

        return $this;
    }

    public function getAverageAnnualSalaryNonFinalClient(): ?int
    {
        return $this->averageAnnualSalaryNonFinalClient;
    }

    /**
     * @Groups({"job_contribution_statistics:get"})
     */
    public function getFormattedAverageAnnualSalaryNonFinalClient(): ?string
    {
        return Numbers::formatCurrency($this->averageAnnualSalaryNonFinalClient);
    }

    public function setAverageAnnualSalaryNonFinalClient(?int $averageAnnualSalaryNonFinalClient): self
    {
        $this->averageAnnualSalaryNonFinalClient = $averageAnnualSalaryNonFinalClient;

        return $this;
    }

    public function getSalaryExperienceDistributionFree(): ?array
    {
        return $this->salaryExperienceDistributionFree;
    }

    public function setSalaryExperienceDistributionFree(?array $salaryExperienceDistributionFree): self
    {
        $this->salaryExperienceDistributionFree = $salaryExperienceDistributionFree;

        return $this;
    }

    public function getSalaryExperienceDistributionWork(): ?array
    {
        return $this->salaryExperienceDistributionWork;
    }

    public function setSalaryExperienceDistributionWork(?array $salaryExperienceDistributionWork): self
    {
        $this->salaryExperienceDistributionWork = $salaryExperienceDistributionWork;

        return $this;
    }

    public function getSalaryExperienceLocationDistributionFree(): ?array
    {
        return $this->salaryExperienceLocationDistributionFree;
    }

    public function setSalaryExperienceLocationDistributionFree(?array $salaryExperienceLocationDistributionFree): self
    {
        $this->salaryExperienceLocationDistributionFree = $salaryExperienceLocationDistributionFree;

        return $this;
    }

    public function getSalaryExperienceLocationDistributionWork(): ?array
    {
        return $this->salaryExperienceLocationDistributionWork;
    }

    public function setSalaryExperienceLocationDistributionWork(?array $salaryExperienceLocationDistributionWork): self
    {
        $this->salaryExperienceLocationDistributionWork = $salaryExperienceLocationDistributionWork;

        return $this;
    }

    public function getOnCallPercentageFree(): ?int
    {
        return $this->onCallPercentageFree;
    }

    public function setOnCallPercentageFree(?int $onCallPercentageFree): self
    {
        $this->onCallPercentageFree = $onCallPercentageFree;

        return $this;
    }

    public function getOnCallPercentageWork(): ?int
    {
        return $this->onCallPercentageWork;
    }

    public function setOnCallPercentageWork(?int $onCallPercentageWork): self
    {
        $this->onCallPercentageWork = $onCallPercentageWork;

        return $this;
    }

    public function getRemoteDaysPerWeekDistributionFree(): array
    {
        return $this->remoteDaysPerWeekDistributionFree;
    }

    public function setRemoteDaysPerWeekDistributionFree(array $remoteDaysPerWeekDistributionFree): self
    {
        $this->remoteDaysPerWeekDistributionFree = $remoteDaysPerWeekDistributionFree;

        return $this;
    }

    public function getRemoteDaysPerWeekDistributionWork(): array
    {
        return $this->remoteDaysPerWeekDistributionWork;
    }

    public function setRemoteDaysPerWeekDistributionWork(array $remoteDaysPerWeekDistributionWork): self
    {
        $this->remoteDaysPerWeekDistributionWork = $remoteDaysPerWeekDistributionWork;

        return $this;
    }

    public function getFoundByDistributionFree(): array
    {
        return $this->foundByDistributionFree;
    }

    public function setFoundByDistributionFree(array $foundByDistributionFree): self
    {
        $this->foundByDistributionFree = $foundByDistributionFree;

        return $this;
    }

    public function getAverageSearchJobDurationFree(): ?int
    {
        return $this->averageSearchJobDurationFree;
    }

    public function setAverageSearchJobDurationFree(?int $averageSearchJobDurationFree): self
    {
        $this->averageSearchJobDurationFree = $averageSearchJobDurationFree;

        return $this;
    }

    public function getAverageSearchJobDurationWork(): ?int
    {
        return $this->averageSearchJobDurationWork;
    }

    public function setAverageSearchJobDurationWork(?int $averageSearchJobDurationWork): self
    {
        $this->averageSearchJobDurationWork = $averageSearchJobDurationWork;

        return $this;
    }

    public function getContractDistributionWork(): array
    {
        return $this->contractDistributionWork;
    }

    public function setContractDistributionWork(array $contractDistributionWork): self
    {
        $this->contractDistributionWork = $contractDistributionWork;

        return $this;
    }
}
