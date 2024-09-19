<?php

namespace App\Company\DataFixtures;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyBusinessActivity;
use App\Company\Entity\CompanyFeaturesUsage;
use App\Company\Entity\CompanyPicture;
use App\Company\Enum\CompanySize;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\SkillsFixtures;
use App\Core\DataFixtures\SoftSkillsFixtures;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Entity\SoftSkill;
use App\Core\Util\Arrays;
use App\Core\Util\Files;
use App\Core\Util\Strings;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Gaufrette\Filesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CompaniesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private Filesystem $filesystem;
    private array $skills = [];
    private array $activities = [];
    private array $softSkills = [];
    private array $featuresUsageData = [];
    private DenormalizerInterface $denormalizer;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(string $env, FilesystemMap $filesystemMap, DenormalizerInterface $denormalizer, PropertyAccessorInterface $propertyAccessor)
    {
        parent::__construct($env);
        $this->filesystem = $filesystemMap->get('blog_post_image_fs');
        $this->denormalizer = $denormalizer;
        $this->propertyAccessor = $propertyAccessor;
        $this->featuresUsageData = [
            'searchDisplayArrayAt' => new \DateTime('2022-01-01 12:00:00'),
            'searchDisplayListAt' => new \DateTime('2022-01-02 12:00:00'),
            'searchBooleanAt' => new \DateTime('2021-01-03 12:00:00'),
            'searchQueryAt' => new \DateTime('2021-01-04 12:00:00'),
            'searchJobAt' => new \DateTime('2021-01-05 12:00:00'),
            'searchLocationAt' => new \DateTime('2021-01-06 12:00:00'),
            'searchFolderAt' => new \DateTime('2021-01-07 12:00:00'),
            'searchOrderAt' => new \DateTime('2021-01-08 12:00:00'),
            'searchAvailabilityAndLanguageAt' => new \DateTime('2021-01-09 12:00:00'),
            'userCartAt' => new \DateTime('2021-01-10 12:00:00'),
            'userFavoriteAt' => new \DateTime('2021-01-11 12:00:00'),
            'userHideAt' => new \DateTime('2021-01-12 12:00:00'),
            'userDownloadResumeAt' => new \DateTime('2021-01-13 12:00:00'),
            'userCommentAt' => new \DateTime('2021-01-14 12:00:00'),
            'userFolderAt' => new \DateTime('2021-01-15 12:00:00'),
            'userJobPostingAt' => new \DateTime('2021-01-16 12:00:00'),
            'userEmailTransferAt' => new \DateTime('2021-01-17 12:00:00'),
            'userEmailSendAt' => new \DateTime('2021-01-18 12:00:00'),
            'userMultipleFolderAt' => new \DateTime('2021-01-19 12:00:00'),
            'userMultipleExportAt' => new \DateTime('2021-01-20 12:00:00'),
            'userAlertAt' => new \DateTime('2021-01-21 12:00:00'),
            'jobPostingFreeWorkAt' => new \DateTime('2021-01-22 12:00:00'),
            'jobPostingTurnoverAt' => new \DateTime('2021-01-23 12:00:00'),
            'jobPostingPublicAt' => new \DateTime('2021-01-24 12:00:00'),
            'jobPostingInternalAt' => new \DateTime('2021-01-25 12:00:00'),
            'intercontractSearchByCompanyAt' => new \DateTime('2021-01-26 12:00:00'),
            'intercontractPublishAt' => new \DateTime('2021-01-27 12:00:00'),
            'intercontractOnlyAt' => new \DateTime('2021-01-28 12:00:00'),
            'companyPublishAt' => new \DateTime('2021-01-29 12:00:00'),
            'companyLogAt' => new \DateTime('2021-01-30 12:00:00'),
            'exportJobPostingPublishAt' => new \DateTime('2021-01-31 12:00:00'),
            'exportUserLogAndDownloadAt' => new \DateTime('2021-02-01 12:00:00'),
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // fetch skills
        foreach ($manager->getRepository(Skill::class)->findAll() as $skill) {
            /* @var Skill $skill */
            $this->skills[$skill->getSlug()] = $skill;
        }

        // fetch activities
        foreach ($manager->getRepository(CompanyBusinessActivity::class)->findAll() as $activity) {
            /* @var CompanyBusinessActivity $activity */
            $this->activities[$activity->getId()] = $activity;
        }

        // fetch softSkills
        foreach ($manager->getRepository(SoftSkill::class)->findAll() as $softSkill) {
            /* @var SoftSkill $softSkill */
            $this->softSkills[$softSkill->getName()] = $softSkill;
        }

        foreach ($this->getData() as $d) {
            $company = (new Company())
                ->setName(ucwords($d['name']))
                ->setExcerpt($d['excerpt'] ?? null)
                ->setDescription($d['description'])
                ->setAnnualRevenue($d['annualRevenue'])
                ->setBusinessActivity($d['businessActivity'])
                ->setSize($d['size'])
                ->setWebsiteUrl($d['websiteUrl'])
                ->setLinkedInUrl($d['linkedInUrl'])
                ->setFacebookUrl($d['facebookUrl'])
                ->setTwitterUrl($d['twitterUrl'])
                ->setCreationYear($d['creationYear'])
                ->setLocation($this->denormalizer->denormalize($d['location'], Location::class))
                ->setCreatedAt($d['createdAt'] ?? null)
                ->setUpdatedAt($d['updatedAt'] ?? null)
                ->setOldId($d['oldId'] ?? null)
                ->setDirectoryFreeWork($d['directoryFreeWork'] ?? false)
                ->setCreatedAt($d['createdAt'] ?? false)
                ->setDirectoryTurnover($d['directoryTurnover'] ?? false)
                ->setLegalName($d['name'])
                ->setBaseline($d['baseline'] ?? null)
                ->setIntracommunityVat($d['intracommunityVat'] ?? null)
                ->setBillingEmail($d['billingEmail'] ?? null)
                ->setBillingAddress($this->denormalizer->denormalize($d['billingAddress'], Location::class))
            ;

            // skills
            foreach ($d['skills'] ?? [] as $skill) {
                $company->addSkill($skill);
            }

            // soft skills
            foreach ($d['softSkills'] ?? [] as $softSkill) {
                $company->addSoftSkill($softSkill);
            }

            // logo
            if (null !== ($d['logoFile'] ?? null)) {
                $company->setLogoFile(Files::getUploadedFile($d['logoFile']));
            } elseif (null !== ($d['logo'] ?? null)) {
                if (false === $logoContent = file_get_contents($d['logo']['path'])) {
                    continue;
                }
                $this->filesystem->write($d['logo']['basename'], $logoContent, true);
                $company->setLogo($d['logo']['basename']);
            }

            // coverPicture
            if (null !== ($d['coverPictureFile'] ?? null)) {
                $company->setCoverPictureFile(Files::getUploadedFile($d['coverPictureFile']));
            } elseif (null !== ($d['coverPicture'] ?? null)) {
                if (false === $coverPictureContent = file_get_contents($d['coverPicture']['path'])) {
                    continue;
                }
                $this->filesystem->write($d['coverPicture']['basename'], $coverPictureContent, true);
                $company->setCoverPicture($d['coverPicture']['basename']);
            }

            // video
            if (null !== ($d['videoFile'] ?? null)) {
                $company->setVideoFile(Files::getUploadedFile($d['videoFile']));
            } elseif (null !== ($d['video'] ?? null)) {
                if (false === $videoContent = file_get_contents($d['video']['path'])) {
                    continue;
                }
                $this->filesystem->write($d['video']['basename'], $videoContent, true);
                $company->setVideo($d['video']['basename']);
            }

            // pictures
            $i = 0;
            foreach (($d['pictures'] ?? []) as $dPicture) {
                $picture = new CompanyPicture();
                if (null !== ($dPicture['imageFile'] ?? null)) {
                    $picture->setImageFile(Files::getUploadedFile($dPicture['imageFile']));
                } elseif (null !== ($dPicture['image'] ?? null)) {
                    if (false === $imageContent = file_get_contents($dPicture['image']['path'])) {
                        continue;
                    }
                    $this->filesystem->write($dPicture['image']['basename'], $imageContent, true);
                    $picture->setImage($dPicture['image']['basename']);
                }
                $picture->setPosition($i);
                ++$i;
                $company->addPicture($picture);
            }

            // companyFeatureUsage
            if (\array_key_exists('featuresUsage', $d)) {
                $companyFeatureUsage = new CompanyFeaturesUsage();
                foreach ($d['featuresUsage'] as $field => $value) {
                    $this->propertyAccessor->setValue($companyFeatureUsage, $field, $value);
                }
                $company->setFeaturesUsage($companyFeatureUsage);
            }

            $manager->persist($company);
        }

        $manager->flush();
        $manager->clear();
    }

    public function getDevData(): array
    {
        $data = [];
        $faker = Faker::create('fr_FR');
        if (($handle = fopen(__DIR__ . '/files/companies.csv', 'r')) !== false) {
            $i = 0;
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                $d = array_map(static function (string $e) {
                    return empty($e) ? null : $e;
                }, $d);

                $d = [
                    'businessActivity' => $this->activities[$d[0]] ?? null,
                    'name' => $d[1],
                    'description' => $d[2],
                    'annualRevenue' => $d[3],
                    'size' => $d[4],
                    'websiteUrl' => $d[5],
                    'linkedInUrl' => $d[6],
                    'facebookUrl' => $d[7],
                    'twitterUrl' => $d[8],
                    'creationYear' => true === ctype_digit($d[9]) ? (int) ($d[9]) : null,
                    'directoryFreeWork' => '1' === $d[10],
                    'oldId' => $d[11],
                    'location' => [
                        'latitude' => $d[12],
                        'longitude' => $d[13],
                        'street' => implode(' ', array_filter([$d[14], $d[15]])),
                        'subLocality' => $d[16],
                        'locality' => $d[17],
                        'localitySlug' => $d[18],
                        'postalCode' => $d[19],
                        'adminLevel1' => $d[20],
                        'adminLevel1Slug' => $d[21],
                        'adminLevel2' => $d[22],
                        'adminLevel2Slug' => $d[23],
                        'country' => $d[24],
                        'countryCode' => $d[25],
                        'value' => $d[26],
                    ],
                    'createdAt' => null === $d[27] ? null : \DateTime::createFromFormat('Y-m-d H:i:s', $d[27]),
                    'skills' => empty($d[28]) ? [] : Arrays::subarray($this->skills, explode(',', $d[28])),
                    'directoryTurnover' => 0 === mt_rand(0, 1),
                    'legalName' => $d[1],
                    'baseline' => $d[2] ? Strings::substrToLength($d[2], 100) : null,
                    'softSkills' => Arrays::getRandomSubarray($this->softSkills, 3, 5),
                    'intracommunityVat' => $faker->numberBetween(100000000, 999999999),
                    'billingEmail' => $faker->email,
                    'billingAddress' => [
                        'street' => implode(' ', array_filter([$d[14], $d[15]])),
                        'locality' => $d[17],
                        'localitySlug' => $d[18],
                        'postalCode' => $d[19],
                        'country' => $d[24],
                        'countryCode' => $d[25],
                    ],
                ];

                // logo
                try {
                    $finder = new Finder();
                    $finder->in('src/Company/DataFixtures/files/companies')->name(sprintf('%d-logo*', $d['oldId']))->sortByName();
                    foreach ($finder->files() as $file) {
                        $d['logoFile'] = $file->getPathname();
                        break;
                    }
                } catch (DirectoryNotFoundException $e) {
                    $d['logo'] = null;
                }

                // coverPicture
                try {
                    $finder = new Finder();
                    $finder->in('src/Company/DataFixtures/files/companies')->name(sprintf('%d-picture-*', $d['oldId']))->sortByName();
                    foreach ($finder->files() as $file) {
                        $d['coverPictureFile'] = $file->getPathname();
                        break;
                    }
                } catch (DirectoryNotFoundException $e) {
                    $d['coverPictureFile'] = null;
                }

                // pictures
                try {
                    $finder = new Finder();
                    $finder->in('src/Company/DataFixtures/files/companies')->name(sprintf('%d-picture-*', $d['oldId']))->sortByName();
                    foreach ($finder->files() as $file) {
                        $d['pictures'][] = [
                            'imageFile' => $file->getPathname(),
                        ];
                    }
                } catch (DirectoryNotFoundException $e) {
                    $d['pictures'] = [];
                }

                // video
                // featuresUsages
                if (0 === $i) {
                    $d['video'] = [
                        'path' => __DIR__ . '/files/companies/video.mp4',
                        'basename' => 'video.mp4',
                    ];

                    $d['featuresUsage'] = $this->featuresUsageData;
                }

                $data[] = $d;
                ++$i;
            }
            fclose($handle);
        }

        return $data;
    }

    public function getTestData(): array
    {
        $locations = array_map(static function (string $location) {
            return json_decode($location, true);
        }, [
            'lyon' => '{"subLocality":null,"locality":"Lyon","localitySlug":"lyon","postalCode":null,"adminLevel1":"Auvergne-Rhône-Alpes","adminLevel1Slug":"auvergne-rhone-alpes","adminLevel2":"Métropole de Lyon","adminLevel2Slug":"metropole-de-lyon","country":"France","countryCode":"FR","latitude":"45.7578137","longitude":"4.8320114"}',
            'paris' => '{"subLocality":"Paris","locality":"Paris","localitySlug":"paris","postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.8588897","longitude":"2.3200410217201"}',
            'idf' => '{"subLocality":null,"locality":null,"localitySlug":null,"postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.6443057","longitude":"2.7537863"}',
            'toulouse' => '{"locality":"Toulouse","postalCode":"31000","adminLevel1":"Occitanie","adminLevel2":"Haute-Garonne","country":"France","countryCode":"FR","latitude":"43.6044622","longitude":"1.4442469","key":"fr~occitanie~haute-garonne~toulouse","label":"Toulouse, Occitanie","shortLabel":"Toulouse (31)"}',
        ]);

        $billingAddresses = array_map(static function (string $billingAddress) {
            return json_decode($billingAddress, true);
        }, [
            'lyon' => '{"street":"14 Quai du Général Sarrail","locality":"Lyon","localitySlug":"lyon","postalCode":"69006","country":"France","countryCode":"FR"}',
            'paris' => '{"street":"8 Avenue Foch","locality":"Paris","localitySlug":"paris","postalCode":"75006","country":"France","countryCode":"FR"}',
            'toulouse' => '{"street":"4 Place du Capitole","locality":"Toulouse","localitySlug":"toulouse","postalCode":"31000","country":"France","countryCode":"FR"}',
        ]);

        return [
            [
                'name' => 'Company 1',
                'excerpt' => 'Company 1 // Excerpt',
                'description' => 'Company 1 // Description',
                'annualRevenue' => '100k',
                'businessActivity' => $this->activities['1'],
                'size' => CompanySize::LESS_THAN_20_EMPLOYEES,
                'websiteUrl' => 'https://www.company-1.com',
                'linkedInUrl' => 'https://www.linkedin.com/company-1',
                'twitterUrl' => 'https://www.twitter.com/company-1',
                'facebookUrl' => 'https://www.facebook.com/company-1',
                'creationYear' => 1904,
                'directoryFreeWork' => true,
                'location' => $locations['paris'],
                'billingAddress' => $billingAddresses['paris'],
                'logo' => [
                    'path' => __DIR__ . '/files/companies/test/company-logo-1.png',
                    'basename' => 'company-1-logo.jpg',
                ],
                'coverPicture' => [
                    'path' => __DIR__ . '/files/companies/test/company-picture-1-1.jpg',
                    'basename' => 'company-1-picture-1.jpg',
                ],
                'pictures' => [
                    [
                        'image' => [
                            'path' => __DIR__ . '/files/companies/test/company-picture-1-2.jpg',
                            'basename' => 'company-1-picture-2.jpg',
                        ],
                    ],
                    [
                        'image' => [
                            'path' => __DIR__ . '/files/companies/test/company-picture-1-3.jpg',
                            'basename' => 'company-1-picture-3.jpg',
                        ],
                    ],
                ],
                'createdAt' => new \DateTime('2021-01-01 12:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 12:00:00'),
                'directoryTurnover' => true,
                'legalName' => 'Company 1 // Legal Name',
                'baseline' => 'Company 1 // Baseline',
                'softSkills' => [
                    $this->softSkills['SoftSkill 1'],
                    $this->softSkills['SoftSkill 2'],
                ],
                'intracommunityVat' => '123456789',
                'billingEmail' => 'company-1@free-work.fr',
                'video' => [
                    'path' => __DIR__ . '/files/companies/video.mp4',
                    'basename' => 'video.mp4',
                ],
                'featuresUsage' => $this->featuresUsageData,
            ],
            [
                'name' => 'Company 2',
                'excerpt' => 'Company 2 // Excerpt',
                'description' => 'Company 2 // Description',
                'websiteUrl' => null,
                'linkedInUrl' => 'https://www.linkedin.com/company-2',
                'twitterUrl' => 'https://www.twitter.com/company-2',
                'facebookUrl' => 'https://www.facebook.com/company-2',
                'annualRevenue' => '2.3M',
                'businessActivity' => $this->activities['1'],
                'size' => CompanySize::EMPLOYEES_20_99,
                'creationYear' => 2005,
                'location' => $locations['idf'],
                'billingAddress' => $billingAddresses['paris'],
                'skills' => Arrays::subarray($this->skills, ['php', 'javascript']),
                'logo' => [
                    'path' => __DIR__ . '/files/companies/test/company-logo-2.png',
                    'basename' => 'company-2-logo.jpg',
                ],
                'createdAt' => new \DateTime('2021-01-01 13:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 13:00:00'),
                'directoryTurnover' => false,
                'legalName' => 'Company 2 // Legal Name',
                'baseline' => 'Company 2 // Baseline',
                'softSkills' => [
                    $this->softSkills['SoftSkill 2'],
                    $this->softSkills['SoftSkill 4'],
                ],
                'intracommunityVat' => '13371337',
                'billingEmail' => 'company-2@free-work.fr',
            ],
            [
                'name' => 'Company 3',
                'excerpt' => 'Company 3 // Excerpt',
                'description' => 'Company 3 // Description',
                'websiteUrl' => 'https://www.company-3.com',
                'linkedInUrl' => null,
                'twitterUrl' => 'https://www.twitter.com/company-3',
                'facebookUrl' => 'https://www.facebook.com/company-3',
                'annualRevenue' => '33k',
                'businessActivity' => $this->activities['2'],
                'size' => CompanySize::LESS_THAN_20_EMPLOYEES,
                'creationYear' => 2018,
                'location' => $locations['lyon'],
                'billingAddress' => $billingAddresses['lyon'],
                'skills' => Arrays::subarray($this->skills, ['javascript']),
                'logo' => [
                    'path' => __DIR__ . '/files/companies/test/company-logo-3.png',
                    'basename' => 'company-3-logo.jpg',
                ],
                'createdAt' => new \DateTime('2021-01-01 14:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 14:00:00'),
                'directoryTurnover' => false,
                'legalName' => 'Company 3 // Legal Name',
                'baseline' => 'Company 3 // Baseline',
                'softSkills' => [
                    $this->softSkills['SoftSkill 3'],
                ],
                'intracommunityVat' => null,
                'billingEmail' => 'company-3@free-work.fr',
            ],
            [
                'name' => 'Company 4',
                'excerpt' => 'Company 4 // Excerpt',
                'description' => 'Company 4 // Description',
                'websiteUrl' => 'https://www.company-4.com',
                'linkedInUrl' => 'https://www.linkedin.com/company-4',
                'twitterUrl' => 'null',
                'facebookUrl' => 'https://www.facebook.com/company-4',
                'annualRevenue' => '122.8M',
                'businessActivity' => $this->activities['1'],
                'size' => CompanySize::LESS_THAN_20_EMPLOYEES,
                'creationYear' => 1982,
                'location' => $locations['idf'],
                'billingAddress' => null,
                'skills' => Arrays::subarray($this->skills, ['php', 'symfony', 'laravel']),
                'logo' => [
                    'path' => __DIR__ . '/files/companies/test/company-logo-4.png',
                    'basename' => 'company-4-logo.jpg',
                ],
                'createdAt' => new \DateTime('2021-01-01 15:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 15:00:00'),
                'directoryTurnover' => true,
                'legalName' => 'Company 4 // Legal Name',
                'baseline' => 'Company 4 // Baseline',
                'softSkills' => [
                    $this->softSkills['SoftSkill 1'],
                ],
                'intracommunityVat' => null,
                'billingEmail' => 'company-4@free-work.fr',
            ],
            [
                'name' => 'Company 5',
                'excerpt' => 'Company 5 // Excerpt',
                'description' => 'Company 5 // Description',
                'websiteUrl' => 'https://www.company-5.com',
                'linkedInUrl' => 'https://www.linkedin.com/company-5',
                'twitterUrl' => 'https://www.twitter.com/company-5',
                'facebookUrl' => null,
                'annualRevenue' => '1.8M',
                'businessActivity' => $this->activities['3'],
                'size' => CompanySize::MORE_THAN_1000_EMPLOYEES,
                'creationYear' => 1992,
                'location' => $locations['idf'],
                'billingAddress' => $billingAddresses['paris'],
                'skills' => Arrays::subarray($this->skills, ['php', 'symfony', 'laravel']),
                'logo' => [
                    'path' => __DIR__ . '/files/companies/test/company-logo-5.png',
                    'basename' => 'company-5-logo.jpg',
                ],
                'coverPicture' => [
                    'path' => __DIR__ . '/files/companies/test/company-picture-2-1.jpg',
                    'basename' => 'company-5-picture-1.jpg',
                ],
                'createdAt' => new \DateTime('2021-01-01 16:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 16:00:00'),
                'pictures' => [
                    [
                        'image' => [
                            'path' => __DIR__ . '/files/companies/test/company-picture-2-2.jpg',
                            'basename' => 'company-5-picture-2.jpg',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Company 6',
                'excerpt' => 'Company 6 // Excerpt',
                'description' => 'Company 6 // Description',
                'websiteUrl' => 'https://www.company-6.com',
                'linkedInUrl' => 'https://www.linkedin.com/company-6',
                'twitterUrl' => 'https://www.twitter.com/company-6',
                'facebookUrl' => null,
                'annualRevenue' => '1.8M',
                'businessActivity' => $this->activities['3'],
                'size' => CompanySize::MORE_THAN_1000_EMPLOYEES,
                'creationYear' => 1992,
                'location' => $locations['toulouse'],
                'billingAddress' => $billingAddresses['toulouse'],
                'skills' => Arrays::subarray($this->skills, ['php', 'symfony', 'laravel']),
                'logo' => [
                    'path' => __DIR__ . '/files/companies/test/company-logo-6.png',
                    'basename' => 'company-6-logo.jpg',
                ],
                'createdAt' => new \DateTime('2022-01-01 16:00:00'),
                'updatedAt' => new \DateTime('2022-01-01 16:00:00'),
                'coverPicture' => [
                    'path' => __DIR__ . '/files/companies/test/company-picture-2-1.jpg',
                    'basename' => 'company-6-picture-1.jpg',
                ],
                'pictures' => [
                    [
                        'image' => [
                            'path' => __DIR__ . '/files/companies/test/company-picture-2-2.jpg',
                            'basename' => 'company-6-picture-2.jpg',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Company 7',
                'excerpt' => 'Company 7 // Excerpt',
                'description' => 'Company 7 // Description',
                'websiteUrl' => 'https://www.company-7.com',
                'linkedInUrl' => 'https://www.linkedin.com/company-7',
                'twitterUrl' => 'https://www.twitter.com/company-7',
                'facebookUrl' => null,
                'annualRevenue' => '1.8M',
                'businessActivity' => $this->activities['3'],
                'size' => CompanySize::MORE_THAN_1000_EMPLOYEES,
                'creationYear' => 1992,
                'location' => $locations['toulouse'],
                'billingAddress' => null,
                'skills' => Arrays::subarray($this->skills, ['php', 'symfony', 'laravel']),
                'logo' => [
                    'path' => __DIR__ . '/files/companies/test/company-logo-7.png',
                    'basename' => 'company-7-logo.jpg',
                ],
                'createdAt' => new \DateTime('2022-02-01 16:00:00'),
                'updatedAt' => new \DateTime('2022-02-01 16:00:00'),
                'coverPicture' => [
                    'path' => __DIR__ . '/files/companies/test/company-picture-2-1.jpg',
                    'basename' => 'company-7-picture-1.jpg',
                ],
                'pictures' => [
                    [
                        'image' => [
                            'path' => __DIR__ . '/files/companies/test/company-picture-2-2.jpg',
                            'basename' => 'company-7-picture-2.jpg',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            SkillsFixtures::class,
            CompanyBusinessActivitiesFixtures::class,
            SoftSkillsFixtures::class,
        ];
    }
}
