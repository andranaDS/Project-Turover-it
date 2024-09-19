<?php

namespace App\Forum\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Enum\Locale;
use App\Forum\Entity\ForumCategory;
use Doctrine\Persistence\ObjectManager;

class ForumCategoriesFixtures extends AbstractFixture
{
    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        foreach ($this->getData() as $d) {
            $this->createCategory($d);
        }

        $manager->flush();
    }

    public function createCategory(array $data, ForumCategory $parent = null): void
    {
        $category = (new ForumCategory())
            ->setTitle($data['title'])
            ->setPosition($data['position'])
            ->setDescription($data['description'] ?? null)
            ->setParent($parent)
            ->setLocales($data['locales'])
            ->setMetaTitle($data['metaTitle'] ?? null)
            ->setMetaDescription($data['metaDescription'] ?? null)
        ;

        $this->manager->persist($category);

        foreach (($data['children'] ?? []) as $d) {
            $this->createCategory($d, $category);
        }
    }

    public function getDevData(): array
    {
        return [
            [
                'title' => 'Administration et comptabilité',
                'position' => 0,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'La comptabilité, la gestion, les obligations légales',
                        'description' => 'Les formalités administratives, les obligations comptables : déclarations de résultat, TVA CA12, bilan...',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Les frais',
                        'description' => 'Que faites vous passer en frais ? loyer, vehicule...',
                        'position' => 1,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
            [
                'title' => 'Débutants',
                'position' => 1,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Par où commencer ?',
                        'description' => 'Le dossier à constituer, l\'immatriculation, où aller ? Les types de contrats, les clauses obligatoires etc, posez vos questions aux seniors ...',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Vos problèmes, les pièges à éviter',
                        'description' => 'Faites partager aux autres les soucis que vous avez rencontrés. Infos à lire avec attention...',
                        'position' => 1,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Portage salarial',
                        'description' => 'Toutes les questions sur le portage salarial, decouvrez les atouts et les inconvénients d\'être porté.',
                        'position' => 2,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
            [
                'title' => 'Personnel',
                'position' => 2,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Presentez-vous !',
                        'description' => 'Ici, c\'est le forum pour se présenter et suggerer des modifs aux webmasters du site !',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Protection sociale, retraite',
                        'description' => 'Les questions sur votre retraite, votre protection sociale, les assurances perte d\'activité, les complémentaires santé...',
                        'position' => 1,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Le café de Freelance-info',
                        'description' => 'Ce forum est destiné à toutes les discussions ne pouvant être classées dans les autres catégories. Ceci ne signifie pas pour autant qu\'il est possible d\'y faire n\'importe quoi ! ',
                        'position' => 2,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
            [
                'title' => 'Projets',
                'position' => 3,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Vos projets',
                        'description' => 'Envie d\'embaucher, de retourner salarié ? De monter un GIE, une SSII ?',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
            [
                'title' => 'Parlons technique',
                'position' => 4,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Les meilleurs outils utilisés par les développeurs',
                        'description' => 'Echanges sur les outils utilisés par les développeurs au quotidien',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Logiciels et applications',
                        'description' => 'Echanges et résolutions de problèmes ',
                        'position' => 1,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Réseaux et sécurité informatique',
                        'description' => 'Partage technique et entraide entre experts en réseaux et sécurité informatique ',
                        'position' => 2,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'JAVA',
                        'description' => 'Partage technique et entraide sur les technologies web de Java',
                        'position' => 3,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'PHP',
                        'description' => 'Echanges sur les frameworks PHP (Symfony, Laravel, ZendFramework...) ',
                        'position' => 4,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'C, C++',
                        'description' => 'Partage technique et entraide sur les langages C et C++ ',
                        'position' => 5,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'C#',
                        'description' => 'Partage technique et entraide sur le langage C# ',
                        'position' => 6,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Autres langages',
                        'description' => 'Forum ouvert aux discussions sur tous les langages non cités plus hauts (JavaScript, ...)',
                        'position' => 7,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'ERP',
                        'description' => 'Partage et d\'entraide sur les ERP (Enterprise Resource Planning)',
                        'position' => 8,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'DEVOPS',
                        'description' => 'Difficultés rencontrées, recommandations lors du rapprochement des team dev et infra.',
                        'position' => 9,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Cloud computing',
                        'description' => 'Avantages, inconvénients, points d\'attention',
                        'position' => 10,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Blockchain',
                        'description' => 'Pratique et enjeux à venir',
                        'position' => 11,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
        ];
    }

    public function getTestData(): array
    {
        return [
            [
                'title' => 'Category 1',
                'position' => 0,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Category 1.1',
                        'description' => 'Lorem ipsum dolor sit amet',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                        'metaTitle' => 'Forum - Category 1.1 // Meta title',
                        'metaDescription' => 'Forum - Category 1.1 // Meta description',
                    ],
                    [
                        'title' => 'Category 1.2',
                        'description' => 'Curabitur eros eros, maximus lobortis sollicitudin eget, gravida nec velit',
                        'position' => 1,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
            [
                'title' => 'Category 2',
                'position' => 1,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Category 2.1',
                        'description' => 'Nullam erat purus, bibendum ac velit ut, commodo tincidunt nisl',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Category 2.2',
                        'description' => 'Phasellus tincidunt, leo et tempus finibus, ex mi feugiat purus, vitae molestie felis orci et velit',
                        'position' => 1,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                        'metaTitle' => 'Forum - Category 2.2 // Meta title',
                        'metaDescription' => 'Forum - Category 2.2 // Meta description',
                    ],
                    [
                        'title' => 'Category 2.3',
                        'description' => 'Nullam interdum in elit sit amet eleifend. Integer mollis metus non imperdiet sagittis. Integer vitae felis velit',
                        'position' => 2,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                        'metaTitle' => 'Forum - Category 2.3 // Meta title',
                        'metaDescription' => 'Forum - Category 2.3 // Meta description',
                    ],
                ],
            ],
            [
                'title' => 'Category 3',
                'position' => 2,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Category 3.1',
                        'description' => 'Quisque vitae molestie diam. Aliquam finibus ex sed purus convallis pretium. Phasellus rutrum a quam in mollis',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
            [
                'title' => 'Category 4',
                'position' => 3,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Category 4.1',
                        'description' => 'Lorem ipsum dolor sit amet',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Category 4.2',
                        'description' => 'Curabitur eros eros, maximus lobortis sollicitudin eget, gravida nec velit',
                        'position' => 1,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
            [
                'title' => 'Category 6',
                'position' => 4,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Category 6.1',
                        'description' => 'Lorem ipsum dolor sit amet',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Category 6.2',
                        'description' => 'Curabitur eros eros, maximus lobortis sollicitudin eget, gravida nec velit',
                        'position' => 1,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
            [
                'title' => 'Category 7',
                'position' => 5,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Category 7.1',
                        'description' => 'Lorem ipsum dolor sit amet',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
            [
                'title' => 'Category 8',
                'position' => 6,
                'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                'children' => [
                    [
                        'title' => 'Category 8.1',
                        'description' => 'Lorem ipsum dolor sit amet',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                    [
                        'title' => 'Category 8.2',
                        'description' => 'Lorem ipsum dolor sit amet',
                        'position' => 0,
                        'locales' => [Locale::fr_FR, Locale::fr_BE, Locale::fr_CH, Locale::fr_LU],
                    ],
                ],
            ],
        ];
    }
}
