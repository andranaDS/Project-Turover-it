<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\Company\Entity\Company;
use App\Core\Entity\Location;
use App\FeedRss\Entity\FeedRss;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\ExperienceLevel;
use App\JobPosting\Enum\RemoteMode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AbstractLinkedinDTO
{
    public const SALARY_PERIOD_DAILY = 'daily';
    public const SALARY_PERIOD_YEARLY = 'yearly';

    public const JOB_TYPE_FULL_TIME = 'FULLTIME';
    public const JOB_TYPE_INTERNSHIP = 'INTERNSHIP';

    public const WORKPLACE_TYPE_ON_SITE = 'On-site';
    public const WORKPLACE_TYPE_HYBRID = 'Hybrid';
    public const WORKPLACE_TYPE_REMOTE = 'Remote';

    public const EXPERIENCE_ENTRY_LEVEL = 'ENTRY_LEVEL';
    public const EXPERIENCE_MID_SENIOR_LEVEL = 'MID_SENIOR_LEVEL';

    protected ?string $partnerJobId = null;
    protected ?string $company = null;
    protected ?string $title = null;
    protected ?string $description = null;
    protected string $applyUrl;
    protected ?string $location = null;
    protected ?string $city = null;
    protected ?string $state = null;
    protected ?string $country = null;
    protected ?string $postalCode = null;
    protected ?int $salaryHighEndAmount = null;
    protected ?int $salaryLowEndAmount = null;
    protected ?string $salaryCurrency = null;
    protected ?string $salaryPeriod = null;
    protected string $workplaceTypes;

    /** FW field */
    protected ?string $date = null;
    protected ?string $experienceLevel = null;
    protected string $jobType;

    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        $this
            ->setPartnerJobId((string) $jobPosting->getId())
            ->setCompany($jobPosting->getCompany())
            ->setTitle($jobPosting->getTitle())
            ->setDescription($jobPosting->getDescription())
            ->setApplyUrl($jobPosting, $router, $feedRss->getGaTag())
            ->setLocation($jobPosting->getLocation())
            ->setCity($jobPosting->getLocation())
            ->setState($jobPosting->getLocation()->getAdminLevel1())
            ->setCountry($jobPosting->getLocation()->getCountryCode())
            ->setPostalCode($jobPosting->getLocation()->getPostalCode())
            ->setDate($jobPosting->getPublishedAt())
            ->setSalaryCurrency($jobPosting->getCurrency())
            ->setJobType($jobPosting->getContracts())
            ->setWorkplaceTypes($jobPosting->getRemoteMode())
            ->setExperienceLevel($jobPosting->getExperienceLevel())
        ;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company?->getName();

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getApplyUrl(): string
    {
        return $this->applyUrl;
    }

    public function setApplyUrl(JobPosting $jobPosting, RouterInterface $router, ?string $gaTag): self
    {
        $url = $router->generate(
            'candidates_job_posting',
            [
                'JobSlug' => null !== $jobPosting->getJob() ? $jobPosting->getJob()->getSlug() : '',
                'jobPostingSlug' => $jobPosting->getSlug(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        if (null !== $gaTag) {
            $url .= '?' . $gaTag;
        }

        $this->applyUrl = $url;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        if (null !== $location->getCountryCode()) {
            $this->location = match (strtolower($location->getCountryCode())) {
                'us' => sprintf('%s, %s)', $location->getLocality() ?? $location->getAdminLevel2(), $location->getAdminLevel1()),
                default => sprintf('%s, %s)', $location->getLocality() ?? $location->getAdminLevel2(), $location->getCountry()),
            };
        }

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(Location $location): self
    {
        $city = $location->getLocality();

        if (null === $city) {
            $city = $location->getAdminLevel2();
        }

        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getSalaryHighEndAmount(): ?int
    {
        return $this->salaryHighEndAmount;
    }

    public function setSalaryHighEndAmount(?int $salaryHighEndAmount): self
    {
        $this->salaryHighEndAmount = $salaryHighEndAmount;

        return $this;
    }

    public function getSalaryLowEndAmount(): ?int
    {
        return $this->salaryLowEndAmount;
    }

    public function setSalaryLowEndAmount(?int $salaryLowEndAmount): self
    {
        $this->salaryLowEndAmount = $salaryLowEndAmount;

        return $this;
    }

    public function getSalaryPeriod(): ?string
    {
        return $this->salaryPeriod;
    }

    public function setSalaryPeriod(string $salaryPeriod): self
    {
        $this->salaryPeriod = $salaryPeriod;

        return $this;
    }

    public function getJobType(): string
    {
        return $this->jobType;
    }

    public function setJobType(?array $contracts): self
    {
        if ($contracts === [Contract::INTERNSHIP]) {
            $this->jobType = self::JOB_TYPE_INTERNSHIP;
        } else {
            $this->jobType = self::JOB_TYPE_FULL_TIME;
        }

        return $this;
    }

    public function getWorkplaceTypes(): string
    {
        return $this->workplaceTypes;
    }

    public function setWorkplaceTypes(?string $workplaceTypes): self
    {
        $this->workplaceTypes = match ($workplaceTypes) {
            RemoteMode::PARTIAL => self::WORKPLACE_TYPE_HYBRID,
            RemoteMode::FULL => self::WORKPLACE_TYPE_REMOTE,
            default => self::WORKPLACE_TYPE_ON_SITE,
        };

        return $this;
    }

    public function getPartnerJobId(): ?string
    {
        return $this->partnerJobId;
    }

    public function setPartnerJobId(?string $partnerJobId): self
    {
        $this->partnerJobId = $partnerJobId;

        return $this;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = null !== $date ? $date->format('r') : $date;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getSalaryCurrency(): ?string
    {
        return $this->salaryCurrency;
    }

    public function setSalaryCurrency(?string $salaryCurrency): self
    {
        $this->salaryCurrency = $salaryCurrency;

        return $this;
    }

    public function getExperienceLevel(): ?string
    {
        return $this->experienceLevel;
    }

    public function setExperienceLevel(?string $experienceLevel): self
    {
        $this->experienceLevel = match ($experienceLevel) {
            ExperienceLevel::JUNIOR => self::EXPERIENCE_ENTRY_LEVEL,
            ExperienceLevel::INTERMEDIATE,ExperienceLevel::SENIOR, ExperienceLevel::EXPERT => self::EXPERIENCE_MID_SENIOR_LEVEL,
            default => self::EXPERIENCE_ENTRY_LEVEL,
        };

        return $this;
    }
}
