<?php

namespace App\Core\Tests\Functional\Job;

use App\Tests\Functional\ApiTestCase;

class JobGetTest extends ApiTestCase
{
    public static function provideFoundCases(): iterable
    {
        return [
            [
                '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                [
                    '@context' => '/contexts/Job',
                    '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                    '@type' => 'Job',
                    'id' => 1,
                    'name' => 'Administrateur BDD',
                    'slug' => 'administrateur-bdd',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Administrateur de base de donnée (oracle, sybase…)',
                    'nameForContributionSlug' => 'administrateur-de-base-de-donnee-oracle-sybase',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur de base de données',
                    'nameForUserSlug' => 'administrateur-de-base-de-donnees',
                    'category' => [
                        '@id' => '/job_categories/1',
                        '@type' => 'JobCategory',
                        'id' => 1,
                        'name' => 'Data',
                        'slug' => 'data',
                    ],
                    'salaryDescription' => 'L\'administrateur de bases de données est responsable du bon fonctionnement d\'une base de données.
A ce titre, il est principalement en charge des périmètres suivants : intégrité des données, sécurité, performance, aide au développement et au test, disponibilité et recouvrement de données.
C\'est donc lui qui conçoit, gère et administre les systèmes de gestion de données de l\'entreprise, et qui en assure à la fois la cohérence, la qualité et la sécurité.
L\'administrateur de base de données est également appelé gestionnaire de base de données, administrateur de bases de données, ingénieur datawarehouse ou encore database administrator (DBA).',
                    'salaryFormation' => 'Bac +5 école d\'ingénieurs',
                    'salaryStandardMission' => 'Conception et spécifications des paramètres et de l\'architecture de la base de données en lien avec l\'architecture SI Administration de la base de données
Suivi de la qualité des données
Suivi du niveau de service
Gestion des accès utilisateurs et de la sécurité
Gestion des évolutions, des migrations et des back up
Mise à jour des documents d\'exploitation
Support technique (utilisateurs et équipes de développement)
Garantie de la confidentialité, de l\'intégrité et de la disponibilité des données',
                    'salarySkills' => [
                        'ORACLE',
                        'SYBASE',
                        'SQL SERVER',
                        'DB2',
                        'Architecture technique SI',
                        'Intégration',
                        'Gestion des opérations informatiques',
                        'Gestion des contrôles, tests et diagnostics',
                    ],
                    'salarySeoMetaTitle' => 'TJM et salaire : Administrateur base de données | Free-Work.com',
                    'salarySeoMetaDescription' => 'Retrouvez le TJM et le salaire moyen pour le métier d\' Administrateur base de données. Retrouvez également toutes les informations relatives à ce métier, le ...',
                    'faqPrice' => 'En fonction de son expérience, un administrateur de base de données va pouvoir prétendre à un taux journalier moyen plus ou moins important. Actuellement, le TJM moyen pour un administrateur de base de données s\'élève à 340 € par jour. Un professionnel disposant de plus de 10 ans d\'expérience pourra prétendre à un TJM deux fois plus important. Le salaire mensuel moyen d\'un administrateur base de données est de 4500€.',
                    'faqDefinition' => 'L\'administrateur de base de données est également connu sous le nom de gestionnaire de base de données. Son rôle au sein du service IT d\'une entreprise est des plus importants puisque c\'est lui qui s\'occupe de gérer les données et de les mettre à disposition. Il va également assurer la sécurité du réseau informatique ainsi que celle des données. Il travaille régulièrement pour augmenter les performances de tout le système afin que les personnes qui l\'utilisent puissent accéder aux données le plus simplement et rapidement possible. C\'est à l\'administrateur de base de données que revient la charge de choisir les différents logiciels qui vont être utilisés au sein de l\'entreprise. Il sera également de son ressort de les installer et de les configurer. En cas de besoin, un administrateur de base de données peut être amené à former les différentes personnes utilisant le système informatique de l\'entreprise.',
                    'faqMissions' => 'Un administrateur de base de données va pouvoir gérer différentes missions. Il est capable de concevoir une base de données du début à la fin, cela signifie qu\'il va gérer les paramètres ainsi que la sécurité. Il dispose d\'une capacité d\'adaptation importante puisqu\'il doit prendre en compte les impératifs de son client pour créer la base de données. Il doit également être capable d\'administrer et d\'effectuer la maintenance sur les différentes bases de données. Il pourra alors être amené à dimensionner un serveur ou encore à garantir la disponibilité des différentes données qui sont stockées dans la base. Un administrateur de base de données en freelance va également être amené régulièrement à veiller à ce que les sauvegardes soient correctement réalisées afin que les fichiers ne puissent pas être perdus en cas d\'incident sur les serveurs de stockage. De manière générale, un administrateur BDD doit effectuer une veille technologique sur les bases de données afin que les performances du système informatique soient toujours optimales. Enfin, on peut noter que l\'administrateur de base de données se doit d\'être présent pour former certaines personnes en cas de besoin mais également pour assister les informaticiens.',
                    'faqSkills' => 'Un administrateur de base de données dispose d\'une bonne connaissance du système informatique de manière générale ainsi que de l\'architecture de ce dernier. Il est capable de gérer les différents systèmes ainsi que différents types de base de données. La maîtrise du langage de requête SQL est impérative, de même qu\'un administrateur se doit de connaître les différents scripts Shell. Par ailleurs, l\'administrateur BDD dispose de compétences en matière de cybersécurité. Il est important de noter que ce professionnel doit également disposer d\'une certaine maîtrise de l\'anglais.',
                    'faqProfile' => 'Le métier d\'administrateur de base de données est tout à fait adapté pour une personne qui est capable de s\'adapter rapidement au service IT de l\'entreprise, notamment dans le cas où elle évolue en freelance. Un administrateur de base de données est une personne qui doit être réactive et également pédagogue puisqu\' elle sera amenée à former différentes personnes en cas de besoin. Enfin, un administrateur doit être réactif et rigoureux puisqu\'il sera nécessaire d\'intervenir rapidement en cas de problème au niveau de la sécurité du réseau par exemple. La gestion des données demande de la rigueur puisque celles-ci doivent pouvoir être mise à disposition des utilisateurs en toutes circonstances.',
                    'faqSeoMetaTitle' => 'Fiche métier : Administrateur base de données | Free-Work.com',
                    'faqSeoMetaDescription' => 'Découvrez le métier d\'administrateur base de données. Les compétences requises, les missions principales, le salaire, le TJM, la formation.',
                ],
            ],
            [
                '/jobs/administrateur-oracle',
                [
                    '@context' => '/contexts/Job',
                    '@id' => '/jobs/administrateur-oracle',
                    '@type' => 'Job',
                    'id' => 4,
                    'name' => 'Administrateur Oracle',
                    'slug' => 'administrateur-oracle',
                    'availableForContribution' => false,
                    'nameForContribution' => 'Administrateur Oracle',
                    'nameForContributionSlug' => 'administrateur-oracle',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur oracle',
                    'nameForUserSlug' => 'administrateur-oracle',
                    'category' => null,
                    'salaryDescription' => null,
                    'salaryFormation' => null,
                    'salaryStandardMission' => null,
                    'salarySkills' => [
                        '',
                    ],
                    'salarySeoMetaTitle' => null,
                    'salarySeoMetaDescription' => null,
                    'faqPrice' => null,
                    'faqDefinition' => null,
                    'faqMissions' => null,
                    'faqSkills' => null,
                    'faqProfile' => null,
                    'faqSeoMetaTitle' => null,
                    'faqSeoMetaDescription' => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideFoundCases
     */
    public function testFound(string $path, array $expected): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public function testNotFound(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/jobs/actor');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
