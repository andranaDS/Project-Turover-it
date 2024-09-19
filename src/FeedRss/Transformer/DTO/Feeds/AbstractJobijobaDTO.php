<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\Company\Entity\Company;
use App\Core\Enum\Currency;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Contract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AbstractJobijobaDTO
{
    public const SALARY_PERIODICITY_YEAR = 'year';
    public const SALARY_PERIODICITY_WEEK = 'week';

    public const CONTRACT_TYPE_ROLLING = 'rolling_contract';
    public const CONTRACT_TYPE_FIXED_TERM = 'fixed_term_contract';
    public const CONTRACT_TYPE_INDEPENDENT = 'independent';
    public const CONTRACT_TYPE_INTERNSHIP = 'internship';

    public const CONTRACT_LENGTH_FULL_TIME = 'full_time';

    public const SALARY_CURRENCY_EUR = 'eur';
    public const SALARY_CURRENCY_USD = 'usd';
    public const SALARY_CURRENCY_GBP = 'gbp';

    protected ?string $title = null;
    protected string $link;
    protected string $description;
    protected ?string $region1 = null;
    protected ?string $region2 = null;
    protected ?string $town = null;
    protected ?string $postalCode = null;
    protected string $publicationDate;
    protected string $salaryPeriodicity;
    protected ?string $salaryCurrency = null;
    protected string $salary;
    protected string $company;
    protected string $contractType;
    protected ?string $countryName = null;
    protected string $contractLength;

    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        $this
            ->setTitle($jobPosting->getTitle())
            ->setLink(
                $router->generate('candidates_job_posting', [
                    'JobSlug' => null !== $jobPosting->getJob() ? $jobPosting->getJob()->getSlug() : '',
                    'jobPostingSlug' => $jobPosting->getSlug(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                $feedRss->getGaTag()
            )
            ->setDescription($jobPosting->getDescription())
            ->setRegion1($jobPosting->getLocation()->getAdminLevel1())
            ->setRegion2($jobPosting->getLocation()->getAdminLevel2())
            ->setTown($jobPosting->getLocation()->getLocality())
            ->setPostalCode($jobPosting->getLocation()->getPostalCode())
            ->setPublicationDate($jobPosting->getPublishedAt())
            ->setSalaryCurrency($jobPosting->getCurrency())
            ->setCompany($jobPosting->getCompany())
            ->setContractType($jobPosting->getContracts())
            ->setCountryName($jobPosting->getCompany())
            ->setContractLength(self::CONTRACT_LENGTH_FULL_TIME)
        ;
    }

    public function getParamNameElementFlux(): string
    {
        return 'item';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSalary(): string
    {
        return $this->salary;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getContractType(): string
    {
        return $this->contractType;
    }

    public function getPublicationDate(): string
    {
        return $this->publicationDate;
    }

    public function getRegion1(): ?string
    {
        return $this->region1;
    }

    public function getRegion2(): ?string
    {
        return $this->region2;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function getContractLength(): string
    {
        return $this->contractLength;
    }

    public function setTitle(?string $title): self
    {
        $this->title = RssTransformer::transformTitle($title);

        return $this;
    }

    public function setPublicationDate(?\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = RssTransformer::transformForRss(null !== $publicationDate ? $publicationDate->format('Y-m-d') : $publicationDate);

        return $this;
    }

    public function setLink(?string $link, ?string $GATag): self
    {
        $this->link = RssTransformer::transformForUrl((string) $link, $GATag);

        return $this;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = RssTransformer::transformForRss($company?->getName());

        return $this;
    }

    public function setTown(?string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function setRegion1(?string $region1): self
    {
        $this->region1 = $region1;

        return $this;
    }

    public function setRegion2(?string $region2): self
    {
        $this->region2 = $region2;

        return $this;
    }

    public function setCountryName(?Company $company): self
    {
        $this->countryName = $company?->getLocation()?->getCountry();

        return $this;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = RssTransformer::transformForRss($description);

        return $this;
    }

    public function setSalary(?int $minSalary, ?int $maxSalary): self
    {
        $parts = [$minSalary, $maxSalary];

        $salary = empty(array_filter($parts)) ? null : implode('-', array_filter($parts));

        $this->salary = RssTransformer::transformForRss($salary);

        return $this;
    }

    public function setContractType(?array $contracts): self
    {
        $this->contractType = match ($contracts) {
            [Contract::PERMANENT] => self::CONTRACT_TYPE_ROLLING,
            [Contract::FIXED_TERM] => self::CONTRACT_TYPE_FIXED_TERM,
            [Contract::CONTRACTOR] => self::CONTRACT_TYPE_INDEPENDENT,
            [Contract::INTERNSHIP] => self::CONTRACT_TYPE_INTERNSHIP,
            default => self::CONTRACT_TYPE_ROLLING,
        };

        return $this;
    }

    public function getSalaryPeriodicity(): string
    {
        return $this->salaryPeriodicity;
    }

    public function getSalaryCurrency(): ?string
    {
        return $this->salaryCurrency;
    }

    public function setSalaryCurrency(?string $salaryCurrency): self
    {
        $currency = match ($salaryCurrency) {
            Currency::EUR => self::SALARY_CURRENCY_EUR,
            Currency::USD => self::SALARY_CURRENCY_USD,
            Currency::GBP => self::SALARY_CURRENCY_GBP,
            default => self::SALARY_CURRENCY_EUR,
        };

        $this->salaryCurrency = RssTransformer::transformValueTranslated(sprintf('jobijoba.salary.currency.%s', $currency), false);

        return $this;
    }

    public function setContractLength(string $contractLength): self
    {
        $this->contractLength = RssTransformer::transformForRss($contractLength);

        return $this;
    }

    public function getNotRequiredFields(): array
    {
        return [];
    }

    public function setSalaryPeriodicity(string $salaryPeriodicity): self
    {
        $this->salaryPeriodicity = $salaryPeriodicity;

        return $this;
    }
}
