<?php

namespace App\User\Manager;

use App\User\Entity\User;
use App\User\Enum\Availability;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\UploadHandler;

class UserManager
{
    private EntityManagerInterface $em;
    private UploadHandler $uploadHandler;
    private string $jwtHpName;
    private string $jwtSName;
    private string $refreshTokenName;

    public function __construct(EntityManagerInterface $em, UploadHandler $uploadHandler, string $jwtHpName, string $jwtSName, string $refreshTokenName)
    {
        $this->em = $em;
        $this->uploadHandler = $uploadHandler;
        $this->jwtHpName = $jwtHpName;
        $this->jwtSName = $jwtSName;
        $this->refreshTokenName = $refreshTokenName;
    }

    public function logout(Response $response): Response
    {
        $cookiesToDelete = [
            $this->jwtHpName,
            $this->jwtSName,
            $this->refreshTokenName,
        ];

        foreach ($cookiesToDelete as $cookieToDelete) {
            $response->headers->clearCookie($cookieToDelete);
        }

        return $response;
    }

    public static function calculateNextAvailabilityAt(string $availability = null, \DateTime $date = null): ?\DateTime
    {
        if (empty($availability)) {
            return null;
        }

        $date = null === $date ? new \DateTime() : clone $date;
        $date->setTime(0, 0);

        if (Availability::IMMEDIATE === $availability) {
            return $date;
        }
        if (Availability::WITHIN_1_MONTH === $availability) {
            return $date->modify('+1 month');
        }
        if (Availability::WITHIN_2_MONTH === $availability) {
            return $date->modify('+2 months');
        }
        if (Availability::WITHIN_3_MONTH === $availability) {
            return $date->modify('+3 months');
        }

        return null;
    }

    public function deleteUser(User $user, ?User $deletedBy): void
    {
        $user->setEmail(null)
            ->setPlainPassword(null)
            ->setNickname(null)
            ->setFirstName(null)
            ->setLastName(null)
            ->setGender(null)
            ->setJobTitle(null)
            ->setWebsite(null)
            ->setSignature(null)
            ->setAvatar(null)
            ->setAvatarFile(null)
            ->setDeletedAt(new \DateTime())
            ->setDeletedBy($deletedBy)
            ->setNotification(null)
            ->setData(null)
        ;

        $this->uploadHandler->remove($user, 'avatarFile');

        foreach ($user->getProviders()->getValues() as $provider) {
            $this->em->remove($provider);
        }

        foreach ($user->getBlogComments()->getValues() as $blogComment) {
            // $this->em->remove($blogComment); TODO ticket FW
        }

        foreach ($user->getJobPostingSearches()->getValues() as $search) {
            $this->em->remove($search);
        }

        foreach ($user->getApplications()->getValues() as $application) {
            // $this->em->remove($application); TODO ticket FW
        }

        $this->deleteProfile($user);
    }

    public function deleteProfile(User $user): void
    {
        $user
            // STEP: personal_info
            ->setPhone(null)
            ->setBirthdate(null)
            ->setLocation(null)
            ->setDrivingLicense(false)
            ->setProfileJobTitle(null)
            ->setExperienceYear(null)

            // STEP: job_search_preferences
            ->setEmploymentTime(null)
            ->setEmployee(false)
            ->setGrossAnnualSalary(null)
            ->setEmployeeCurrency(null)
            ->setFreelance(false)
            ->setFreelanceLegalStatus(null)
            ->setAverageDailyRate(null)
            ->setFreelanceCurrency(null)
            ->setCompanyRegistrationNumberBeingAttributed(false)
            ->setCompanyCountryCode(null)
            ->setCompanyRegistrationNumber(null)
            ->setUmbrellaCompany(null)
            ->setFulltimeTeleworking(false)

            // STEP: education
            ->setFormation(null)

            // STEP: about_me
            ->setIntroduceYourself(null)
            ->setProfileWebsite(null)
            ->setProfileLinkedInProfile(null)
            ->setProfileProjectWebsite(null)

            // linked profile fields
            ->setVisible(null)
            ->setAvailability(null)
            ->setNextAvailabilityAt(null)
            ->setProfileCompleted(false)
            ->setFormStep(null)
        ;

        // STEP: personnal_info
        foreach ($user->getDocuments()->getValues() as $document) {
            $this->uploadHandler->remove($document, 'documentFile');
            $this->em->remove($document);
        }

        // STEP: job_search_preferences
        foreach ($user->getJobs()->getValues() as $job) {
            $this->em->remove($job);
        }
        foreach ($user->getLocations()->getValues() as $mobility) {
            $this->em->remove($mobility);
        }

        // STEP: skills_and_languages
        foreach ($user->getSkills()->getValues() as $userSkill) {
            $this->em->remove($userSkill);
        }
        foreach ($user->getSoftSkills()->getValues() as $softSkill) {
            $user->removeSoftSkill($softSkill);
        }
        foreach ($user->getLanguages()->getValues() as $userLanguage) {
            $this->em->remove($userLanguage);
        }
    }
}
