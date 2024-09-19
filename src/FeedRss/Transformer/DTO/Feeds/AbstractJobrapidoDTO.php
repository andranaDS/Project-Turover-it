<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\Company\Entity\Company;
use App\Core\Entity\Location;
use App\Core\Enum\Currency;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\ExperienceLevel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AbstractJobrapidoDTO
{
    public const JOB_TYPE_FULL_TIME = 'fulltime';
    public const JOB_TYPE_INTERNSHIP = 'internship';

    public const SALARY_PERIOD_CONTRACTOR = 'day';
    public const SALARY_PERIOD_WORKER = 'year';

    protected string $title;
    protected string $location;
    protected string $state;
    protected string $country;
    protected ?string $postalCode = null;
    protected string $company;
    protected string $companyWebSite;
    protected string $publishDate;
    protected string $url;
    protected string $description;
    protected string $referenceId;
    protected string $jobType;
    protected ?string $salary = null;
    protected ?string $experience = null;

    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        $this
            ->setTitle($jobPosting->getTitle())
            ->setLocation($jobPosting->getLocation())
            ->setState($jobPosting->getLocation())
            ->setCountry($jobPosting->getLocation()->getCountry())
            ->setPostalCode($jobPosting->getLocation()->getPostalCode())
            ->setCompany($jobPosting->getCompany())
            ->setCompanyWebSite($jobPosting->getCompany())
            ->setPublishDate($jobPosting->getPublishedAt())
            ->setUrl($jobPosting, $router, $feedRss->getGaTag())
            ->setDescription($jobPosting->getDescription())
            ->setReferenceId($jobPosting->getId())
            ->setJobType($jobPosting->getContracts())
        ;
    }

    public function getParamNameElementFlux(): string
    {
        return 'job';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setTitle(?string $title): self
    {
        $this->title = RssTransformer::transformForRss($title);

        return $this;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = RssTransformer::transformForRss($company?->getName());

        return $this;
    }

    public function setPostalCode(?string $postalCode): self
    {
        if (null !== $postalCode) {
            $this->postalCode = RssTransformer::transformForRss($postalCode);
        }

        return $this;
    }

    public function getNotRequiredFields(): array
    {
        return [];
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $locality = $location->getLocality();

        if (null === $locality) {
            $locality = $location->getAdminLevel2();
        }

        $this->location = RssTransformer::transformForRss($locality);

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

    public function getCompanyWebSite(): string
    {
        return $this->companyWebSite;
    }

    public function setCompanyWebSite(?Company $company): self
    {
        $this->companyWebSite = RssTransformer::transformForRss($company?->getWebsiteUrl());

        return $this;
    }

    public function getPublishDate(): string
    {
        return $this->publishDate;
    }

    public function setPublishDate(?\DateTimeInterface $publishDate): self
    {
        $this->publishDate = RssTransformer::transformForRss(null !== $publishDate ? $publishDate->format('d/m/Y') : $publishDate);

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

    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }

    public function setReferenceId(?int $referenceId): self
    {
        $this->referenceId = RssTransformer::transformForRss((string) $referenceId);

        return $this;
    }

    public function getJobType(): string
    {
        return $this->jobType;
    }

    public function setJobType(?array $contracts): self
    {
        if ($contracts === [Contract::INTERNSHIP]) {
            $jobType = self::JOB_TYPE_INTERNSHIP;
        } else {
            $jobType = self::JOB_TYPE_FULL_TIME;
        }

        $this->jobType = RssTransformer::transformForRss($jobType);

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = RssTransformer::transformForRss($description);

        return $this;
    }

    public function setSalary(?int $minSalary, ?int $maxSalary, ?string $currency, string $periodicity): self
    {
        $parts = [$minSalary, $maxSalary];

        $currency = match ($currency) {
            Currency::EUR => '€',
            Currency::USD => '$',
            Currency::GBP => '£',
            default => '€',
        };

        $salary = empty(array_filter($parts))
            ? null :
            implode('-', array_map(static function (int $salary) use ($currency) { return $currency . $salary; }, array_filter($parts)))
        ;

        if (null !== $salary) {
            $this->salary = RssTransformer::transformForRss(sprintf('%s per %s', $salary, $periodicity));
        }

        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(?string $experienceLvl): self
    {
        $experience = match ($experienceLvl) {
            ExperienceLevel::INTERMEDIATE => '+2 years',
            ExperienceLevel::SENIOR => '+5 years',
            ExperienceLevel::EXPERT => '+10 years',
            default => null,
        };

        if (null !== $experience) {
            $this->experience = RssTransformer::transformForRss($experience);
        }

        return $this;
    }
}
