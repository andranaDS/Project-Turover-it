<?php

namespace App\Recruiter\Manager;

use App\Recruiter\Entity\Recruiter;
use Carbon\Carbon;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Vich\UploaderBundle\Handler\UploadHandler;

class RecruiterManager
{
    private UserPasswordHasherInterface $hasher;
    private UploadHandler $uploadHandler;

    public function __construct(UserPasswordHasherInterface $hasher, UploadHandler $uploadHandler)
    {
        $this->hasher = $hasher;
        $this->uploadHandler = $uploadHandler;
    }

    public function delete(Recruiter $recruiter): void
    {
        // 1. remove company data
        // TODO check invoices - company with invoice(s) can't be deleted

        if (true === $recruiter->isMain()) {
            $company = $recruiter->getCompany();

            if (null !== $company) {
                $company->setName(null)
                    ->setExcerpt(null)
                    ->setDescription(null)
                    ->setAnnualRevenue(null)
                    ->setBusinessActivity(null)
                    ->setSize(null)
                    ->setWebsiteUrl(null)
                    ->setLinkedInUrl(null)
                    ->setFacebookUrl(null)
                    ->setTwitterUrl(null)
                    ->setLocation(null)
                    ->setLogo(null)
                    ->setLogoFile(null)
                    ->setCoverPicture(null)
                    ->setCoverPictureFile(null)
                    ->setCreationYear(null)
                    ->setDirectoryFreeWork(false)
                    ->setBillingAddress(null)
                    ->setRegistrationNumber(null)
                    ->setData(null)
                    ->setDeletedAt(Carbon::now())
                ;

                $this->uploadHandler->remove($company, 'logoFile');
                $this->uploadHandler->remove($company, 'coverPictureFile');

                foreach ($company->getPictures() as $picture) {
                    $company->removePicture($picture);
                    $this->uploadHandler->remove($picture, 'imageFile');
                }

                foreach ($company->getUserFavorites() as $companyFavorite) {
                    $company->removeUserFavorite($companyFavorite);
                }

                foreach ($company->getBlacklists() as $companyBlackList) {
                    $company->removeBlacklist($companyBlackList);
                }

                foreach ($company->getJobPostings() as $jobPosting) {
                    // todo: cf SuggestedTest
                }

                foreach ($company->getSkills() as $skill) {
                    $company->removeSkill($skill);
                }
            }
        }

        // 2. remove recruiter data
        $recruiter
            ->setEmail(null)
            ->setUsername(null)
            ->setGender(null)
            ->setFirstName(null)
            ->setLastName(null)
            ->setPhoneNumber(null)
            ->setEnabled(false)
            ->setPassword(null)
            ->setJob(null)
            ->setTermsOfService(false)
            ->setTermsOfServiceAcceptedAt(null)
            ->setWebinarViewedAt(null)
            ->setDeletedAt(Carbon::now())
        ;
    }

    public function setPassword(Recruiter $recruiter, string $plainPassword): void
    {
        $hashedPassword = $this->hasher->hashPassword($recruiter, $plainPassword);
        $recruiter->setPassword($hashedPassword)
            ->setPasswordUpdatedAt(Carbon::now())
            ->eraseCredentials()
        ;
    }
}
