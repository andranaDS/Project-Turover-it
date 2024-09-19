<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\User\Entity\User;
use App\User\Entity\UserLanguage;
use App\User\Enum\Language;
use App\User\Enum\LanguageLevel;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserLanguagesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            if ($user->getProfileJobTitle()) {
                $this->users[$user->getEmail()] = $user;
            }
        }

        foreach ($this->getData() as $d) {
            $language = (new UserLanguage())
                ->setUser($d['user'])
                ->setLanguage($d['language'])
                ->setLanguageLevel($d['languageLevel'])
            ;
            $manager->persist($language);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        foreach ($this->users as $user) {
            if (0 !== mt_rand(0, 4)) {
                $languageCount = mt_rand(1, 3);
                for ($i = 0; $i < $languageCount; ++$i) {
                    $data[] = [
                        'user' => $user,
                        'language' => Arrays::getRandom(Language::getConstants()),
                        'languageLevel' => Arrays::getRandom(LanguageLevel::getConstants()),
                    ];
                }
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['user@free-work.fr'],
                'language' => Language::LANGUAGE_FR,
                'languageLevel' => LanguageLevel::NATIVE_OR_BILINGUAL,
            ],
            [
                'user' => $this->users['user@free-work.fr'],
                'language' => Language::LANGUAGE_DE,
                'languageLevel' => LanguageLevel::LIMITED_PROFESSIONAL_SKILLS,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'language' => Language::LANGUAGE_FR,
                'languageLevel' => LanguageLevel::NATIVE_OR_BILINGUAL,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'language' => Language::LANGUAGE_EN,
                'languageLevel' => LanguageLevel::LIMITED_PROFESSIONAL_SKILLS,
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'language' => Language::LANGUAGE_NL,
                'languageLevel' => LanguageLevel::NATIVE_OR_BILINGUAL,
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'language' => Language::LANGUAGE_EN,
                'languageLevel' => LanguageLevel::FULL_PROFESSIONAL_CAPACITY,
            ],
        ];
    }

    public static function getGroups(): array
    {
        return ['user'];
    }

    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
        ];
    }
}
