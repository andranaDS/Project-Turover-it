<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\Company\Entity\Company;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingMeteojobRhonesAlpesDTO implements JobPostingDTOInterface
{
    /**
     * WARNING : The order of the GETTERS determine the order of the Fields in each item of the FLUX
     * Salary must only appear in feed if salaryFrom and salaryTo are NULL.
     */
    public function __construct(JobPosting $jobPosting, FeedRss $FeedRss, RouterInterface $router)
    {
        $this->setReference($jobPosting->getId());
        $this->setTitle($jobPosting->getTitle());
        $this->setJobName($jobPosting->getTitle());
        $this->setContractTypes($jobPosting->getContracts());
        $this->setCompanyName($jobPosting->getCompany());
        $this->setJobDescription($jobPosting->getDescription());
        $this->setProfileDescription($jobPosting->getDescription());
        $this->setCompanyDescription($jobPosting->getCompany());

        $this->_setSalaries($jobPosting);

        $this->setSalaryCurrency($jobPosting->getCurrency());
        $this->setPostalCode($jobPosting->getLocation()->getPostalCode());
        $this->setCity($jobPosting->getLocation()->getLocality());
        $this->setApplicationURL(
            $router->generate('candidates_job_posting', [
                'JobSlug' => null !== $jobPosting->getJob() ? $jobPosting->getJob()->getSlug() : '',
                'jobPostingSlug' => $jobPosting->getSlug(),
            ]),
            $FeedRss->getGaTag()
        );
    }

    public function getNotRequiredFields(): array
    {
        return ['companyDescription', 'salary', 'salaryFrom', 'salaryTo'];
    }

    public function getParamNameElementFlux(): string
    {
        return 'offer';
    }

    private string $reference;

    private ?string $title;

    private string $jobName;

    private string $contractTypes;

    private string $companyName;

    private string $jobDescription;

    private string $profileDescription;

    private string $companyDescription;

    private string $salary;

    private string $salaryFrom;

    private string $salaryTo;

    private string $salaryCurrency;

    private string $postalcode;

    private string $city;

    private string $applicationURL;

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getJobName(): string
    {
        return $this->jobName;
    }

    public function getContractTypes(): string
    {
        return $this->contractTypes;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getJobDescription(): string
    {
        return $this->jobDescription;
    }

    public function getProfileDescription(): string
    {
        return $this->profileDescription;
    }

    public function getCompanyDescription(): string
    {
        return $this->companyDescription;
    }

    public function getSalaryFrom(): string
    {
        return $this->salaryFrom;
    }

    public function getSalaryTo(): string
    {
        return $this->salaryTo;
    }

    public function getSalary(): string
    {
        return $this->salary;
    }

    public function getSalaryCurrency(): string
    {
        return $this->salaryCurrency;
    }

    public function getPostalcode(): string
    {
        return $this->postalcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getApplicationURL(): string
    {
        return $this->applicationURL;
    }

    public function setTitle(?string $title): void
    {
        $this->title = RssTransformer::transformTitle($title);
    }

    public function setJobName(?string $jobName): void
    {
        $this->jobName = RssTransformer::transformForRss($jobName);
    }

    public function setReference(?int $reference): void
    {
        $this->reference = RssTransformer::transformForRss((string) $reference);
    }

    public function setCompanyDescription(?string $companyDescription): void
    {
        $this->companyDescription = RssTransformer::transformForRss($companyDescription);
    }

    public function setProfileDescription(?string $profileDescription): void
    {
        $this->profileDescription = RssTransformer::transformForRss($profileDescription);
    }

    public function setApplicationURL(?string $applicationURL, ?string $GATag): void
    {
        $this->applicationURL = RssTransformer::transformForUrl((string) $applicationURL, $GATag);
    }

    public function setSalaryFrom(?int $minSalaryFrom): void
    {
        $this->salaryFrom = RssTransformer::transformForRss((string) $minSalaryFrom);
    }

    public function setSalaryTo(?int $maxSalaryTo): void
    {
        $this->salaryTo = RssTransformer::transformForRss((string) $maxSalaryTo);
    }

    public function setSalaryCurrency(?string $salaryCurrency): void
    {
        $this->salaryCurrency = RssTransformer::transformForRss($salaryCurrency);
    }

    public function setSalary(?string $salary): void
    {
        $this->salary = RssTransformer::transformForRss($salary);
    }

    public function setCompanyName(?Company $company): void
    {
        $this->companyName = RssTransformer::transformForRss($company?->getName());
    }

    public function setCity(?string $city): void
    {
        $this->city = RssTransformer::transformForRss($city);
    }

    public function setPostalcode(?string $postalcode): void
    {
        $this->postalcode = RssTransformer::transformForRss($postalcode);
    }

    public function setJobDescription(?string $jobDescription): void
    {
        $this->jobDescription = RssTransformer::transformForRss($jobDescription);
    }

    public function setContractTypes(?array $contractTypes): void
    {
        $this->contractTypes = RssTransformer::transformContract($contractTypes);
    }

    private function _setSalaries(JobPosting $jobPosting): void
    {
        if (null !== $jobPosting->getMinAnnualSalary() && null !== $jobPosting->getMaxAnnualSalary()) {
            $this->setSalaryFrom($jobPosting->getMinAnnualSalary());
            $this->setSalaryTo($jobPosting->getMaxAnnualSalary());
            $this->setSalary('');
        } elseif (null !== $jobPosting->getMinDailySalary() && null !== $jobPosting->getMaxDailySalary()) {
            $this->setSalaryFrom($jobPosting->getMinDailySalary());
            $this->setSalaryTo($jobPosting->getMaxDailySalary());
            $this->setSalary('');
        } else {
            $this->setSalaryFrom(null);
            $this->setSalaryTo(null);
            $this->setSalary('0');
        }
    }
}
