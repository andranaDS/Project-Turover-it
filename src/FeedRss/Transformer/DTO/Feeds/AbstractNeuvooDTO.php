<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\Company\Entity\Company;
use App\Core\Entity\Location;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\ExperienceLevel;
use App\JobPosting\Enum\RemoteMode;
use Carbon\Carbon;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AbstractNeuvooDTO
{
    public const JOB_TYPE_CONTRACT = 'contract';
    public const JOB_TYPE_INTERNSHIP = 'internship';
    public const JOB_TYPE_CDD = 'cdd';
    public const JOB_TYPE_CDI = 'cdi';
    public const APPRENTICESHIP = 'apprenticeship';
    public const JOB_TYPE_FULL_TIME = 'full_time';

    public const REMOTE_YES = 'yes';
    public const REMOTE_NO = 'no';

    public const EXPERIENCE_PERIOD_YEAR = 'year';

    public const SALARY_PERIOD_YEAR = 'year';
    public const SALARY_PERIOD_DAY = 'day';

    public const DEFAULT_CATEGORY = 'IT';

    protected ?string $referenceNumber = null;
    protected ?string $title = null;
    protected ?string $company = null;
    protected ?string $city = null;
    protected ?string $state = null;
    protected ?string $country = null;
    protected ?string $datePosted = null;
    protected string $url;
    protected ?string $description = null;
    protected ?string $postalCode = null;
    protected ?string $jobType = null;
    protected ?string $remote = null;
    protected ?string $category = null;
    protected ?string $experienceMax = null;
    protected ?string $experienceMin = null;
    protected ?string $experiencePeriod = null;
    protected ?string $salaryMin = null;
    protected ?string $salaryMax = null;
    protected ?string $salaryCurrency = null;
    protected ?string $salaryPeriod = null;

    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        $this
            ->setReferenceNumber($jobPosting->getId())
            ->setTitle($jobPosting->getTitle())
            ->setCompany($jobPosting->getCompany())
            ->setCity($jobPosting->getLocation())
            ->setState($jobPosting->getLocation())
            ->setCountry($jobPosting->getLocation()->getCountry())
            ->setDatePosted($jobPosting->getPublishedAt())
            ->setUrl($jobPosting, $router, $feedRss->getGaTag())
            ->setDescription($jobPosting->getDescription())
            ->setPostalCode($jobPosting->getLocation()->getPostalCode())
            ->setJobType($jobPosting->getContracts())
            ->setRemote($jobPosting->getRemoteMode())
            ->setCategory(self::DEFAULT_CATEGORY)
            ->setExperienceMax($jobPosting->getExperienceLevel())
            ->setExperienceMin($jobPosting->getExperienceLevel())
            ->setExperiencePeriod(self::EXPERIENCE_PERIOD_YEAR)
            ->setSalaryPeriod(self::SALARY_PERIOD_YEAR)
            ->setSalaryCurrency($jobPosting->getCurrency())

        ;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = RssTransformer::transformForRss($title);

        return $this;
    }

    public function getDatePosted(): ?string
    {
        return $this->datePosted;
    }

    public function setDatePosted(?\DateTimeInterface $date): self
    {
        $this->datePosted = RssTransformer::transformForRss(null !== $date ? Carbon::createFromTimestamp($date->getTimestamp())->toIso8601String() : $date);

        return $this;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(?int $referenceNumber): self
    {
        $this->referenceNumber = RssTransformer::transformForRss((string) $referenceNumber);

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(JobPosting $jobPosting, RouterInterface $router, ?string $gaTag): self
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

        $this->url = RssTransformer::transformForRss($url);

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = RssTransformer::transformForRss($company?->getName());

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

        $this->city = RssTransformer::transformForRss($city);

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(Location $location): self
    {
        $state = $location->getAdminLevel1();

        if (null === $state) {
            $state = $location->getAdminLevel2();
        }

        $this->state = RssTransformer::transformForRss($state);

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = RssTransformer::transformForRss($country);

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        if (null !== $postalCode) {
            $this->postalCode = RssTransformer::transformForRss($postalCode);
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = RssTransformer::transformForRss($description);

        return $this;
    }

    public function getJobType(): ?string
    {
        return $this->jobType;
    }

    public function setJobType(?array $contracts): self
    {
        $jobType = match ($contracts) {
            [Contract::INTERNSHIP] => self::JOB_TYPE_INTERNSHIP,
            [Contract::CONTRACTOR] => self::JOB_TYPE_CONTRACT,
            [Contract::FIXED_TERM] => self::JOB_TYPE_CDD,
            [Contract::PERMANENT] => self::JOB_TYPE_CDI,
            [Contract::APPRENTICESHIP] => self::APPRENTICESHIP,
            default => self::JOB_TYPE_FULL_TIME,
        };

        $this->jobType = RssTransformer::transformValueTranslated(sprintf('neuvoo.job_type.%s', $jobType));

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getRemote(): ?string
    {
        return $this->remote;
    }

    public function setRemote(?string $remoteMode): self
    {
        $isRemote = match ($remoteMode) {
            RemoteMode::PARTIAL, RemoteMode::FULL => self::REMOTE_YES,
            default => self::REMOTE_NO,
        };

        $this->remote = RssTransformer::transformForRss($isRemote);

        return $this;
    }

    public function getExperienceMax(): ?string
    {
        return $this->experienceMax;
    }

    public function setExperienceMax(?string $experienceLevel): self
    {
        $experienceMax = match ($experienceLevel) {
            ExperienceLevel::JUNIOR => 2,
            ExperienceLevel::INTERMEDIATE => 5,
            ExperienceLevel::SENIOR => 10,
            ExperienceLevel::EXPERT => null,
            default => null,
        };

        if (null !== $experienceMax) {
            $this->experienceMax = RssTransformer::transformForRss((string) $experienceMax);
        }

        return $this;
    }

    public function getExperienceMin(): ?string
    {
        return $this->experienceMin;
    }

    public function setExperienceMin(?string $experienceLevel): self
    {
        $experienceMin = match ($experienceLevel) {
            ExperienceLevel::JUNIOR => null,
            ExperienceLevel::INTERMEDIATE => 2,
            ExperienceLevel::SENIOR => 5,
            ExperienceLevel::EXPERT => 10,
            default => null,
        };

        if (null !== $experienceMin) {
            $this->experienceMin = RssTransformer::transformForRss((string) $experienceMin);
        }

        return $this;
    }

    public function getExperiencePeriod(): ?string
    {
        return $this->experiencePeriod;
    }

    public function setCategory(string $category): self
    {
        $this->category = RssTransformer::transformForRss($category);

        return $this;
    }

    public function setExperiencePeriod(?string $experiencePeriod): self
    {
        $this->experiencePeriod = RssTransformer::transformForRss($experiencePeriod);

        return $this;
    }

    public function getSalaryMin(): ?string
    {
        return $this->salaryMin;
    }

    public function setSalaryMin(?int $salaryMin): self
    {
        if (null !== $salaryMin) {
            $this->salaryMin = RssTransformer::transformForRss((string) $salaryMin);
        }

        return $this;
    }

    public function getSalaryMax(): ?string
    {
        return $this->salaryMax;
    }

    public function setSalaryMax(?int $salaryMax): self
    {
        if (null !== $salaryMax) {
            $this->salaryMax = RssTransformer::transformForRss((string) $salaryMax);
        }

        return $this;
    }

    public function getSalaryCurrency(): ?string
    {
        return $this->salaryCurrency;
    }

    public function setSalaryCurrency(?string $salaryCurrency): self
    {
        $this->salaryCurrency = RssTransformer::transformForRss($salaryCurrency);

        return $this;
    }

    public function getSalaryPeriod(): ?string
    {
        return $this->salaryPeriod;
    }

    public function setSalaryPeriod(?string $salaryPeriod): self
    {
        $this->salaryPeriod = RssTransformer::transformForRss($salaryPeriod);

        return $this;
    }

    public function getNotRequiredFields(): array
    {
        return [];
    }

    public function getParamNameElementFlux(): string
    {
        return 'job';
    }
}
