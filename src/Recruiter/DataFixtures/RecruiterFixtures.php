<?php

namespace App\Recruiter\DataFixtures;

use App\Company\DataFixtures\CompaniesFixtures;
use App\Company\DataFixtures\SitesFixtures;
use App\Company\Entity\Company;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Enum\Gender;
use App\Core\Util\TokenGenerator;
use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Entity\RecruiterAccessToken;
use App\Recruiter\Entity\RecruiterNotification;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use libphonenumber\PhoneNumber;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RecruiterFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $hasher;
    private array $companies;

    public function __construct(string $env, UserPasswordHasherInterface $hasher)
    {
        parent::__construct($env);
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // fetch companies
        $this->companies = $manager->getRepository(Company::class)->findSome();

        $defaults = [
            'password' => $this->hasher->hashPassword(new User(), 'P@ssw0rd'),
        ];

        // process data
        foreach ($this->getData() as $dataRecruiter) {
            $recruiter = (new Recruiter())
                ->setEmail($dataRecruiter['email'])
                ->setUsername($dataRecruiter['username'])
                ->setFirstName($dataRecruiter['firstName'])
                ->setLastName($dataRecruiter['lastName'])
                ->setPassword($dataRecruiter['password'] ?? $defaults['password'])
                ->setCompany($dataRecruiter['company'])
                ->setMain(true)
                ->setEnabled($dataRecruiter['enabled'] ?? false)
                ->setPhoneNumber($dataRecruiter['phoneNumber'] ?? null)
                ->setTermsOfService($dataRecruiter['termsOfService'] ?? false)
                ->setTermsOfServiceAcceptedAt($dataRecruiter['termsOfServiceAcceptedAt'] ?? null)
                ->setJob($dataRecruiter['job'])
                ->setConfirmationToken($dataRecruiter['confirmationToken'] ?? null)
                ->setGender($dataRecruiter['gender'] ?? null)
                ->setEmailRequestedAt($dataRecruiter['emailRequestedAt'] ?? null)
                ->setPasswordRequestedAt($dataRecruiter['passwordRequestedAt'] ?? null)
                ->setCreatedAt($dataRecruiter['createdAt'] ?? Carbon::now())
                ->setUpdatedAt($dataRecruiter['createdAt'] ?? Carbon::now())
                ->setPasswordUpdateRequired($dataRecruiter['passwordUpdateRequired'] ?? false)
            ;

            if (null !== ($dataRecruiter['notification'] ?? null)) {
                $notification = (new RecruiterNotification())
                    ->setNewApplicationEmail($dataRecruiter['notification']['newApplicationEmail'])
                    ->setNewApplicationNotification($dataRecruiter['notification']['newApplicationNotification'])
                    ->setEndBroadcastJobPostingEmail($dataRecruiter['notification']['endBroadcastJobPostingEmail'])
                    ->setEndBroadcastJobPostingNotification($dataRecruiter['notification']['endBroadcastJobPostingNotification'])
                    ->setDailyResumeEmail($dataRecruiter['notification']['dailyResumeEmail'])
                    ->setDailyJobPostingEmail($dataRecruiter['notification']['dailyJobPostingEmail'])
                    ->setJobPostingPublishATSEmail($dataRecruiter['notification']['jobPostingPublishATSEmail'])
                    ->setJobPostingPublishATSNotification($dataRecruiter['notification']['jobPostingPublishATSNotification'])
                    ->setNewsletterEmail($dataRecruiter['notification']['newsletterEmail'])
                    ->setSubscriptionEndEmail($dataRecruiter['notification']['subscriptionEndEmail'])
                    ->setSubscriptionEndNotification($dataRecruiter['notification']['subscriptionEndNotification'])
                    ->setInvoiceEmail($dataRecruiter['notification']['invoiceEmail'])
                    ->setInvoiceNotification($dataRecruiter['notification']['invoiceNotification'])
                    ->setOrderEmail($dataRecruiter['notification']['orderEmail'])
                    ->setOrderNotification($dataRecruiter['notification']['orderNotification'])
                    ->setSubscriptionEndReminderEmail($dataRecruiter['notification']['subscriptionEndReminderEmail'])
                    ->setSubscriptionEndReminderNotification($dataRecruiter['notification']['subscriptionEndReminderNotification'])
                ;
                $recruiter->setNotification($notification);
            }

            $manager->persist($recruiter);

            $accessToken = (new RecruiterAccessToken())
                ->setRecruiter($recruiter)
                ->setPlainValue(TokenGenerator::generateFromValue($dataRecruiter['email'], 32))
                ->setExpiredAt(new \DateTime('2030-01-01 00:00:00'))
            ;
            $manager->persist($accessToken);

            foreach ($dataRecruiter['recruiters'] ?? [] as $dataSubRecruiter) {
                $subRecruiter = (new Recruiter())
                    ->setEmail($dataSubRecruiter['email'])
                    ->setUsername($dataSubRecruiter['username'])
                    ->setFirstName($dataSubRecruiter['firstName'])
                    ->setLastName($dataSubRecruiter['lastName'])
                    ->setPassword($dataSubRecruiter['password'] ?? $defaults['password'])
                    ->setCompany($dataRecruiter['company'])
                    ->setSite($dataSubRecruiter['site'] ?? null)
                    ->setMain(false)
                    ->setEnabled($dataSubRecruiter['enabled'] ?? false)
                    ->setCreatedBy($recruiter)
                    ->setPhoneNumber($dataSubRecruiter['phoneNumber'] ?? null)
                    ->setTermsOfService($dataSubRecruiter['termsOfService'] ?? false)
                    ->setTermsOfServiceAcceptedAt($dataSubRecruiter['termsOfServiceAcceptedAt'] ?? null)
                    ->setJob($dataSubRecruiter['job'])
                    ->setConfirmationToken($dataSubRecruiter['confirmationToken'] ?? null)
                    ->setGender($dataSubRecruiter['gender'] ?? null)
                    ->setEmailRequestedAt($dataSubRecruiter['emailRequestedAt'] ?? null)
                    ->setPasswordRequestedAt($dataSubRecruiter['passwordRequestedAt'] ?? null)
                    ->setCreatedAt($dataSubRecruiter['createdAt'] ?? Carbon::now())
                    ->setUpdatedAt($dataSubRecruiter['createdAt'] ?? Carbon::now())
                    ->setPasswordUpdateRequired($dataSubRecruiter['passwordUpdateRequired'] ?? false)
                ;
                $manager->persist($subRecruiter);

                $accessToken = (new RecruiterAccessToken())
                    ->setRecruiter($subRecruiter)
                    ->setPlainValue(TokenGenerator::generateFromValue($dataSubRecruiter['email'], 32))
                    ->setExpiredAt(new \DateTime('2030-01-01 00:00:00'))
                ;
                $manager->persist($accessToken);
            }
        }

        $manager->flush();
    }

    public function getData(): array
    {
        return [
            [
                'firstName' => 'Walter',
                'lastName' => 'White',
                'email' => 'walter.white@breaking-bad.com',
                'username' => 'walter.white',
                'enabled' => true,
                'phoneNumber' => (new PhoneNumber())->setRawInput('+330612345678'),
                'company' => $this->companies[0],
                'termsOfService' => true,
                'termsOfServiceAcceptedAt' => new \DateTime('2022-01-01 12:00:00'),
                'job' => 'CTO',
                'gender' => Gender::MALE,
                'passwordUpdateRequired' => true,
                'recruiters' => [
                    [
                        'firstName' => 'Jesse',
                        'lastName' => 'Pinkman',
                        'email' => 'jesse.pinkman@breaking-bad.com',
                        'username' => 'jesse.pinkman',
                        'enabled' => true,
                        'gender' => Gender::MALE,
                        'phoneNumber' => (new PhoneNumber())->setRawInput('+330687654321'),
                        'job' => 'Lead développeur',
                        'site' => $this->companies[0]->getSites()->first(),
                    ],
                    [
                        'firstName' => 'Gustavo',
                        'lastName' => 'Fring',
                        'email' => 'gustavo.fring@breaking-bad.com',
                        'username' => 'gustavo.fring',
                        'termsOfService' => true,
                        'termsOfServiceAcceptedAt' => new \DateTime('2022-01-01 12:00:00'),
                        'job' => 'Développeur',
                        'site' => $this->companies[0]->getSites()->first(),
                    ],
                ],
                'notification' => [
                    'newApplicationEmail' => true,
                    'newApplicationNotification' => true,
                    'endBroadcastJobPostingEmail' => true,
                    'endBroadcastJobPostingNotification' => true,
                    'dailyResumeEmail' => true,
                    'dailyJobPostingEmail' => true,
                    'jobPostingPublishATSEmail' => true,
                    'jobPostingPublishATSNotification' => true,
                    'newsletterEmail' => true,
                    'subscriptionEndEmail' => true,
                    'subscriptionEndNotification' => true,
                    'invoiceEmail' => true,
                    'invoiceNotification' => true,
                    'orderEmail' => true,
                    'orderNotification' => true,
                    'subscriptionEndReminderEmail' => true,
                    'subscriptionEndReminderNotification' => true,
                ],
            ],
            [
                'firstName' => 'Eddard',
                'lastName' => 'Stark',
                'email' => 'eddard.stark@got.com',
                'username' => 'eddard.stark',
                'enabled' => true,
                'company' => $this->companies[1],
                'termsOfService' => true,
                'termsOfServiceAcceptedAt' => new \DateTime('2022-01-01 12:00:00'),
                'job' => 'CEO',
                'recruiters' => [
                    [
                        'firstName' => 'Robb',
                        'lastName' => 'Stark',
                        'email' => 'robb.stark@got.com',
                        'username' => 'robb.stark',
                        'enabled' => true,
                        'job' => 'CTO',
                        'site' => $this->companies[1]->getSites()->first(),
                    ],
                    [
                        'firstName' => 'Sansa',
                        'lastName' => 'Stark',
                        'email' => 'sansa.stark@got.com',
                        'username' => 'sansa.stark',
                        'enabled' => true,
                        'job' => 'Lead développeur',
                        'gender' => Gender::FEMALE,
                        'site' => $this->companies[1]->getSites()->first(),
                    ],
                    [
                        'firstName' => 'Arya',
                        'lastName' => 'Stark',
                        'email' => 'arya.stark@got.com',
                        'username' => 'arya.stark',
                        'enabled' => true,
                        'job' => 'Développeur',
                        'site' => $this->companies[1]->getSites()->first(),
                    ],
                    [
                        'firstName' => 'John',
                        'lastName' => 'Snow',
                        'email' => 'john.snow@got.com',
                        'username' => 'john.snow',
                        'job' => 'Développeur',
                        'site' => $this->companies[1]->getSites()->first(),
                    ],
                ],
                'notification' => [
                    'newApplicationEmail' => true,
                    'newApplicationNotification' => true,
                    'endBroadcastJobPostingEmail' => true,
                    'endBroadcastJobPostingNotification' => true,
                    'dailyResumeEmail' => true,
                    'dailyJobPostingEmail' => true,
                    'jobPostingPublishATSEmail' => true,
                    'jobPostingPublishATSNotification' => true,
                    'newsletterEmail' => true,
                    'subscriptionEndEmail' => true,
                    'subscriptionEndNotification' => true,
                    'invoiceEmail' => true,
                    'invoiceNotification' => true,
                    'orderEmail' => true,
                    'orderNotification' => true,
                    'subscriptionEndReminderEmail' => true,
                    'subscriptionEndReminderNotification' => true,
                ],
            ],
            [
                'firstName' => 'Robert',
                'lastName' => 'Ford',
                'email' => 'robert.ford@ww.com',
                'username' => 'robert.ford',
                'enabled' => true,
                'company' => $this->companies[2],
                'job' => 'CTO',
                'recruiters' => [
                    [
                        'firstName' => 'Dolores',
                        'lastName' => 'Abernathy',
                        'email' => 'dolores.abernathy@ww.com',
                        'username' => 'dolores.abernathy',
                        'enabled' => true,
                        'job' => 'Développeur Front-End',
                        'site' => $this->companies[2]->getSites()->first(),
                    ],
                    [
                        'firstName' => 'Maeve',
                        'lastName' => 'Millay',
                        'email' => 'maeve.millay@ww.com',
                        'username' => 'maeve.millay',
                        'enabled' => true,
                        'job' => 'Développeur Back-End',
                        'site' => $this->companies[2]->getSites()->first(),
                    ],
                    [
                        'firstName' => 'Bernard',
                        'lastName' => 'Lowe',
                        'email' => 'bernard.lowe@ww.com',
                        'username' => 'lowe',
                        'job' => 'Lead developer',
                        'confirmationToken' => 'forgotten-password-expired-token',
                        'passwordRequestedAt' => Carbon::now()->subHours(48),
                        'createdAt' => new \DateTime('2022-01-01 00:00:00'),
                    ],
                    [
                        'firstName' => 'Teddy',
                        'lastName' => 'Flood',
                        'email' => 'teddy.flood@ww.com',
                        'username' => 'tflood',
                        'job' => 'Cow-boy',
                        'confirmationToken' => 'forgotten-password-valid-token',
                        'passwordRequestedAt' => Carbon::now()->subHours(3),
                        'createdAt' => new \DateTime('2022-01-01 00:00:00'),
                    ],
                ],
                'notification' => [
                    'newApplicationEmail' => false,
                    'newApplicationNotification' => false,
                    'endBroadcastJobPostingEmail' => false,
                    'endBroadcastJobPostingNotification' => false,
                    'dailyResumeEmail' => false,
                    'dailyJobPostingEmail' => false,
                    'jobPostingPublishATSEmail' => false,
                    'jobPostingPublishATSNotification' => false,
                    'newsletterEmail' => false,
                    'subscriptionEndEmail' => false,
                    'subscriptionEndNotification' => false,
                    'invoiceEmail' => false,
                    'invoiceNotification' => false,
                    'orderEmail' => false,
                    'orderNotification' => false,
                    'subscriptionEndReminderEmail' => false,
                    'subscriptionEndReminderNotification' => false,
                ],
            ],
            [
                'firstName' => 'Carrie',
                'lastName' => 'Mathison',
                'email' => 'carrie.mathison@homeland.com',
                'username' => 'carrie.mathison',
                'company' => $this->companies[3],
                'job' => 'CIA secret agent',
                'confirmationToken' => 'carrie-mathison-token',
                'createdAt' => new \DateTime('2021-01-01 00:00:00'),
                'recruiters' => [
                    [
                        'firstName' => 'Peter',
                        'lastName' => 'Quinn',
                        'email' => 'peter.quinn@homeland.com',
                        'username' => 'peter.quinn',
                        'job' => 'Stagiaire',
                        'confirmationToken' => 'peter-quinn-token',
                        'createdAt' => Carbon::now()->modify('- 6 hours'),
                        'site' => $this->companies[3]->getSites()->first(),
                    ],
                ],
            ],
            [
                'firstName' => 'Guillaume',
                'lastName' => 'Debailly',
                'email' => 'guillaume.debailly@le-bureau-des-legendes.fr',
                'username' => 'malotru',
                'company' => $this->companies[4],
                'job' => 'DGSE',
                'confirmationToken' => TokenGenerator::generateFromValue('g.debailly@le-bureau-des-legendes.fr', 20),
                'emailRequestedAt' => Carbon::now()->subHours(3),
                'createdAt' => new \DateTime('2021-01-01 00:00:00'),
                'recruiters' => [
                    [
                        'firstName' => 'Henri',
                        'lastName' => 'Duflot',
                        'email' => 'henri.duflot@le-bureau-des-legendes.fr',
                        'username' => 'socrate',
                        'job' => 'DGSE',
                        'confirmationToken' => TokenGenerator::generateFromValue('hduflot@le-bureau-des-legendes.fr', 20),
                        'emailRequestedAt' => Carbon::now()->subHours(48),
                        'createdAt' => new \DateTime('2021-01-01 00:00:00'),
                    ],
                ],
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            CompaniesFixtures::class,
            SitesFixtures::class,
        ];
    }
}
