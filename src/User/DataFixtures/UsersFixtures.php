<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\JobFixtures;
use App\Core\DataFixtures\SoftSkillsFixtures;
use App\Core\Entity\Location;
use App\Core\Entity\SoftSkill;
use App\Core\Enum\EmploymentTime;
use App\Core\Enum\Gender;
use App\Core\Util\Arrays;
use App\Core\Util\Dates;
use App\Core\Util\Files;
use App\JobPosting\Enum\Contract;
use App\Partner\DataFixtures\PartnerFixtures;
use App\Partner\Entity\Partner;
use App\Partner\Enum\Partner as PartnerEnum;
use App\User\Entity\InsuranceCompany;
use App\User\Entity\UmbrellaCompany;
use App\User\Entity\User;
use App\User\Entity\UserData;
use App\User\Entity\UserFormation;
use App\User\Entity\UserNotification;
use App\User\Enum\Availability;
use App\User\Enum\CompanyCountryCode;
use App\User\Enum\ExperienceYear;
use App\User\Enum\FreelanceLegalStatus;
use App\User\Enum\UserProfileStep;
use App\User\Manager\UserManager;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Faker\Generator;
use Gaufrette\Filesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use libphonenumber\PhoneNumber;
use Nette\Utils\Strings;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UsersFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $hasher;
    private int $passwordRequestTtl;
    private int $emailRequestTtl;
    private array $avatars = [];
    private array $softSkills = [];
    private array $locations = [];
    private array $umbrellaCompanies = [];
    private array $insuranceCompanies = [];
    private array $partners = [];
    private Filesystem $filesystem;
    private DenormalizerInterface $denormalizer;
    private int $emailConfirmTtl;
    private Generator $faker;
    private array $names = [];

    public function __construct(string $env, UserPasswordHasherInterface $hasher, FilesystemMap $filesystemMap, DenormalizerInterface $denormalizer, int $passwordRequestTtl, int $emailRequestTtl, int $emailConfirmTtl)
    {
        parent::__construct($env);
        $this->hasher = $hasher;
        $this->passwordRequestTtl = $passwordRequestTtl;
        $this->emailRequestTtl = $emailRequestTtl;
        $this->filesystem = $filesystemMap->get('user_avatar_fs');
        $this->denormalizer = $denormalizer;
        $this->emailConfirmTtl = $emailConfirmTtl;
        $this->faker = Faker::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // fetch avatars
        $this->avatars = [
            '1' => __DIR__ . '/files/user-avatar-1.jpg',
            '2' => __DIR__ . '/files/user-avatar-2.jpg',
            '3' => __DIR__ . '/files/user-avatar-3.jpg',
        ];

        // fetch softSkills
        foreach ($manager->getRepository(SoftSkill::class)->findAll() as $softSkill) {
            /* @var SoftSkill $softSkill */
            $this->softSkills[$softSkill->getName()] = $softSkill;
        }

        // fetch umbrellaCompanies
        foreach ($manager->getRepository(UmbrellaCompany::class)->findAll() as $company) {
            /* @var UmbrellaCompany $company */
            $this->umbrellaCompanies[$company->getName()] = $company;
        }

        // fetch insuranceCompanies
        foreach ($manager->getRepository(InsuranceCompany::class)->findAll() as $company) {
            /* @var InsuranceCompany $company */
            $this->insuranceCompanies[$company->getName()] = $company;
        }

        // fetch locations
        $this->locations = array_map(static function (string $location) {
            return json_decode($location, true);
        }, [
            'lyon' => '{"subLocality":null,"locality":"Lyon","localitySlug":"lyon","postalCode":null,"adminLevel1":"Auvergne-Rhône-Alpes","adminLevel1Slug":"auvergne-rhone-alpes","adminLevel2":"Métropole de Lyon","adminLevel2Slug":"metropole-de-lyon","country":"France","countryCode":"FR","latitude":"45.7578137","longitude":"4.8320114"}',
            'paris' => '{"subLocality":"Paris","locality":"Paris","localitySlug":"paris","postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.8588897","longitude":"2.3200410217201"}',
            'idf' => '{"subLocality":null,"locality":null,"localitySlug":null,"postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.6443057","longitude":"2.7537863"}',
        ]);

        // fetch partners
        foreach ($manager->getRepository(Partner::class)->findByPartner(PartnerEnum::getPartners()) as $partner) {
            /* @var Partner $partner */
            $this->partners[$partner->getPartner()] = $partner;
        }

        $defaults = [
            'password' => $this->hasher->hashPassword(new User(), 'P@ssw0rd'),
        ];

        foreach ($this->getData() as $dataUser) {
            $user = (new User())
                ->setFirstName($dataUser['firstName'] ?? null)
                ->setLastName($dataUser['lastName'] ?? null)
                ->setEmail($dataUser['email'] ?? null)
                ->setPhone($dataUser['phone'] ?? null)
                ->setNickname($dataUser['nickname'] ?? null)
                ->setJobTitle($dataUser['jobTitle'] ?? null)
                ->setWebsite($dataUser['website'] ?? null)
                ->setSignature($dataUser['signature'] ?? null)
                ->setGender($dataUser['gender'] ?? null)
                ->setPassword($dataUser['password'] ?? $defaults['password'])
                ->setBirthdate($dataUser['birthdate'] ?? null)
                ->setPasswordRequestedAt($dataUser['passwordRequestedAt'] ?? null)
                ->setEmailRequestedAt($dataUser['emailRequestedAt'] ?? null)
                ->setConfirmationToken($dataUser['confirmationToken'] ?? null)
                ->setEnabled($dataUser['enabled'] ?? true)
                ->setLocked($dataUser['locked'] ?? false)
                ->setRoles($dataUser['roles'] ?? [])
                ->setDeletedAt($dataUser['deletedAt'] ?? null)
                ->setCreatedAt($dataUser['createdAt'] ?? null)
                ->setUpdatedAt($dataUser['updatedAt'] ?? null)
                ->setDisplayAvatar($dataUser['displayAvatar'] ?? false)
                ->setProfileJobTitle($dataUser['profileJobTitle'] ?? null)
                ->setExperienceYear($dataUser['experienceYear'] ?? null)
                ->setAvailability($dataUser['availability'] ?? null)
                ->setStatusUpdatedAt($dataUser['statusUpdatedAt'] ?? ($dataUser['updatedAt'] ?? null))
                ->setNextAvailabilityAt(
                    $dataUser['nextAvailabilityAt'] ??
                    UserManager::calculateNextAvailabilityAt($dataUser['availability'] ?? null, $dataUser['statusUpdatedAt'] ?? null)
                )
                ->setProfileWebsite($dataUser['profileWebsite'] ?? null)
                ->setProfileLinkedInProfile($dataUser['profileLinkedInProfile'] ?? null)
                ->setProfileProjectWebsite($dataUser['profileProjectWebsite'] ?? null)
                ->setEmploymentTime($dataUser['employmentTime'] ?? null)
                ->setFormStep($dataUser['formStep'] ?? UserProfileStep::PERSONAL_INFO)
                ->setIntroduceYourself($dataUser['introduceYourself'] ?? null)
                ->setDrivingLicense($dataUser['drivingLicense'] ?? false)
                ->setFulltimeTeleworking($dataUser['fulltimeTeleworking'] ?? false)
                ->setFreelance($dataUser['freelance'] ?? false)
                ->setAverageDailyRate($dataUser['averageDailyRate'] ?? null)
                ->setFreelanceCurrency($dataUser['freelanceCurrency'] ?? null)
                ->setFreelanceLegalStatus($dataUser['freelanceLegalStatus'] ?? null)
                ->setCompanyCountryCode($dataUser['companyCountryCode'] ?? null)
                ->setCompanyRegistrationNumber($dataUser['companyRegistrationNumber'] ?? null)
                ->setCompanyRegistrationNumberBeingAttributed($dataUser['companyRegistrationNumberBeinAttributed'] ?? false)
                ->setProfileCompleted($dataUser['profileCompleted'] ?? false)
                ->setAnonymous($dataUser['anonymous'] ?? false)
                ->setEmployee($dataUser['employee'] ?? false)
                ->setGrossAnnualSalary($dataUser['grossAnnualSalary'] ?? null)
                ->setEmployeeCurrency($dataUser['employeeCurrency'] ?? null)
                ->setLocation(\array_key_exists('location', $dataUser) && $dataUser['location'] ? $this->denormalizer->denormalize($dataUser['location'], Location::class) : null)
                ->setUmbrellaCompany($dataUser['umbrellaCompany'] ?? null)
                ->setInsuranceCompany($dataUser['insuranceCompany'] ?? null)
                ->setBanned($dataUser['banned'] ?? false)
                ->setIp($dataUser['ip'] ?? null)
                ->setVisible($dataUser['visible'] ?? true)
                ->setContracts($dataUser['contracts'] ?? null)
                ->setTermsOfService($dataUser['termsOfService'] ?? false)
                ->setLastLoginAt($dataUser['lastLoginAt'] ?? null)
                ->setLastLoginProvider($dataUser['lastLoginProvider'] ?? null)
                ->setPartner($dataUser['partner'] ?? null)
            ;

            if (null !== ($dataUser['avatarFile'] ?? null)) {
                $user->setAvatarFile(Files::getUploadedFile($dataUser['avatarFile']));
            } elseif (null !== ($dataUser['avatar'] ?? null)) {
                $avatarPath = $dataUser['avatar']['path'];
                $avatarBasename = $dataUser['avatar']['basename'];
                if (false === $avatarContent = file_get_contents($avatarPath)) {
                    throw new \InvalidArgumentException();
                }
                $this->filesystem->write($avatarBasename, $avatarContent, true);
                $user->setAvatar($avatarBasename);
            }

            foreach ($dataUser['softSkills'] ?? [] as $softSkill) {
                $user->addSoftSkill($softSkill);
            }

            if (null !== ($dataUser['formation'] ?? null)) {
                $formation = (new UserFormation())
                    ->setDiplomaTitle($dataUser['formation']['diplomaTitle'])
                    ->setDiplomaLevel($dataUser['formation']['diplomaLevel'])
                    ->setSchool($dataUser['formation']['school'])
                    ->setDiplomaYear($dataUser['formation']['diplomaYear'])
                    ->setBeingObtained($dataUser['formation']['beingObtained'])
                ;
                $manager->persist($formation);

                $user->setFormation($formation);
            }

            if (null !== ($dataUser['notification'] ?? null)) {
                $notification = (new UserNotification())
                    ->setForumTopicReply($dataUser['notification']['forumTopicReply'])
                    ->setForumTopicFavorite($dataUser['notification']['forumTopicFavorite'])
                    ->setForumPostReply($dataUser['notification']['forumPostReply'])
                    ->setForumPostLike($dataUser['notification']['forumPostLike'])
                    ->setMessagingNewMessage($dataUser['notification']['messagingNewMessage'])
                ;
                $user->setNotification($notification);
            }

            if (null !== ($dataUser['data'] ?? null)) {
                $data = (new UserData())
                    ->setLastActivityAt($dataUser['data']['lastActivityAt'] ?? null)
                    ->setLastForumActivityAt($dataUser['data']['lastForumActivityAt'] ?? null)
                    ->setCronProfileNotVisibleExecAt($dataUser['data']['cronProfileNotVisibleExecAt'] ?? null)
                    ->setCronProfileUncompletedExecAt($dataUser['data']['cronProfileUncompletedExecAt'] ?? null)
                    ->setCronNoJobPostingSearchExecAt($dataUser['data']['cronNoJobPostingSearchExecAt'] ?? null)
                    ->setCronAlertMissionsExecAt($dataUser['data']['cronAlertMissionsExecAt'] ?? null)
                ;
                $user->setData($data);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [
            [
                'email' => 'zzidane@free-work.fr',
                'firstName' => 'Zinédine',
                'lastName' => 'Zidane',
                'termsOfService' => true,
                'roles' => ['ROLE_ADMIN'],
                'nickname' => 'Zinedine-Zidane',
                'jobTitle' => 'Footballer',
                'profileJobTitle' => 'Footballer',
                'experienceYear' => ExperienceYear::MORE_THAN_15_YEARS,
                'availability' => Availability::IMMEDIATE,
                'employmentTime' => EmploymentTime::FULL_TIME,
                'website' => 'https://fr.wikipedia.org/wiki/Zin%C3%A9dine_Zidane',
                'signature' => 'ZZ',
                'avatarFile' => __DIR__ . '/files/user-avatar-zinedine-zidane.jpg',
                'displayAvatar' => true,
                'gender' => Gender::MALE,
                'profileWebsite' => 'https://fr.wikipedia.org/wiki/Zin%C3%A9dine_Zidane',
                'profileLinkedInProfile' => 'https://www.linkedin.com/in/zinedin-zidane',
                'profileProjectWebsite' => 'https://ela-asso.com/fiche_parrain/zinedine-zidane/',
                'freelance' => true,
                'averageDailyRate' => 1500,
                'freelanceCurrency' => 'EUR',
                'freelanceLegalStatus' => FreelanceLegalStatus::UMBRELLA_COMPANY,
                'companyCountryCode' => CompanyCountryCode::FR,
                'companyRegistrationNumber' => '123456789',
                'companyRegistrationNumberBeinAttributed' => true,
                'profileCompleted' => true,
                'anonymous' => false,
                'employee' => true,
                'grossAnnualSalary' => 500000,
                'employeeCurrency' => 'EUR',
                'introduceYourself' => 'Zinédine Zidane, né le 23 juin 1972 à Marseille, est un footballeur international français devenu entraîneur. Durant sa carrière de joueur, entre 1988 et 2006, il évolue au poste de milieu offensif, comme meneur de jeu. De janvier 2016 à fin mai 2018, et de mars 2019 à mai 2021, il est l\'entraîneur du Real Madrid, où il a terminé sa carrière de joueur. ',
                'drivingLicense' => true,
                'fulltimeTeleworking' => true,
                'location' => $this->locations['paris'],
                'softSkills' => Arrays::getRandomSubarray($this->softSkills, 0, 3),
                'umbrellaCompany' => Arrays::getRandom($this->umbrellaCompanies),
                'insuranceCompany' => Arrays::getRandom($this->insuranceCompanies),
                'birthdate' => new \DateTime('1972-06-23'),
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'lastLoginAt' => new \DateTime('2020-01-01 10:15:00'),
                'lastLoginProvider' => 'google',
                'ip' => $this->faker->ipv4,
                'notification' => [
                    'marketingNewsletter' => true,
                    'forumTopicReply' => true,
                    'forumTopicFavorite' => true,
                    'forumPostReply' => true,
                    'forumPostLike' => true,
                    'messagingNewMessage' => true,
                ],
                'contracts' => [
                    Contract::FIXED_TERM,
                    Contract::PERMANENT,
                ],
            ],
            [
                'email' => 'thenry@free-work.fr',
                'firstName' => 'Thierry',
                'lastName' => 'Henry',
                'termsOfService' => true,
                'roles' => [],
                'nickname' => 'Thierry-Henry',
                'avatarFile' => __DIR__ . '/files/user-avatar-thierry-henry.jpg',
                'freelance' => true,
                'averageDailyRate' => 1200,
                'freelanceCurrency' => 'USD',
                'freelanceLegalStatus' => FreelanceLegalStatus::SELF_EMPLOYED,
                'employmentTime' => EmploymentTime::PART_TIME,
                'companyCountryCode' => CompanyCountryCode::GB,
                'companyRegistrationNumber' => '123456789',
                'companyRegistrationNumberBeinAttributed' => false,
                'profileCompleted' => true,
                'anonymous' => false,
                'employee' => true,
                'grossAnnualSalary' => 400000,
                'employeeCurrency' => 'USD',
                'drivingLicense' => true,
                'fulltimeTeleworking' => false,
                'location' => $this->locations['lyon'],
                'softSkills' => Arrays::getRandomSubarray($this->softSkills, 0, 3),
                'birthdate' => new \DateTime('1977-08-17'),
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-02 13:00:00'),
                ],
                'ip' => $this->faker->ipv4,
                'contracts' => [
                    Contract::APPRENTICESHIP,
                    Contract::INTERNSHIP,
                    Contract::FIXED_TERM,
                    Contract::PERMANENT,
                ],
                'partner' => Arrays::getRandom($this->partners),
            ],
            [
                'email' => 'charlene.herent@free-work.fr',
                'firstName' => 'Charlene',
                'lastName' => 'Herent',
                'termsOfService' => true,
                'roles' => ['ROLE_ADMIN'],
                'active' => true,
                'nickname' => 'Charlene-Herent',
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastActivityAt' => new \DateTime('2020-01-02 13:00:00'),
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                ],
                'ip' => $this->faker->ipv4,
            ],
            [
                'email' => 'jacques.delamballerie@free-work.fr',
                'firstName' => 'Jacques',
                'lastName' => 'Nicolas de Lamballerie',
                'termsOfService' => true,
                'roles' => ['ROLE_ADMIN'],
                'active' => true,
                'nickname' => 'jacquesndl',
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-02 13:00:00'),
                ],
                'ip' => $this->faker->ipv4,
            ],
            [
                'email' => 'user-deleted@free-work.fr',
                'termsOfService' => false,
                'firstName' => null,
                'lastName' => null,
                'nickname' => null,
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'deletedAt' => new \DateTime('2020-01-02 12:00:00'),
                'ip' => $this->faker->ipv4,
            ],
            [
                'email' => 'jmleglise@gmail.com',
                'firstName' => 'Jean-Marc',
                'lastName' => 'Léglise',
                'termsOfService' => true,
                'roles' => ['ROLE_ADMIN'],
                'active' => true,
                'nickname' => 'jmleglise',
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-02 13:00:00'),
                ],
                'ip' => $this->faker->ipv4,
            ],
        ];

        $usersCount = 50;
        for ($i = 0; $i < $usersCount; ++$i) {
            $name = $this->fakeUniqueName();
            $firstName = $name['firstName'];
            $lastName = $name['lastName'];

            $createdAt = $this->faker->dateTimeBetween('- 9 months', '- 7 months');
            $updatedAt = $this->faker->dateTimeBetween('- 7 months', '- 6 months');
            $lastForumActivityAt = $this->faker->dateTimeBetween('- 6 months', '- 5 months');
            $lastActivityAt = $this->faker->dateTimeBetween('- 2 months');
            $birthdate = $this->faker->dateTimeBetween('- 90 years', '- 16 years');

            $hasAvatar = 0 === mt_rand(0, 3);
            $hasResume = 0 === mt_rand(0, 2);
            $hasFreelance = $hasResume && 0 === mt_rand(0, 1);
            $hasEmployee = $hasResume && 0 === mt_rand(0, 1);
            $currentYear = (int) date('Y');
            $diplomaYear = mt_rand($currentYear - 30, $currentYear);

            $data[] = [
                'email' => Strings::webalize($firstName . ' ' . $lastName) . '@free-work.fr',
                'firstName' => $firstName,
                'lastName' => $lastName,
                'termsOfService' => true,
                'phone' => mt_rand(0, 1) ? null : (new PhoneNumber())->setRawInput($this->faker->e164PhoneNumber()),
                'nickname' => 0 === mt_rand(0, 1) ? sprintf('%s-%s', $firstName, $lastName) : null,
                'roles' => [],
                'enabled' => 0 !== mt_rand(0, 10),
                'locked' => 0 === mt_rand(0, 20),
                'gender' => 0 === mt_rand(0, 1) ? Arrays::getRandom(Gender::getConstants()) : null,
                'birthdate' => 0 === mt_rand(0, 1) ? $birthdate->setTime(0, 0, 0, 0) : null,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'data' => [
                    'lastActivityAt' => $lastActivityAt,
                    'lastForumActivityAt' => $lastForumActivityAt,
                ],
                'jobTitle' => 0 === random_int(0, 3) ? $this->faker->jobTitle : null,
                'profileJobTitle' => true === $hasResume ? $this->faker->jobTitle : null,
                'experienceYear' => true === $hasResume ? Arrays::getRandom(ExperienceYear::getConstants()) : null,
                'availability' => true === $hasResume ? Arrays::getRandom(Availability::getConstants()) : null,
                'employmentTime' => true === $hasResume ? Arrays::getRandom(EmploymentTime::getConstants()) : null,
                'website' => 0 === mt_rand(0, 3) ? $this->faker->url() : null,
                'signature' => 0 === mt_rand(0, 3) ? $firstName . $lastName : null,
                'avatarFile' => true === $hasAvatar ? Arrays::getRandom($this->avatars) : null,
                'displayAvatar' => (true === $hasAvatar && 0 === mt_rand(0, 1)),
                'profileWebsite' => 0 === mt_rand(0, 3) ? $this->faker->url() : null,
                'profileLinkedInProfile' => 0 === mt_rand(0, 4) ? $this->faker->url() : null,
                'profileProjectWebsite' => 0 === mt_rand(0, 5) ? $this->faker->url() : null,
                'freelance' => true === $hasFreelance,
                'averageDailyRate' => true === $hasFreelance ? $this->faker->numberBetween(300, 1500) : null,
                'freelanceCurrency' => true === $hasFreelance ? $this->faker->currencyCode : null,
                'freelanceLegalStatus' => true === $hasFreelance ? Arrays::getRandom(FreelanceLegalStatus::getConstants()) : null,
                'companyCountryCode' => true === $hasFreelance ? Arrays::getRandom(CompanyCountryCode::getConstants()) : null,
                'companyRegistrationNumber' => true === $hasFreelance ? $this->faker->numberBetween(100000000, 999999999) : null,
                'companyRegistrationNumberBeinAttributed' => true === $hasFreelance && 0 === mt_rand(0, 1),
                'profileCompleted' => $hasFreelance || $hasEmployee,
                'employee' => true === $hasEmployee,
                'grossAnnualSalary' => true === $hasEmployee ? $this->faker->numberBetween(20000, 100000) : null,
                'employeeCurrency' => true === $hasFreelance ? $this->faker->currencyCode : null,
                'introduceYourself' => true === $hasResume && 0 === mt_rand(0, 1) ? $this->faker->text(mt_rand(300, 600)) : null,
                'drivingLicense' => true === $hasResume && 0 !== mt_rand(0, 4),
                'fulltimeTeleworking' => true === $hasResume && 0 === mt_rand(0, 1),
                'location' => 0 === mt_rand(0, 1) ? Arrays::getRandom($this->locations) : null,
                'softSkills' => true === $hasResume ? Arrays::getRandomSubarray($this->softSkills, 0, 3) : null,
                'umbrellaCompany' => true === $hasFreelance ? Arrays::getRandom($this->umbrellaCompanies) : null,
                'insuranceCompany' => true === $hasFreelance ? Arrays::getRandom($this->insuranceCompanies) : null,
                'formation' => (true === $hasResume && 0 !== mt_rand(0, 4)) ? [
                    'diplomaTitle' => $this->faker->text(mt_rand(20, 40)),
                    'diplomaLevel' => mt_rand(0, 10),
                    'school' => $this->faker->company,
                    'diplomaYear' => $diplomaYear,
                    'beingObtained' => mt_rand(0, 1) && $diplomaYear > $currentYear - 5,
                ] : null,
                'notification' => 0 === mt_rand(0, 1) ? [
                    'marketingNewsletter' => mt_rand(0, 1),
                    'forumTopicReply' => mt_rand(0, 1),
                    'forumTopicFavorite' => mt_rand(0, 1),
                    'forumPostReply' => mt_rand(0, 1),
                    'forumPostLike' => mt_rand(0, 1),
                    'messagingNewMessage' => mt_rand(0, 1),
                ] : null,
                'ip' => $this->faker->ipv4,
                'contracts' => true === $hasResume ? array_values(Arrays::getRandomSubarray(Contract::getWorkValues(), 1, 3)) : null,
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        $now = Carbon::now();

        return [
            [
                'email' => 'user@free-work.fr',
                'firstName' => 'User',
                'lastName' => 'Free-Work',
                'termsOfService' => true,
                'nickname' => 'User-Free-Work',
                'gender' => Gender::MALE,
                'profileJobTitle' => 'User',
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'admin@free-work.fr',
                'firstName' => 'Admin',
                'lastName' => 'Free-Work',
                'termsOfService' => true,
                'roles' => ['ROLE_ADMIN'],
                'nickname' => 'Admin-Free-Work',
                'gender' => Gender::MALE,
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-forgotten-password-with-active-request@free-work.fr',
                'firstName' => 'User Forgotten Password With Active Request',
                'lastName' => 'Free-Work',
                'termsOfService' => true,
                'passwordRequestedAt' => (new \DateTime())->modify(sprintf('- %d seconds', $this->passwordRequestTtl / 2)),
                'confirmationToken' => 'token-active',
                'nickname' => 'User-Forgotten-Password-With-Active-Request-Free-Work',
                'gender' => Gender::FEMALE,
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-forgotten-password-with-expired-request@free-work.fr',
                'firstName' => 'User Forgotten Password With Expired Request',
                'lastName' => 'Free-Work',
                'termsOfService' => true,
                'passwordRequestedAt' => (new \DateTime())->modify(sprintf('- %d seconds', $this->passwordRequestTtl * 2)),
                'confirmationToken' => 'token-expired',
                'nickname' => 'User-Forgotten-Password-With-Expired-Request-Free-Work',
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-forgotten-password-without-request@free-work.fr',
                'firstName' => 'User Forgotten Password Without Request',
                'lastName' => 'Free-Work',
                'termsOfService' => true,
                'nickname' => 'User-Forgotten-Password-Without-Request-Free-Work',
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'claude.monet@free-work.fr',
                'firstName' => 'Claude',
                'lastName' => 'Monet',
                'termsOfService' => true,
                'nickname' => 'Claude-Monet',
                'jobTitle' => 'Peintre',
                'profileJobTitle' => 'Peintre',
                'experienceYear' => ExperienceYear::YEARS_3_4,
                'availability' => Availability::IMMEDIATE,
                'employmentTime' => EmploymentTime::FULL_TIME,
                'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                'signature' => 'Claude Monet.',
                'avatar' => [
                    'path' => $this->avatars['1'],
                    'basename' => 'monet-avatar.jpg',
                ],
                'displayAvatar' => true,
                'gender' => Gender::MALE,
                'profileWebsite' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                'profileLinkedInProfile' => 'https://www.linkedin.com/in/claude-monet',
                'profileProjectWebsite' => 'https://www.association-artistique-monet.fr/',
                'freelance' => true,
                'freelanceCurrency' => 'EUR',
                'freelanceLegalStatus' => FreelanceLegalStatus::SELF_EMPLOYED,
                'companyCountryCode' => CompanyCountryCode::FR,
                'companyRegistrationNumber' => '123456789',
                'companyRegistrationNumberBeinAttributed' => false,
                'profileCompleted' => true,
                'anonymous' => false,
                'employee' => true,
                'grossAnnualSalary' => 40000,
                'averageDailyRate' => 300,
                'employeeCurrency' => 'EUR',
                'formStep' => UserProfileStep::ABOUT_ME,
                'introduceYourself' => 'Claude Monnet',
                'drivingLicense' => true,
                'fulltimeTeleworking' => true,
                'softSkills' => [
                    $this->softSkills['SoftSkill 1'],
                    $this->softSkills['SoftSkill 3'],
                ],
                'birthdate' => new \DateTime('1840-11-14'),
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'location' => $this->locations['paris'],
                'formation' => [
                    'diplomaTitle' => 'Formation - DiplomaTitle - 1',
                    'diplomaLevel' => 15,
                    'school' => 'Formation - School - 1',
                    'diplomaYear' => 1857,
                    'beingObtained' => false,
                ],
                'notification' => [
                    'marketingNewsletter' => true,
                    'forumTopicReply' => true,
                    'forumTopicFavorite' => true,
                    'forumPostReply' => true,
                    'forumPostLike' => true,
                    'messagingNewMessage' => true,
                ],
                'ip' => '1.2.3.4',
                'contracts' => [
                    Contract::FIXED_TERM,
                    Contract::PERMANENT,
                ],
                'partner' => $this->partners[PartnerEnum::FREELANCECOM],
            ],
            [
                'email' => 'vincent.van-gogh@free-work.fr',
                'firstName' => 'Vincent',
                'lastName' => 'van Gogh',
                'termsOfService' => true,
                'nickname' => 'Vincent-van-Gogh',
                'profileJobTitle' => 'Peintre',
                'experienceYear' => ExperienceYear::YEARS_5_10,
                'availability' => Availability::NONE,
                'employmentTime' => EmploymentTime::PART_TIME,
                'gender' => Gender::MALE,
                'profileLinkedInProfile' => 'https://www.linkedin.com/in/vincent-van-gogh',
                'freelance' => true,
                'averageDailyRate' => 600,
                'freelanceCurrency' => 'USD',
                'freelanceLegalStatus' => FreelanceLegalStatus::UMBRELLA_COMPANY,
                'companyCountryCode' => CompanyCountryCode::NL,
                'formStep' => UserProfileStep::SKILLS_AND_LANGUAGES,
                'companyRegistrationNumber' => '123456789',
                'companyRegistrationNumberBeinAttributed' => true,
                'profileCompleted' => true,
                'anonymous' => true,
                'employee' => true,
                'grossAnnualSalary' => 45000,
                'employeeCurrency' => 'USD',
                'introduceYourself' => 'Vincent van Gogh',
                'drivingLicense' => false,
                'fulltimeTeleworking' => true,
                'umbrellaCompany' => $this->umbrellaCompanies['Umbrella Company 1'],
                'birthdate' => new \DateTime('1853-03-30'),
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'formation' => [
                    'diplomaTitle' => 'Formation - DiplomaTitle - 2',
                    'diplomaLevel' => 10,
                    'school' => 'Formation - School - 2',
                    'diplomaYear' => 1863,
                    'beingObtained' => false,
                ],
                'notification' => [
                    'marketingNewsletter' => true,
                    'forumTopicReply' => true,
                    'forumTopicFavorite' => true,
                    'forumPostReply' => true,
                    'forumPostLike' => true,
                    'messagingNewMessage' => true,
                ],
                'ip' => '1.2.3.4',
                'contracts' => [
                    Contract::APPRENTICESHIP,
                    Contract::INTERNSHIP,
                    Contract::FIXED_TERM,
                    Contract::PERMANENT,
                ],
                'partner' => $this->partners[PartnerEnum::FREELANCECOM],
            ],
            [
                'email' => 'auguste.renoir@free-work.fr',
                'firstName' => 'Auguste',
                'lastName' => 'Renoir',
                'termsOfService' => true,
                'nickname' => 'Auguste-Renoir',
                'profileJobTitle' => 'Peintre',
                'experienceYear' => ExperienceYear::YEARS_1_2,
                'availability' => Availability::WITHIN_2_MONTH,
                'employmentTime' => EmploymentTime::FULL_TIME,
                'gender' => Gender::MALE,
                'profileWebsite' => 'https://fr.wikipedia.org/wiki/Auguste_Renoir',
                'freelance' => true,
                'averageDailyRate' => 800,
                'freelanceCurrency' => 'EUR',
                'freelanceLegalStatus' => FreelanceLegalStatus::STATUS_IN_PROGRESS,
                'profileCompleted' => true,
                'anonymous' => false,
                'drivingLicense' => true,
                'fulltimeTeleworking' => false,
                'softSkills' => [
                    $this->softSkills['SoftSkill 2'],
                    $this->softSkills['SoftSkill 4'],
                ],
                'birthdate' => new \DateTime('1853-03-30'),
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'notification' => [
                    'marketingNewsletter' => false,
                    'forumTopicReply' => false,
                    'forumTopicFavorite' => false,
                    'forumPostReply' => false,
                    'forumPostLike' => false,
                    'messagingNewMessage' => false,
                ],
                'ip' => '1.2.3.4',
                'partner' => $this->partners[PartnerEnum::FREELANCECOM],
            ],
            [
                'email' => 'henri.matisse@free-work.fr',
                'firstName' => 'Henri',
                'lastName' => 'Matisse',
                'termsOfService' => true,
                'nickname' => 'Henri-Matisse',
                'profileJobTitle' => 'Peintre',
                'experienceYear' => ExperienceYear::LESS_THAN_1_YEAR,
                'availability' => Availability::WITHIN_3_MONTH,
                'employmentTime' => EmploymentTime::FULL_TIME,
                'gender' => Gender::MALE,
                'avatar' => [
                    'path' => $this->avatars['2'],
                    'basename' => 'matisse-avatar.jpg',
                ],
                'displayAvatar' => false,
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'pablo.picasso@free-work.fr',
                'firstName' => 'Pablo',
                'lastName' => 'Picasso',
                'termsOfService' => true,
                'nickname' => 'Pablo-Picasso',
                'gender' => Gender::MALE,
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime(),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
                'freelance' => true,
            ],
            [
                'email' => 'elisabeth.vigee-le-brun@free-work.fr',
                'termsOfService' => true,
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime(),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-locked@free-work.fr',
                'firstName' => 'User Locked',
                'lastName' => 'Free-Work',
                'nickname' => 'User-Locked',
                'termsOfService' => false,
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'locked' => true,
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-new-email-with-expired-request@free-work.fr',
                'firstName' => 'User New Email With Expired Request',
                'lastName' => 'Free-Work',
                'termsOfService' => true,
                'nickname' => 'User-New-Email-With-Expired-Request-Free-Work',
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'emailRequestedAt' => (new \DateTime())->modify(sprintf('- %d seconds', $this->emailRequestTtl * 2)),
                'confirmationToken' => 'a1dacc3c1a3c93dcf7a5',
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-new-email-with-active-request@free-work.fr',
                'firstName' => 'User New Email With Active Request',
                'lastName' => 'Free-Work',
                'termsOfService' => true,
                'emailRequestedAt' => (new \DateTime())->modify(sprintf('- %d seconds', $this->emailRequestTtl / 2)),
                'confirmationToken' => '5e9e4e3910906a9a75c9',
                'nickname' => 'User-New-Email-With-Active-Request-Free-Work',
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-new-email-confirm-enabled@free-work.fr',
                'firstName' => 'User New Email Confirm Enabled',
                'lastName' => 'Free-Work',
                'termsOfService' => true,
                'emailRequestedAt' => (new \DateTime())->modify(sprintf('- %d seconds', $this->emailRequestTtl / 2)),
                'confirmationToken' => sha1(substr('new-email@free-work.fr', 0, 10)),
                'nickname' => 'User-New-Email-Confirm-Enabled-Free-Work',
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-01 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-trend-1@free-work.fr',
                'firstName' => 'User',
                'lastName' => 'Trend 1',
                'termsOfService' => true,
                'roles' => ['ROLE_USER'],
                'active' => true,
                'nickname' => 'user-trend-1',
                'gender' => Gender::MALE,
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                    'lastActivityAt' => Dates::lastWeek()->modify('+ 2 days'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
                'fulltimeTeleworking' => true,
                'freelance' => true,
                'employee' => false,
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-trend-2@free-work.fr',
                'firstName' => 'User',
                'lastName' => 'Trend 2',
                'termsOfService' => true,
                'roles' => ['ROLE_USER'],
                'active' => true,
                'nickname' => 'user-trend-2',
                'gender' => Gender::MALE,
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                    'lastActivityAt' => Dates::lastWeek()->modify('+ 3 days'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
                'fulltimeTeleworking' => true,
                'freelance' => true,
                'employee' => true,
                'companyRegistrationNumber' => '13371337',
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-trend-3@free-work.fr',
                'firstName' => 'User',
                'lastName' => 'Trend 3',
                'termsOfService' => true,
                'roles' => ['ROLE_USER'],
                'active' => true,
                'nickname' => 'user-trend-3',
                'gender' => Gender::MALE,
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                    'lastActivityAt' => Dates::lastWeek()->modify('+ 4 days'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
                'fulltimeTeleworking' => true,
                'freelance' => true,
                'employee' => true,
                'companyRegistrationNumber' => null,
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-trend-4@free-work.fr',
                'firstName' => 'User',
                'lastName' => 'Trend 4',
                'termsOfService' => true,
                'roles' => ['ROLE_USER'],
                'active' => true,
                'nickname' => 'user-trend-4',
                'gender' => Gender::MALE,
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                    'lastActivityAt' => Dates::lastWeek()->modify('+ 4 days'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
                'fulltimeTeleworking' => false,
                'freelance' => false,
                'employee' => true,
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-trend-5@free-work.fr',
                'firstName' => 'User',
                'lastName' => 'Trend 5',
                'termsOfService' => true,
                'roles' => ['ROLE_USER'],
                'active' => true,
                'nickname' => 'user-trend-5',
                'gender' => Gender::FEMALE,
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                    'lastActivityAt' => Dates::lastWeek()->modify('+ 6 days'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
                'fulltimeTeleworking' => false,
                'freelance' => false,
                'employee' => false,
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-trend-6@free-work.fr',
                'firstName' => 'User',
                'lastName' => 'Trend 6',
                'termsOfService' => true,
                'roles' => ['ROLE_USER'],
                'active' => true,
                'nickname' => 'user-trend-6',
                'gender' => Gender::MALE,
                'createdAt' => new \DateTime('2020-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-02 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-02 12:00:00'),
                    'lastActivityAt' => Dates::lastWeek()->modify('- 2 days'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-from-freelance-info@free-work.fr',
                'firstName' => 'User From Freelance Info',
                'lastName' => 'Free-Work',
                'termsOfService' => false,
                'nickname' => 'User-From-Freelance-Info-Free-Work',
                'password' => '$2y$13$xIuXCVfWO5uLNvQ8.N5zQ.ISmdCpl/KrMN8w8t9URQVA8wFtI05y.',
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-from-carriere-info@free-work.fr',
                'firstName' => 'User From Carriere Info',
                'lastName' => 'Free-Work',
                'termsOfService' => false,
                'nickname' => 'User-From-Carriere-Info-Free-Work',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-ban@free-work.fr',
                'firstName' => 'User',
                'lastName' => 'Ban',
                'termsOfService' => true,
                'roles' => ['ROLE_USER'],
                'active' => true,
                'banned' => true,
                'nickname' => 'user-ban',
                'gender' => Gender::MALE,
                'createdAt' => new \DateTime('2020-01-03 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-03 11:00:00'),
                'ip' => '1.2.3.4',
                'data' => [
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
            ],
            [
                'email' => 'user-deleted@free-work.fr',
                'termsOfService' => false,
                'firstName' => null,
                'lastName' => null,
                'nickname' => null,
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'deletedAt' => new \DateTime('2020-01-01 14:00:00'),
                'ip' => '1.2.3.4',
            ],
            [
                'email' => 'user-not-enabled@free-work.fr',
                'termsOfService' => false,
                'createdAt' => new \DateTime('2020-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2020-01-01 11:00:00'),
                'data' => [
                    'lastForumActivityAt' => new \DateTime('2020-01-01 12:00:00'),
                    'lastActivityAt' => new \DateTime('2020-01-01 13:00:00'),
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
                'ip' => '1.2.3.4',
                'enabled' => false,
            ],
            [
                'email' => 'user-to-enable-with-expired-token@free-work.fr',
                'createdAt' => (new \DateTime())->modify(sprintf('- %d seconds', $this->emailConfirmTtl * 2)),
                'updatedAt' => (new \DateTime())->modify(sprintf('- %d seconds', $this->emailConfirmTtl * 2)),
                'enabled' => false,
                'confirmationToken' => 'email-confirm-token-expired',
                'data' => [
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
            ],
            [
                'email' => 'user-to-enable-with-active-token@free-work.fr',
                'createdAt' => (new \DateTime())->modify(sprintf('- %d seconds', $this->emailConfirmTtl / 2)),
                'updatedAt' => (new \DateTime())->modify(sprintf('- %d seconds', $this->emailConfirmTtl / 2)),
                'enabled' => false,
                'confirmationToken' => 'email-confirm-token-active',
                'data' => [
                    'cronNoJobPostingSearchExecAt' => new \DateTime('2020-01-05 13:00:00'),
                ],
            ],
            [
                'email' => 'user-without-document-registration-2-days@free-work.fr',
                'firstName' => 'User Without Document Registration 2 days',
                'lastName' => 'Free-Work',
                'nickname' => 'User-Without-Document-Registration-2-days',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subDays(2),
                'updatedAt' => $now->copy()->subDays(2)->addMinutes(3),
                'data' => [
                    'lastForumActivityAt' => $now->copy()->subDays(2)->addMinutes(3),
                    'lastActivityAt' => $now->copy()->subDays(2)->addMinutes(3),
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subDays(2)->addMinutes(3),
                ],
            ],
            [
                'email' => 'user-without-document-registration-7-days@free-work.fr',
                'firstName' => 'User Without Document Registration 7 days',
                'lastName' => 'Free-Work',
                'nickname' => 'User-Without-Document-Registration-7-days',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subDays(7),
                'updatedAt' => $now->copy()->subDays(7)->addMinutes(3),
                'data' => [
                    'lastForumActivityAt' => $now->copy()->subDays(7)->addMinutes(3),
                    'lastActivityAt' => $now->copy()->subDays(7)->addMinutes(3),
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subDays(7)->addMinutes(3),
                ],
            ],
            [
                'email' => 'user-without-document-registration-30-days@free-work.fr',
                'firstName' => 'User Without Document Registration 30 days',
                'lastName' => 'Free-Work',
                'nickname' => 'User-Without-Document-Registration-30-days',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subDays(30),
                'updatedAt' => $now->copy()->subDays(30)->addMinutes(3),
                'data' => [
                    'lastForumActivityAt' => $now->copy()->subDays(30)->addMinutes(3),
                    'lastActivityAt' => $now->copy()->subDays(30)->addMinutes(3),
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subDays(30)->addMinutes(3),
                ],
            ],
            [
                'email' => 'user-without-document-registration-40-days@free-work.fr',
                'firstName' => 'User Without Document Registration 40 days',
                'lastName' => 'Free-Work',
                'nickname' => 'User-Without-Document-Registration-40-days',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subDays(40),
                'updatedAt' => $now->copy()->subDays(40)->addMinutes(3),
                'data' => [
                    'lastForumActivityAt' => $now->copy()->subDays(40)->addMinutes(3),
                    'lastActivityAt' => $now->copy()->subDays(40)->addMinutes(3),
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subDays(40)->addMinutes(3),
                ],
            ],
            [
                'email' => 'user-registration-3-days-ago@free-work.fr',
                'firstName' => 'User Registration 3 days ago',
                'lastName' => 'Free-Work',
                'nickname' => 'User-Registration-3-days-ago',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subDays(3),
                'updatedAt' => $now->copy()->subDays(3)->addMinutes(3),
                'data' => [
                    'lastForumActivityAt' => $now->copy()->subDays(3)->addMinutes(3),
                    'lastActivityAt' => $now->copy()->subDays(3)->addMinutes(3),
                    'cronProfileUncompletedExecAt' => $now->copy()->subDays(3),
                ],
            ],
            [
                'email' => 'user-immediate-availability-15-days-status@free-work.fr',
                'firstName' => 'User Immediate Avaibility 15 days status',
                'lastName' => 'Free-Work',
                'nickname' => 'User-Immediate-Availability-15-days-status',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subDays(20),
                'updatedAt' => $now->copy()->subDays(3)->addMinutes(15),
                'statusUpdatedAt' => $now->copy()->subDays(15),
                'data' => [
                    'lastForumActivityAt' => $now->copy()->subDays(20)->addMinutes(3),
                    'lastActivityAt' => $now->copy()->subDays(20)->addMinutes(3),
                    'cronProfileUncompletedExecAt' => $now->copy()->subDays(),
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subDays(),
                ],
                'availability' => Availability::IMMEDIATE,
            ],
            [
                'email' => 'user-no-immediate-availability-14-days@free-work.fr',
                'firstName' => 'User No Immediate Avaibility 14 days',
                'lastName' => 'Free-Work',
                'nickname' => 'User-No-Immediate-Availability-14-days',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subDays(20),
                'updatedAt' => $now->copy()->subDays(3)->addMinutes(15),
                'statusUpdatedAt' => $now->copy()->subDays(15),
                'availability' => Availability::WITHIN_1_MONTH,
                'nextAvailabilityAt' => $now->copy()->addDays(14),
                'data' => [
                    'cronProfileUncompletedExecAt' => $now->copy()->subDays(),
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subDays(),
                ],
            ],
            [
                'email' => 'user-no-availability-45-days-status@free-work.fr',
                'firstName' => 'User No  Availability 45 Days Status',
                'lastName' => 'Free-Work',
                'nickname' => 'User-No-Availability-45-days-status',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subDays(60),
                'updatedAt' => $now->copy()->subDays(60),
                'statusUpdatedAt' => $now->copy()->subDays(45),
                'availability' => Availability::NONE,
                'data' => [
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subDays(50),
                ],
            ],
            [
                'email' => 'user-no-availability-75-days-status@free-work.fr',
                'firstName' => 'User No  Availability 75 Days Status',
                'lastName' => 'Free-Work',
                'nickname' => 'User-No-Availability-75-days-status',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subDays(60),
                'updatedAt' => $now->copy()->subDays(60),
                'statusUpdatedAt' => $now->copy()->subDays(75),
                'availability' => Availability::NONE,
                'data' => [
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subDays(50),
                ],
            ],
            [
                'email' => 'user-immediate-availability-last-activity-3-months-updated-4-months@free-work.fr',
                'firstName' => 'User Immediate Availability Last Activity 3 Months Updated 4 Months',
                'lastName' => 'Free-Work',
                'nickname' => 'user-immediate-availability-last-activity-3-months-updated-4-months',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subMonths(5),
                'updatedAt' => $now->copy()->subMonths(4),
                'data' => [
                    'lastActivityAt' => $now->copy()->subMonths(3),
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subMonths(3),
                ],
                'availability' => Availability::IMMEDIATE,
            ],
            [
                'email' => 'user-immediate-availability-last-activity-3-months-updated-3-months@free-work.fr',
                'firstName' => 'User Immediate Availability Last Activity 3 Months Updated 3 Months',
                'lastName' => 'Free-Work',
                'nickname' => 'user-immediate-availability-last-activity-3-months-updated-3-months',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subMonths(5),
                'updatedAt' => $now->copy()->subMonths(4)->subHours(80),
                'data' => [
                    'lastActivityAt' => $now->copy()->subMonths(3)->subHours(72),
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subMonths(3),
                ],
                'availability' => Availability::IMMEDIATE,
            ],
            [
                'email' => 'user-non-visible-status-3-months@free-work.fr',
                'firstName' => 'User Non Visible Status 3 Months',
                'lastName' => 'Free-Work',
                'nickname' => 'user-non-visible-status-3-months',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subMonths(4),
                'updatedAt' => $now->copy()->subMonths(4),
                'statusUpdatedAt' => $now->copy()->subMonths(3),
                'visible' => false,
                'data' => [
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subMonths(3),
                ],
            ],
            [
                'email' => 'user-non-visible-status-6-months@free-work.fr',
                'firstName' => 'User Non Visible Status 6 Months',
                'lastName' => 'Free-Work',
                'nickname' => 'user-non-visible-status-6-months',
                'password' => 'e3faba08dfe8a9d37f17ae9b516150a1b911083a32c2e6023b90e1231f945faac57387dc1adfab69731269fb3e7cb574abe79a53aafcfc92c325bb0a991d3c0da6ed662a0c4f7b8c4b55e',
                'createdAt' => $now->copy()->subMonths(4),
                'updatedAt' => $now->copy()->subMonths(4),
                'statusUpdatedAt' => $now->copy()->subMonths(3),
                'visible' => false,
                'data' => [
                    'cronNoJobPostingSearchExecAt' => $now->copy()->subMonths(3),
                ],
            ],
        ];
    }

    private function fakeUniqueName(): array
    {
        do {
            $name = [
                'firstName' => $this->faker->firstName(),
                'lastName' => $this->faker->lastName(),
            ];
            $key = json_encode($name);
        } while (\in_array($key, $this->names, true));

        $this->names[] = $key;

        return $name;
    }

    public static function getGroups(): array
    {
        return ['user'];
    }

    public function getDependencies(): array
    {
        return [
            UmbrellaCompaniesFixtures::class,
            InsuranceCompaniesFixtures::class,
            SoftSkillsFixtures::class,
            JobFixtures::class,
            PartnerFixtures::class,
        ];
    }
}
