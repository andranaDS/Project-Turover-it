<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\Company\Entity\Company;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class JobPostingMeteoJobWorkerDTO implements JobPostingDTOInterface
{
    /**
     * WARNING : The order of the GETTERS determine the order of the Fields in each item of the FLUX.
     */
    public function __construct(JobPosting $jobPosting, FeedRss $FeedRss, RouterInterface $router)
    {
        $this->setTitle($jobPosting->getTitle());
        $this->setDate($jobPosting->getPublishedAt());
        $this->setReferencenumber($jobPosting->getId());
        $this->setUrl(
            $router->generate(
                'candidates_job_posting',
                [
                    'JobSlug' => null !== $jobPosting->getJob() ? $jobPosting->getJob()->getSlug() : '',
                    'jobPostingSlug' => $jobPosting->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $FeedRss->getGaTag()
        );
        $this->setCompany($jobPosting->getCompany());
        $this->setState($jobPosting->getLocation()->getAdminLevel1());
        $this->setCountry($jobPosting->getLocation()->getCountryCode());
        $this->setCity($jobPosting->getLocation()->getLocality());
        $this->setPostalCode($jobPosting->getLocation()->getPostalCode());
        $this->setSalary($jobPosting->getAnnualSalary(), $jobPosting->getDailySalary());
        $this->setContractTypes($jobPosting->getContracts());
        $this->setDescription($jobPosting->getDescription());
        $this->setJobtype('fulltime');
        $this->setCategory('Informatique / Télécom');
        $this->setExperience($jobPosting->getExperienceLevel());
        $this->setTag($jobPosting->getTitle());
    }

    private ?string $title;

    private string $date;

    private string $referencenumber;

    private string $url;

    private string $company;

    private string $city;

    private string $state;

    private string $country;

    private string $postalcode;

    private string $description;

    private string $salary;

    private string $category;

    private string $contractTypes;

    private string $jobtype;

    private array $tag;

    private string $experience;

    public function getNotRequiredFields(): array
    {
        return [];
    }

    public function getParamNameElementFlux(): string
    {
        return 'job';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getReferencenumber(): string
    {
        return $this->referencenumber;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getPostalcode(): string
    {
        return $this->postalcode;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTag(): array
    {
        return $this->tag;
    }

    public function getSalary(): string
    {
        return $this->salary;
    }

    public function getContractTypes(): string
    {
        return $this->contractTypes;
    }

    public function getJobtype(): string
    {
        return $this->jobtype;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getExperience(): string
    {
        return $this->experience;
    }

    public function setTitle(?string $title): void
    {
        $this->title = RssTransformer::transformTitle($title);
    }

    public function setReferencenumber(?int $referencenumber): void
    {
        $this->referencenumber = RssTransformer::transformForRss((string) $referencenumber);
    }

    public function setDate(?\DateTimeInterface $date): void
    {
        $this->date = RssTransformer::transformForRss(null !== $date ? $date->format('r') : $date);
    }

    public function setUrl(?string $url, ?string $GATag): void
    {
        $this->url = RssTransformer::transformForUrl((string) $url, $GATag);
    }

    public function setCompany(?Company $company): void
    {
        $this->company = RssTransformer::transformForRss($company?->getName());
    }

    public function setCity(?string $city): void
    {
        $this->city = RssTransformer::transformForRss($city);
    }

    public function setState(?string $state): void
    {
        $this->state = RssTransformer::transformForRss($state);
    }

    public function setCountry(?string $country): void
    {
        $this->country = RssTransformer::transformForRss($country);
    }

    public function setPostalcode(?string $postalcode): void
    {
        $this->postalcode = RssTransformer::transformForRss($postalcode);
    }

    public function setDescription(?string $description): void
    {
        $this->description = RssTransformer::transformForRss($description);
    }

    public function setSalary(?string $annualSalary, ?string $dailySalary): void
    {
        $this->salary = RssTransformer::transformSalary($annualSalary, $dailySalary);
    }

    public function setCategory(?string $category): void
    {
        $this->category = RssTransformer::transformForRss($category);
    }

    public function setContractTypes(?array $contractTypes): void
    {
        $this->contractTypes = RssTransformer::transformContract($contractTypes);
    }

    public function setJobtype(?string $jobtype): void
    {
        $this->jobtype = RssTransformer::transformForRss($jobtype);
    }

    public function setTag(?string $title): void
    {
        if (null !== $title && (str_contains($title, 'developpeur') || str_contains($title, 'symphony') || str_contains($title, 'java'))) {
            $tag = 'TAGAPPLYEXTPREM';
        } else { // offres communes
            $tag = null === $title ? 'TAGAPPLYCIOTHER' : 'TAGAPPLYEXTOTHER';
        }
        $this->tag = ['tagName' => 'category', 'tagValue' => RssTransformer::transformForRss($tag)];
    }

    public function setExperience(?string $experience): void
    {
        $this->experience = RssTransformer::transformForRss($experience);
    }
}
