<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\Core\Entity\Location;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Contract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class JobPostingJoobleWorkerDTO implements JobPostingDTOInterface
{
    public const JOB_TYPE_FULL_TIME = 'full-time';
    public const JOB_TYPE_PART_TIME = 'part-time';
    public const JOB_TYPE_INTERNSHIP = 'internship';

    private ?int $id;
    private ?string $link;
    private ?string $name;
    private ?string $region;
    private ?string $description;
    private ?string $pubDate;
    private ?string $updated;
    private ?string $salary;
    private ?string $company;
    private string $jobType;

    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        $this
            ->setId($jobPosting->getId())
            ->setLink(
                $router->generate(
                    'candidates_job_posting',
                    [
                        'JobSlug' => null !== $jobPosting->getJob() ? $jobPosting->getJob()->getSlug() : '',
                        'jobPostingSlug' => $jobPosting->getSlug(),
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $feedRss->getGaTag()
            )
            ->setName($jobPosting->getTitle())
            ->setRegion($jobPosting->getLocation())
            ->setDescription($jobPosting->getDescription())
            ->setPubDate($jobPosting->getPublishedAt())
            ->setUpdated($jobPosting->getUpdatedAt(), $jobPosting->getPublishedAt())
            ->setSalary($jobPosting->getMinAnnualSalary(), $jobPosting->getMaxAnnualSalary(), $jobPosting->getCurrency())
            ->setCompany($jobPosting->getCompany())
            ->setJobType($jobPosting->getContracts())

        ;
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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link, ?string $GATag): self
    {
        $this->link = RssTransformer::transformForUrl((string) $link, $GATag);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = RssTransformer::transformTitle($name);

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(Location $location): self
    {
        $parts = [
            $location->getLocality(),
            $location->getAdminLevel1(),
            null === $location->getAdminLevel1() ? $location->getAdminLevel2() : null,
            null === $location->getAdminLevel1() && null === $location->getAdminLevel2() ? $location->getCountry() : null,
        ];

        $region = empty(array_filter($parts)) ? null : implode(', ', array_filter($parts));

        $this->region = RssTransformer::transformForRss($region);

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

    public function getPubDate(): ?string
    {
        return $this->pubDate;
    }

    public function setPubDate(?\DateTimeInterface $pubDate): self
    {
        $this->pubDate = $pubDate?->format('d.m.Y');

        return $this;
    }

    public function getUpdated(): ?string
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated, ?\DateTimeInterface $pubDate): self
    {
        $this->updated = $updated < $pubDate ? $pubDate?->format('d.m.Y') : $updated->format('d.m.Y');

        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(?int $minSalary, ?int $maxSalary, ?string $currency): self
    {
        $currency = RssTransformer::transformCurrency($currency);

        if (null !== $minSalary && null !== $maxSalary) {
            $this->salary = RssTransformer::transformForRss(sprintf('%d - %d %s / year', $minSalary, $maxSalary, $currency));
        } elseif (null !== $minSalary && null === $maxSalary) {
            $this->salary = RssTransformer::transformForRss(sprintf('%d %s / year', $minSalary, $currency));
        } elseif (null === $minSalary && null !== $maxSalary) {
            $this->salary = RssTransformer::transformForRss(sprintf('%d %s / year', $maxSalary, $currency));
        } else {
            $this->salary = RssTransformer::transformForRss('0');
        }

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = RssTransformer::transformForRss($company);

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

    public function getNotRequiredFields(): array
    {
        return [];
    }

    public function getParamNameElementFlux(): string
    {
        return 'job';
    }
}
