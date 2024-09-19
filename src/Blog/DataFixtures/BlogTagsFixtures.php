<?php

namespace App\Blog\DataFixtures;

use App\Blog\Entity\BlogTag;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Enum\Locale;
use Doctrine\Persistence\ObjectManager;

class BlogTagsFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $d) {
            $category = (new BlogTag())
                ->setName($d['name'])
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
        if (($handle = fopen(__DIR__ . '/files/blog-tags.csv', 'r')) !== false) {
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                $data[] = [
                    'name' => $d[0],
                    'metaTitle' => $d[1],
                    'metaDescription' => $d[2],
                    'locales' => [
                        Locale::fr_FR,
                        Locale::fr_BE,
                        Locale::fr_CH,
                        Locale::fr_LU,
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
                'name' => 'Blog - Tag 1',
                'metaTitle' => 'Blog - Tag 1 // Meta title',
                'metaDescription' => 'Blog - Tag 1 // Meta description',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
            [
                'name' => 'Blog - Tag 2',
                'metaTitle' => 'Blog - Tag 2 // Meta title',
                'metaDescription' => 'Blog - Tag 2 // Meta description',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
            [
                'name' => 'Blog - Tag 3',
                'metaTitle' => 'Blog - Tag 3 // Meta title',
                'metaDescription' => 'Blog - Tag 3 // Meta description',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
            [
                'name' => 'Blog - Tag 4',
                'metaTitle' => 'Blog - Tag 4 // Meta title',
                'metaDescription' => 'Blog - Tag 4 // Meta description',
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
