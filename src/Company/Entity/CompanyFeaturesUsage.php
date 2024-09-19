<?php

namespace App\Company\Entity;

use App\Company\Repository\CompanyFeaturesUsageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompanyFeaturesUsageRepository::class)
 */
class CompanyFeaturesUsage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\OneToOne(targetEntity=Company::class, mappedBy="featuresUsage")
     */
    private ?Company $company = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $searchDisplayArrayAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $searchDisplayListAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $searchBooleanAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $searchQueryAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $searchJobAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $searchLocationAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $searchFolderAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $searchOrderAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $searchAvailabilityAndLanguageAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userCartAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userFavoriteAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userHideAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userDownloadResumeAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userCommentAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userFolderAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userJobPostingAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userEmailTransferAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userEmailSendAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userMultipleFolderAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userMultipleExportAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $userAlertAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $jobPostingFreeWorkAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $jobPostingTurnoverAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $jobPostingPublicAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $jobPostingInternalAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $intercontractSearchByCompanyAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $intercontractPublishAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $intercontractOnlyAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $companyPublishAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $companyLogAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $exportJobPostingPublishAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $exportUserLogAndDownloadAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSearchDisplayArrayAt(): ?\DateTimeInterface
    {
        return $this->searchDisplayArrayAt;
    }

    public function setSearchDisplayArrayAt(?\DateTimeInterface $searchDisplayArrayAt): self
    {
        $this->searchDisplayArrayAt = $searchDisplayArrayAt;

        return $this;
    }

    public function getSearchDisplayListAt(): ?\DateTimeInterface
    {
        return $this->searchDisplayListAt;
    }

    public function setSearchDisplayListAt(?\DateTimeInterface $searchDisplayListAt): self
    {
        $this->searchDisplayListAt = $searchDisplayListAt;

        return $this;
    }

    public function getSearchBooleanAt(): ?\DateTimeInterface
    {
        return $this->searchBooleanAt;
    }

    public function setSearchBooleanAt(?\DateTimeInterface $searchBooleanAt): self
    {
        $this->searchBooleanAt = $searchBooleanAt;

        return $this;
    }

    public function getSearchQueryAt(): ?\DateTimeInterface
    {
        return $this->searchQueryAt;
    }

    public function setSearchQueryAt(?\DateTimeInterface $searchQueryAt): self
    {
        $this->searchQueryAt = $searchQueryAt;

        return $this;
    }

    public function getSearchJobAt(): ?\DateTimeInterface
    {
        return $this->searchJobAt;
    }

    public function setSearchJobAt(?\DateTimeInterface $searchJobAt): self
    {
        $this->searchJobAt = $searchJobAt;

        return $this;
    }

    public function getSearchLocationAt(): ?\DateTimeInterface
    {
        return $this->searchLocationAt;
    }

    public function setSearchLocationAt(?\DateTimeInterface $searchLocationAt): self
    {
        $this->searchLocationAt = $searchLocationAt;

        return $this;
    }

    public function getSearchFolderAt(): ?\DateTimeInterface
    {
        return $this->searchFolderAt;
    }

    public function setSearchFolderAt(?\DateTimeInterface $searchFolderAt): self
    {
        $this->searchFolderAt = $searchFolderAt;

        return $this;
    }

    public function getSearchOrderAt(): ?\DateTimeInterface
    {
        return $this->searchOrderAt;
    }

    public function setSearchOrderAt(?\DateTimeInterface $searchOrderAt): self
    {
        $this->searchOrderAt = $searchOrderAt;

        return $this;
    }

    public function getSearchAvailabilityAndLanguageAt(): ?\DateTimeInterface
    {
        return $this->searchAvailabilityAndLanguageAt;
    }

    public function setSearchAvailabilityAndLanguageAt(?\DateTimeInterface $searchAvailabilityAndLanguageAt): self
    {
        $this->searchAvailabilityAndLanguageAt = $searchAvailabilityAndLanguageAt;

        return $this;
    }

    public function getUserCartAt(): ?\DateTimeInterface
    {
        return $this->userCartAt;
    }

    public function setUserCartAt(?\DateTimeInterface $userCartAt): self
    {
        $this->userCartAt = $userCartAt;

        return $this;
    }

    public function getUserFavoriteAt(): ?\DateTimeInterface
    {
        return $this->userFavoriteAt;
    }

    public function setUserFavoriteAt(?\DateTimeInterface $userFavoriteAt): self
    {
        $this->userFavoriteAt = $userFavoriteAt;

        return $this;
    }

    public function getUserHideAt(): ?\DateTimeInterface
    {
        return $this->userHideAt;
    }

    public function setUserHideAt(?\DateTimeInterface $userHideAt): self
    {
        $this->userHideAt = $userHideAt;

        return $this;
    }

    public function getUserDownloadResumeAt(): ?\DateTimeInterface
    {
        return $this->userDownloadResumeAt;
    }

    public function setUserDownloadResumeAt(?\DateTimeInterface $userDownloadResumeAt): self
    {
        $this->userDownloadResumeAt = $userDownloadResumeAt;

        return $this;
    }

    public function getUserCommentAt(): ?\DateTimeInterface
    {
        return $this->userCommentAt;
    }

    public function setUserCommentAt(?\DateTimeInterface $userCommentAt): self
    {
        $this->userCommentAt = $userCommentAt;

        return $this;
    }

    public function getUserFolderAt(): ?\DateTimeInterface
    {
        return $this->userFolderAt;
    }

    public function setUserFolderAt(?\DateTimeInterface $userFolderAt): self
    {
        $this->userFolderAt = $userFolderAt;

        return $this;
    }

    public function getUserJobPostingAt(): ?\DateTimeInterface
    {
        return $this->userJobPostingAt;
    }

    public function setUserJobPostingAt(?\DateTimeInterface $userJobPostingAt): self
    {
        $this->userJobPostingAt = $userJobPostingAt;

        return $this;
    }

    public function getUserEmailTransferAt(): ?\DateTimeInterface
    {
        return $this->userEmailTransferAt;
    }

    public function setUserEmailTransferAt(?\DateTimeInterface $userEmailTransferAt): self
    {
        $this->userEmailTransferAt = $userEmailTransferAt;

        return $this;
    }

    public function getUserEmailSendAt(): ?\DateTimeInterface
    {
        return $this->userEmailSendAt;
    }

    public function setUserEmailSendAt(?\DateTimeInterface $userEmailSendAt): self
    {
        $this->userEmailSendAt = $userEmailSendAt;

        return $this;
    }

    public function getUserMultipleFolderAt(): ?\DateTimeInterface
    {
        return $this->userMultipleFolderAt;
    }

    public function setUserMultipleFolderAt(?\DateTimeInterface $userMultipleFolderAt): self
    {
        $this->userMultipleFolderAt = $userMultipleFolderAt;

        return $this;
    }

    public function getUserMultipleExportAt(): ?\DateTimeInterface
    {
        return $this->userMultipleExportAt;
    }

    public function setUserMultipleExportAt(?\DateTimeInterface $userMultipleExportAt): self
    {
        $this->userMultipleExportAt = $userMultipleExportAt;

        return $this;
    }

    public function getUserAlertAt(): ?\DateTimeInterface
    {
        return $this->userAlertAt;
    }

    public function setUserAlertAt(?\DateTimeInterface $userAlertAt): self
    {
        $this->userAlertAt = $userAlertAt;

        return $this;
    }

    public function getJobPostingFreeWorkAt(): ?\DateTimeInterface
    {
        return $this->jobPostingFreeWorkAt;
    }

    public function setJobPostingFreeWorkAt(?\DateTimeInterface $jobPostingFreeWorkAt): self
    {
        $this->jobPostingFreeWorkAt = $jobPostingFreeWorkAt;

        return $this;
    }

    public function getJobPostingTurnoverAt(): ?\DateTimeInterface
    {
        return $this->jobPostingTurnoverAt;
    }

    public function setJobPostingTurnoverAt(?\DateTimeInterface $jobPostingTurnoverAt): self
    {
        $this->jobPostingTurnoverAt = $jobPostingTurnoverAt;

        return $this;
    }

    public function getJobPostingPublicAt(): ?\DateTimeInterface
    {
        return $this->jobPostingPublicAt;
    }

    public function setJobPostingPublicAt(?\DateTimeInterface $jobPostingPublicAt): self
    {
        $this->jobPostingPublicAt = $jobPostingPublicAt;

        return $this;
    }

    public function getJobPostingInternalAt(): ?\DateTimeInterface
    {
        return $this->jobPostingInternalAt;
    }

    public function setJobPostingInternalAt(?\DateTimeInterface $jobPostingInternalAt): self
    {
        $this->jobPostingInternalAt = $jobPostingInternalAt;

        return $this;
    }

    public function getIntercontractSearchByCompanyAt(): ?\DateTimeInterface
    {
        return $this->intercontractSearchByCompanyAt;
    }

    public function setIntercontractSearchByCompanyAt(?\DateTimeInterface $intercontractSearchByCompanyAt): self
    {
        $this->intercontractSearchByCompanyAt = $intercontractSearchByCompanyAt;

        return $this;
    }

    public function getIntercontractPublishAt(): ?\DateTimeInterface
    {
        return $this->intercontractPublishAt;
    }

    public function setIntercontractPublishAt(?\DateTimeInterface $intercontractPublishAt): self
    {
        $this->intercontractPublishAt = $intercontractPublishAt;

        return $this;
    }

    public function getIntercontractOnlyAt(): ?\DateTimeInterface
    {
        return $this->intercontractOnlyAt;
    }

    public function setIntercontractOnlyAt(?\DateTimeInterface $intercontractOnlyAt): self
    {
        $this->intercontractOnlyAt = $intercontractOnlyAt;

        return $this;
    }

    public function getCompanyPublishAt(): ?\DateTimeInterface
    {
        return $this->companyPublishAt;
    }

    public function setCompanyPublishAt(?\DateTimeInterface $companyPublishAt): self
    {
        $this->companyPublishAt = $companyPublishAt;

        return $this;
    }

    public function getCompanyLogAt(): ?\DateTimeInterface
    {
        return $this->companyLogAt;
    }

    public function setCompanyLogAt(?\DateTimeInterface $companyLogAt): self
    {
        $this->companyLogAt = $companyLogAt;

        return $this;
    }

    public function getExportJobPostingPublishAt(): ?\DateTimeInterface
    {
        return $this->exportJobPostingPublishAt;
    }

    public function setExportJobPostingPublishAt(?\DateTimeInterface $exportJobPostingPublishAt): self
    {
        $this->exportJobPostingPublishAt = $exportJobPostingPublishAt;

        return $this;
    }

    public function getExportUserLogAndDownloadAt(): ?\DateTimeInterface
    {
        return $this->exportUserLogAndDownloadAt;
    }

    public function setExportUserLogAndDownloadAt(?\DateTimeInterface $exportUserLogAndDownloadAt): self
    {
        $this->exportUserLogAndDownloadAt = $exportUserLogAndDownloadAt;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        // unset the owning side of the relation if necessary
        if (null === $company && null !== $this->company) {
            $this->company->setFeaturesUsage(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $company && $company->getFeaturesUsage() !== $this) {
            $company->setFeaturesUsage($this);
        }

        $this->company = $company;

        return $this;
    }
}
