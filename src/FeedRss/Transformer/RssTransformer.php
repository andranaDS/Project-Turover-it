<?php

namespace App\FeedRss\Transformer;

use App\Core\Enum\Currency;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

class RssTransformer
{
    public static function transformContract(?array $contractsList): string
    {
        $listOfContracts = '';
        $contracts = [
            'permanent' => 'CDI',
            'fixed-term' => 'CDD',
            'contractor' => 'Freelance',
            'apprenticeship' => 'Apprentissage',
            'internship' => 'Stage',
        ];

        if (false === (null === $contractsList)) {
            foreach ($contractsList as $index => $contract) {
                if (\array_key_exists($contract, $contracts)) {
                    $listOfContracts .= 0 !== $index ? ', ' . $contracts[$contract] : $contracts[$contract];
                }
            }
        }

        return self::transformForRss($listOfContracts);
    }

    public static function transformTitle(?string $title, bool $withTransform = true): ?string
    {
        if (null !== $title) {
            $keywords = ['informatique', 'r[e|é]seau', 'internet', 'd[e/é]veloppeur', 'programmeur', 'informaticien'];

            $regex = '#' . implode('|', $keywords) . '#';
            $checkKeywords = preg_match($regex, $title);
            if (false === $checkKeywords || 0 === $checkKeywords) {
                $title .= ' (IT)';
            }

            $title = self::addInformatiqueToKeywords($title);
        }

        if (true === $withTransform) {
            return self::transformForRss($title);
        }

        return $title;
    }

    public static function transformForUrl(string $url, ?string $gaTag): string
    {
        if (null !== $gaTag) {
            $url .= '?' . $gaTag;
        }

        return self::transformForRss($url);
    }

    public static function transformForRss(?string $property): string
    {
        return '<![CDATA[' . ($property ?? '') . ']]>';
    }

    public static function transformSalary(?string $annualSalary, ?string $dailySalary): string
    {
        if (null === $annualSalary && null === $dailySalary) {
            $salary = '';
        } elseif (null !== $annualSalary) {
            $salary = $annualSalary . ' per year.';
        } else {
            $salary = $dailySalary . ' per day.';
        }

        return self::transformForRss($salary);
    }

    private static function addInformatiqueToKeywords(string $title): string
    {
        if (false !== stripos($title, 'manager') && false === stripos($title, 'community')) {
            $title = str_ireplace('manager', 'manager informatique', $title);
        }

        $keywords = ['sécurité', 'securite', 'projets', 'projet', 'ingénieur', 'ingénieurs', 'ingénieur(e)',
            'ingenieur', 'ingenieurs', 'ingenieur(e)', 'consultant', 'consultants', 'directeur', 'hotliner',
            'technicien(ne)', 'techniciens', 'technicien', 'exploitation', 'architecte', 'assistant(e)',
            'production', 'responsable', 'commercial', 'recetteur', 'recette', 'testeurs', 'testeur',
            'integration', 'technique', 'leader', 'analyst',
        ];

        $keywordsWithInformatique = array_map(static function (string $item): string {
            return 'analyst' === $item ? 'analyste informatique' : $item . ' informatique';
        }, $keywords);

        return str_ireplace($keywords, $keywordsWithInformatique, $title);
    }

    public static function transformExperienceTranslated(?string $experience): string
    {
        if (null === $experience) {
            return self::transformForRss($experience);
        }

        $translator = self::getTranslator();

        return self::transformForRss($translator->trans(sprintf('experience_level.%s', $experience)));
    }

    public static function getTranslator(): Translator
    {
        $translator = new Translator('fr_FR');
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource('yaml', __DIR__ . '/../Resources/translations/feeds.fr.yaml', 'fr');

        return $translator;
    }

    public static function transformValueTranslated(string $value, bool $withTransform = true): string
    {
        $translator = self::getTranslator();

        return $withTransform ? self::transformForRss($translator->trans($value)) : $translator->trans($value);
    }

    public static function transformCurrency(?string $currency): string
    {
        return match ($currency) {
            Currency::EUR => '€',
            Currency::USD => '$US',
            Currency::GBP => '£',
            default => '€',
        };
    }
}
