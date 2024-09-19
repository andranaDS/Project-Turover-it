<?php

namespace App\Blog\DataFixtures;

use App\Blog\Entity\BlogCategory;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Enum\Locale;
use Doctrine\Persistence\ObjectManager;

class BlogCategoriesFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $d) {
            $category = (new BlogCategory())
                ->setName($d['name'])
                ->setDescription($d['description'])
                ->setMetaTitle($d['metaTitle'])
                ->setMetaDescription($d['metaDescription'])
                ->setLocales($d['locales'])
            ;
            $manager->persist($category);
        }
        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        if (($handle = fopen(__DIR__ . '/files/blog-categories.csv', 'r')) !== false) {
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                $data[] = [
                    'name' => $d[0],
                    'description' => $d[1],
                    'metaTitle' => $d[2],
                    'metaDescription' => $d[3],
                    'locales' => 'fr' === $d[4] ? [
                        Locale::fr_FR,
                        Locale::fr_BE,
                        Locale::fr_CH,
                        Locale::fr_LU,
                    ] : [
                        Locale::en_GB,
                    ],
                ];
            }
            fclose($handle);
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'name' => 'Blog - Category 1',
                'description' => 'Blog - Category 1 // Description',
                'metaTitle' => 'Blog - Category 1 // Meta title',
                'metaDescription' => 'Blog - Category 1 // Meta description',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
            [
                'name' => 'Blog - Category 2',
                'description' => 'Blog - Category 2 // Description',
                'metaTitle' => 'Blog - Category 2 // Meta title',
                'metaDescription' => 'Blog - Category 2 // Meta description',
                'locales' => [
                    Locale::fr_BE,
                    Locale::nl_BE,
                ],
            ],
            [
                'name' => 'Blog - Category 3',
                'description' => 'Blog - Category 3 // Description',
                'metaTitle' => 'Blog - Category 3 // Meta title',
                'metaDescription' => 'Blog - Category 3 // Meta description',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
        ];
    }
}
