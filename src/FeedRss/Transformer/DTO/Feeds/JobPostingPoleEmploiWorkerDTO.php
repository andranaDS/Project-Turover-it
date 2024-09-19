<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\Company\Entity\Company;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class JobPostingPoleEmploiWorkerDTO implements JobPostingDTOInterface
{
    public function __construct(JobPosting $jobPosting, FeedRss $FeedRss, RouterInterface $router)
    {
        $this->setReference($jobPosting->getId());
        $this->setUrl(
            $router->generate(
                'candidates_job_posting',
                [
                    'JobSlug' => null !== $jobPosting->getJob() ? $jobPosting->getJob()->getSlug() : '',
                    'jobPostingSlug' => $jobPosting->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL),
            $FeedRss->getGaTag()
        );
        $this->setRomeCode(null !== $jobPosting->getJob() ? $jobPosting->getJob()->getRomeCode() : '');
        $this->setOgrCode(null !== $jobPosting->getJob() ? $jobPosting->getJob()->getOgrCode() : '');
        $this->setOgrLabel(null !== $jobPosting->getJob() ? $jobPosting->getJob()->getOgrLabel() : '');
        $this->setDescription($jobPosting->getDescription());
        $this->setExperience($jobPosting->getExperienceLevel());
        $this->setTsaCle('TSACLE');
        $this->setTsaLibelle('TSALIBELLE');
        $this->_setSalaries($jobPosting);
        $this->setUmoCLE($jobPosting->getCurrency());
        $this->setUmoLibelle($jobPosting->getCurrency());
        $this->setTypeContrat($jobPosting->getContracts());
        $this->setDuree($jobPosting->getDuration());
        $this->setCommuneCLe($jobPosting->getLocation()->getPostalCode());
        $this->setCommuneLibelle($jobPosting->getLocation()->getLocality());
        $this->setDepartementCle($jobPosting->getLocation()->getAdminLevel1());
        $this->setDateCreation($jobPosting->getPublishedAt());
        $this->setCompany($jobPosting->getCompany());
    }

    private string $reference;

    private string $url;

    private string $romeCode;

    private string $ogrCode;

    private string $ogrLabel;

    private string $description;

    private string $experience;

    private string $tsaCle;

    private string $tsaLibelle;

    private string $salaireMin;

    private string $salaireMax;

    private string $umoCLE;

    private string $umoLibelle;

    private string $typeContrat;

    private string $duree;

    private string $communeCLe;

    private string $communeLibelle;

    private string $departementCle;

    private string $dateCreation;

    private string $company;

    public function getNotRequiredFields(): array
    {
        return [];
    }

    public function getParamNameElementFlux(): string
    {
        return 'job';
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRomeCode(): string
    {
        return $this->romeCode;
    }

    public function getOgrCode(): string
    {
        return $this->ogrCode;
    }

    public function getOgrLabel(): string
    {
        return $this->ogrLabel;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getExperience(): string
    {
        return $this->experience;
    }

    public function getTsaCle(): string
    {
        return $this->tsaCle;
    }

    public function getTsaLibelle(): string
    {
        return $this->tsaLibelle;
    }

    public function getSalaireMin(): string
    {
        return $this->salaireMin;
    }

    public function getSalaireMax(): string
    {
        return $this->salaireMax;
    }

    public function getUmoCLE(): string
    {
        return $this->umoCLE;
    }

    public function getUmoLibelle(): string
    {
        return $this->umoLibelle;
    }

    public function getTypeContrat(): string
    {
        return $this->typeContrat;
    }

    public function getDuree(): string
    {
        return $this->duree;
    }

    public function getCommuneCLe(): string
    {
        return $this->communeCLe;
    }

    public function getCommuneLibelle(): string
    {
        return $this->communeLibelle;
    }

    public function getDepartementCle(): string
    {
        return $this->departementCle;
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setReference(?int $reference): void
    {
        $this->reference = RssTransformer::transformForRss((string) $reference);
    }

    public function setUrl(?string $url, ?string $GATag): void
    {
        $this->url = RssTransformer::transformForUrl((string) $url, $GATag);
    }

    public function setRomeCode(?string $romeCode): void
    {
        $this->romeCode = RssTransformer::transformForRss($romeCode);
    }

    public function setOgrCode(?string $ogrCode): void
    {
        $this->ogrCode = RssTransformer::transformForRss($ogrCode);
    }

    public function setOgrLabel(?string $ogrLabel): void
    {
        $this->ogrLabel = RssTransformer::transformForRss($ogrLabel);
    }

    public function setDescription(?string $description): void
    {
        $description = preg_replace(
            ['/(-{3,})/', '/(\*{3,})/', '/(_{3,})/'],
            ['---', '***', '___'],
            (string) $description
        );
        $this->description = RssTransformer::transformForRss($description);
    }

    public function setExperience(?string $experience): void
    {
        $this->experience = RssTransformer::transformForRss($experience);
    }

    public function setTsaCle(?string $tsaCle): void
    {
        $this->tsaCle = RssTransformer::transformForRss($tsaCle);
    }

    public function setTsaLibelle(?string $tsaLibelle): void
    {
        $this->tsaLibelle = RssTransformer::transformForRss($tsaLibelle);
    }

    public function setSalaireMin(?int $salaireMin): void
    {
        $this->salaireMin = RssTransformer::transformForRss((string) $salaireMin);
    }

    public function setSalaireMax(?int $salaireMax): void
    {
        $this->salaireMax = RssTransformer::transformForRss((string) $salaireMax);
    }

    public function setUmoCLE(?string $umoCLE): void
    {
        $this->umoCLE = RssTransformer::transformForRss($umoCLE);
    }

    public function setUmoLibelle(?string $umoLibelle): void
    {
        $this->umoLibelle = RssTransformer::transformForRss($umoLibelle);
    }

    public function setTypeContrat(?array $typeContrat): void
    {
        $this->typeContrat = RssTransformer::transformContract($typeContrat);
    }

    public function setDuree(?int $duree): void
    {
        $this->duree = RssTransformer::transformForRss((string) $duree);
    }

    public function setCommuneCLe(?string $communeCLe): void
    {
        $this->communeCLe = RssTransformer::transformForRss($communeCLe);
    }

    public function setCommuneLibelle(?string $communeLibelle): void
    {
        $this->communeLibelle = RssTransformer::transformForRss($communeLibelle);
    }

    public function setDepartementCle(?string $departementCle): void
    {
        $this->departementCle = RssTransformer::transformForRss($departementCle);
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): void
    {
        $this->dateCreation = RssTransformer::transformForRss(null !== $dateCreation ? $dateCreation->format('r') : $dateCreation);
    }

    public function setCompany(?Company $company): void
    {
        $this->company = RssTransformer::transformForRss($company?->getName());
    }

    private function _setSalaries(JobPosting $jobPosting): void
    {
        if (null !== $jobPosting->getMinAnnualSalary() && null !== $jobPosting->getMaxAnnualSalary()) {
            $this->setSalaireMin($jobPosting->getMinAnnualSalary());
            $this->setSalaireMax($jobPosting->getMaxAnnualSalary());
        } elseif (null !== $jobPosting->getMinDailySalary() && null !== $jobPosting->getMaxDailySalary()) {
            $this->setSalaireMin($jobPosting->getMinDailySalary());
            $this->setSalaireMax($jobPosting->getMaxDailySalary());
        } else {
            $this->setSalaireMin(null);
            $this->setSalaireMax(null);
        }
    }
}
