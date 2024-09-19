<?php

namespace App\User\Hydrator;

use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Entity\SoftSkill;
use App\Core\Enum\Gender;
use App\Core\Manager\LocationManager;
use App\Core\Transformer\PhoneNumberTransformer;
use App\Core\Util\Strings;
use App\User\Entity\User;
use App\User\Entity\UserDocument;
use App\User\Entity\UserFormation;
use App\User\Entity\UserLanguage;
use App\User\Entity\UserSkill;
use App\User\Enum\ExperienceYear;
use App\User\Enum\Language;
use App\User\Enum\LanguageLevel;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use ForceUTF8\Encoding;
use Symfony\Component\String\Slugger\SluggerInterface;

class HrFlowParserToUserHydrator
{
    private EntityManagerInterface $em;
    private SluggerInterface $slugger;
    private LocationManager $locationManager;

    public static array $languageMap = [
        'francais' => Language::LANGUAGE_FR,
        'anglais' => Language::LANGUAGE_EN,
        'espagnol' => Language::LANGUAGE_ES,
        'allemand' => Language::LANGUAGE_DE,
        'italien' => Language::LANGUAGE_IT,
        'portugais' => Language::LANGUAGE_PT,
        'chinois' => Language::LANGUAGE_ZH,
        'arabe' => Language::LANGUAGE_AR,
        'russe' => Language::LANGUAGE_RU,
        'neerlandais' => Language::LANGUAGE_NL,
    ];

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger, LocationManager $locationManager)
    {
        $this->em = $em;
        $this->slugger = $slugger;
        $this->locationManager = $locationManager;
    }

    public function hydrateUserDocument(array $data, ?UserDocument $userDocument = null): void
    {
        // hydrate User Document
        if (!empty($data['profile']['text']) && $userDocument instanceof UserDocument) {
            $userDocument->setContent(Encoding::fixUTF8($data['profile']['text']));
        }
    }

    public function hydrateUser(array $data, User $user): User
    {
        $gender = $data['profile']['info']['gender'] ?? null;
        if ($gender) {
            $gender = 'male' === $gender ? Gender::MALE : Gender::FEMALE;
        }

        // name
        $firstname = $data['profile']['info']['first_name'] ?? null;
        $lastname = $data['profile']['info']['last_name'] ?? null;
        $summary = $data['profile']['info']['summary'] ?? null;
        $title = $summary ? Strings::substrToLength($summary, 100) : null;

        $phone = PhoneNumberTransformer::transform($data['profile']['info']['phone'] ?? null);
        $birthdate = $data['parsing']['date_birth']['timestamp'] ? (new \DateTime())->setTimestamp($data['parsing']['date_birth']['timestamp']) : null;

        // websites
        $urls = $this->getUrls($data);
        $website = $urls['from_resume'] ?? null;
        $linkedInUrl = $urls['linkedin'] ?? null;
        $github = $urls['github'] ?? null;

        // address
        // here address is space separated ex: 1 2 rue de la Réussite 7 5 0 1 2 Paris
        $addressLabel = $data['profile']['info']['location']['text'] ?? null;
        // if Hrflow find address, it will be formatted in fields/text
        if (\array_key_exists('text', $data['profile']['info']['location']['fields'])) {
            $addressLabel = $data['profile']['info']['location']['fields']['text'];
        }
        $userLocation = $addressLabel ? $this->addressToLocation($addressLabel) : null;

        // educations
        $latestFormation = $data['profile']['educations'][0] ?? null;

        // experiences duration
        $experiencesDurationValue = $data['profile']['experiences_duration'] ? (int) (ceil((float) $data['profile']['experiences_duration'])) : null;
        if ($experiencesDurationValue < 1) {
            $experiencesDuration = ExperienceYear::LESS_THAN_1_YEAR;
        } elseif ($experiencesDurationValue <= 2) {
            $experiencesDuration = ExperienceYear::YEARS_1_2;
        } elseif ($experiencesDurationValue <= 4) {
            $experiencesDuration = ExperienceYear::YEARS_3_4;
        } elseif ($experiencesDurationValue <= 10) {
            $experiencesDuration = ExperienceYear::YEARS_5_10;
        } elseif ($experiencesDurationValue <= 15) {
            $experiencesDuration = ExperienceYear::YEARS_11_15;
        } else {
            $experiencesDuration = ExperienceYear::MORE_THAN_15_YEARS;
        }

        // separate soft and hard skills
        $hardSkills = array_filter($data['profile']['skills'],
            static function (array $skill) {
                return 'hard' === $skill['type'];
            }
        );

        $softSkills = array_filter($data['profile']['skills'],
            static function (array $skill) {
                return 'soft' === $skill['type'];
            }
        );

        // languages
        $languages = array_map(static function (array $language) {
            return [
                'name' => $language['name'],
                'level' => LanguageLevel::FULL_PROFESSIONAL_CAPACITY,
            ];
        },
            $data['profile']['languages']
        );

        // description
        $description = null;
        $interests = $data['profile']['interests'] ?? [];
        if (0 !== \count($interests)) {
            $description = "Centre d'intérêts : \n";
            $description .= implode(', ', array_map(static function (array $interest) {
                return mb_convert_case($interest['name'], \MB_CASE_TITLE, 'utf8');
            }, $data['profile']['interests']));
        }

        // Hydrate User entity
        if ($gender && !$user->getGender()) {
            $user->setGender($gender);
        }
        if (!$user->getFirstName()) {
            $user->setFirstName($firstname);
        }
        if (!$user->getLastName()) {
            $user->setLastName($lastname);
        }
        if (!$user->getProfileJobTitle()) {
            $user->setProfileJobTitle($title);
        }
        if (!$user->getExperienceYear()) {
            $user->setExperienceYear($experiencesDuration);
        }

        if ((null !== $user->getLocation() && null === $user->getLocation()->getKey()) && $userLocation && null !== $userLocation->getLocality()) {
            $user->setLocation($userLocation);
        }
        if (!$user->getPhone()) {
            $user->setPhone($phone);
        }
        if (!empty($website) && !$user->getProfileWebsite()) {
            $user->setProfileWebsite($this->handleUrl($website));
        }
        if (!empty($linkedInUrl) && !$user->getProfileLinkedInProfile()) {
            $user->setProfileLinkedInProfile($this->handleUrl($linkedInUrl));
        }
        if (!empty($github) && !$user->getProfileProjectWebsite()) {
            $user->setProfileProjectWebsite($this->handleUrl($github));
        }
        if (!$user->getIntroduceYourself()) {
            $user->setIntroduceYourself($description);
        }
        if ($birthdate && !$user->getBirthdate()) {
            $user->setBirthdate($birthdate);
        }

        // Latest formation
        if (!$user->getFormation() && (\is_array($latestFormation) && \count($latestFormation))) {
            $diplomaYear = isset($latestFormation['date_end']) ? strtotime($latestFormation['date_end']) : null;
            $diplomaYear = match ($diplomaYear) {
                false, null => null,
                default => Carbon::createFromTimestamp($diplomaYear)->year,
            };

            $userFormation = new UserFormation();
            $userFormation->setDiplomaLevel($data['profile']['educations_duration'] ? (int) ceil($data['profile']['educations_duration']) : null);
            $userFormation->setBeingObtained($diplomaYear > (new \DateTime())->format('Y'));
            $userFormation->setDiplomaTitle(\strlen($latestFormation['title']) > 255 ? substr($latestFormation['title'], 0, 255) : $latestFormation['title']);
            $userFormation->setSchool($latestFormation['school']);

            if ($diplomaYear) {
                $userFormation->setDiplomaYear($diplomaYear);
            }

            $user->setFormation($userFormation);
        }

        $this->hydrateUserHardSkills($hardSkills, $user);
        $this->hydrateUserSoftSkills($softSkills, $user);
        $this->hydrateUserLanguages($languages, $user);

        return $user;
    }

    /**
     * @param string|array $url
     */
    private function handleUrl($url): string
    {
        if (\is_array($url)) {
            $url = reset($url);
        }

        if (\is_string($url) && 0 === preg_match("/^(http|https):\/\//", $url)) {
            $url = "http://$url";
        }

        return (string) $url;
    }

    private function getUrls(array $data): array
    {
        if (empty($data['profile']['info']['urls'])) {
            return [];
        }

        $urls = [];
        foreach ($data['profile']['info']['urls'] as $url) {
            if (\array_key_exists('type', $url) && \array_key_exists('url', $url)) {
                $urls[$url['type']] = $url['url'];
            }
        }

        return $urls;
    }

    private function addressToLocation(string $addressText): ?Location
    {
        $results = $this->locationManager->autocompleteMobilities($addressText);

        return \count($results) ? $results[0] : null;
    }

    private function hydrateUserHardSkills(array $skills, User $user): void
    {
        foreach ($skills as $skill) {
            $existingSkill = $this->em->getRepository(Skill::class)->findOneBy([
                'slug' => $this->slugger->slug($skill['name']),
            ]);

            if (null === $existingSkill) {
                $existingSkill = $this->em->getRepository(Skill::class)->findOneBy([
                    'synonymSlugs' => $this->slugger->slug($skill['name']),
                ]);
            }

            if ($existingSkill) {
                $userSkill = $this->em->getRepository(UserSkill::class)->findOneBy([
                    'user' => $user,
                    'skill' => $existingSkill,
                ]);

                if ($userSkill) {
                    continue;
                }

                $newSkill = $existingSkill;
            } else {
                $newSkill = (new Skill())
                    ->setName($skill['name'])
                ;

                $this->em->persist($newSkill);
            }

            $newUserSkill = (new UserSkill())
                ->setUser($user)
                ->setSkill($newSkill)
            ;

            $user->addSkill($newUserSkill);
        }
    }

    private function hydrateUserSoftSkills(array $skills, User $user): void
    {
        foreach ($skills as $skill) {
            $existingSkill = $this->em->getRepository(SoftSkill::class)->findOneBy([
                'slug' => $this->slugger->slug($skill['name']),
            ]);

            if ($existingSkill) {
                if ($user->getSoftSkills()->contains($existingSkill)) {
                    continue;
                }

                $newSoftSkill = $existingSkill;
            } else {
                $newSoftSkill = (new SoftSkill())
                    ->setName($skill['name'])
                ;

                $this->em->persist($newSoftSkill);
            }

            $user->addSoftSkill($newSoftSkill);
        }
    }

    private function hydrateUserLanguages(array $languages, User $user): void
    {
        foreach ($languages as $language) {
            if ($user->getLanguages()->contains($language)) {
                continue;
            }

            if (!\array_key_exists($language['name'], self::$languageMap)) {
                continue;
            }

            $languageCode = self::$languageMap[$language['name']];

            $user->addLanguage(
                (new UserLanguage())
                    ->setUser($user)
                    ->setLanguage($languageCode)
                    ->setLanguageLevel($language['level'])
            );
        }
    }
}
