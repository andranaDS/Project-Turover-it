<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\Company\Entity\Company;
use App\Core\Entity\Job;
use App\Core\Entity\Location;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\ExperienceLevel;
use App\JobPosting\Enum\RemoteMode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AbstractIndeedDTO
{
    public const SALARY_PERIOD_DAILY = 'daily';
    public const SALARY_PERIOD_YEARLY = 'yearly';

    public const JOB_TYPE_FULL_TIME = 'fulltime';
    public const JOB_TYPE_PART_TIME = 'parttime';

    protected ?string $title = null;
    protected ?string $date = null;
    protected ?string $referenceNumber = null;
    protected string $url;
    protected ?string $company = null;
    protected ?string $sourceName = null;
    protected ?string $city = null;
    protected ?string $state = null;
    protected ?string $country = null;
    protected ?string $postalCode = null;
    protected ?string $description = null;
    protected ?string $salary = null;
    protected ?string $jobType = null;
    protected ?string $category = null;
    protected ?string $experience = null;
    protected ?string $expirationDate = null;
    protected ?string $remoteType = null;

    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        $this
            ->setTitle($jobPosting->getTitle())
            ->setDate($jobPosting->getPublishedAt())
            ->setReferenceNumber($jobPosting->getId())
            ->setUrl($jobPosting, $router, $feedRss->getGaTag())
            ->setCompany($jobPosting->getCompany())
            ->setSourceName($jobPosting->getCompany())
            ->setCity($jobPosting->getLocation())
            ->setState($jobPosting->getLocation()->getAdminLevel1())
            ->setCountry($jobPosting->getLocation()->getCountryCode())
            ->setPostalCode($jobPosting->getLocation()->getPostalCode())
            ->setDescription($jobPosting->getDescription())
            ->setJobType(self::JOB_TYPE_FULL_TIME)
            ->setCategory($jobPosting->getJob())
            ->setExperience($jobPosting->getExperienceLevel())
            ->setRemoteType($jobPosting->getRemoteMode())

        ;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): AbstractIndeedDTO
    {
        $this->title = RssTransformer::transformForRss($title);

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = RssTransformer::transformForRss(null !== $date ? $date->format('r') : $date);

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

    public function setState(?string $state): self
    {
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

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function getJobType(): ?string
    {
        return $this->jobType;
    }

    public function setJobType(?string $jobType): self
    {
        $this->jobType = $jobType;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?Job $job): self
    {
        if (null !== $job && null !== $job->getCategory()) {
            $this->category = RssTransformer::transformForRss($job->getCategory()->getName());
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

    public function getExpirationDate(): ?string
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?string $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getRemoteType(): ?string
    {
        return $this->remoteType;
    }

    public function setRemoteType(?string $remoteType): self
    {
        if (RemoteMode::FULL === $remoteType) {
            $this->remoteType = RssTransformer::transformForRss('Fully remote');
        }

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

    public function getSourceName(): ?string
    {
        return $this->sourceName;
    }

    public function setSourceName(?Company $company): self
    {
        $this->sourceName = RssTransformer::transformForRss($company?->getName());

        return $this;
    }
}
