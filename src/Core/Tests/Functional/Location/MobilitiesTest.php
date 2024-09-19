<?php

namespace App\Core\Tests\Functional\Location;

use App\Core\Entity\LocationKeyLabel;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class MobilitiesTest extends ApiTestCase
{
    public const WAIT_BETWEEN_SEARCHES = 600; // Used to fit location APIs limits

    public function testWithoutSearch(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/locations/mobilities');

        self::assertResponseStatusCodeSame(400);
    }

    public static function provideCitiesCases(): iterable
    {
        yield [
            'Paris',
            [
                [
                    'street' => null,
                    'locality' => null,
                    'postalCode' => '75000',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Paris',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8588897',
                    'longitude' => '2.32004102',
                    'key' => 'fr~ile-de-france~paris~',
                    'label' => 'Paris, France',
                    'shortLabel' => 'Paris',
                ],
            ],
        ];

        yield [
            'Sarcelles',
            [
                [
                    'street' => null,
                    'locality' => 'Sarcelles',
                    'postalCode' => '95200',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => "Val-d'Oise",
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.9960813',
                    'longitude' => '2.3796245',
                    'key' => 'fr~ile-de-france~val-doise~sarcelles',
                    'label' => 'Sarcelles, Île-de-France',
                    'shortLabel' => 'Sarcelles (95)',
                ],
            ],
        ];

        yield [
            'Bru',
            [
                [
                    'street' => null,
                    'locality' => 'Brue-Auriac',
                    'postalCode' => '83119',
                    'adminLevel1' => "Provence-Alpes-Côte d'Azur",
                    'adminLevel2' => 'Var',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '43.5279244',
                    'longitude' => '5.9450378',
                    'key' => 'fr~provence-alpes-cote-dazur~var~brue-auriac',
                    'label' => "Brue-Auriac, Provence-Alpes-Côte d'Azur",
                    'shortLabel' => 'Brue-Auriac (83)',
                ],
                [
                    'street' => null,
                    'locality' => 'Brû',
                    'postalCode' => '88700',
                    'adminLevel1' => 'Grand Est',
                    'adminLevel2' => 'Vosges',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.3486145',
                    'longitude' => '6.6837962',
                    'key' => 'fr~grand-est~vosges~bru',
                    'label' => 'Brû, Grand Est',
                    'shortLabel' => 'Brû (88)',
                ],
                [
                    'street' => null,
                    'locality' => 'Brù',
                    'postalCode' => 'HS2 0QW',
                    'adminLevel1' => 'Écosse',
                    'adminLevel2' => 'Na h-Eileanan Siar',
                    'country' => 'Royaume-Uni',
                    'countryCode' => 'GB',
                    'latitude' => '58.3538295',
                    'longitude' => '-6.5487749',
                    'key' => 'gb~ecosse~na-h-eileanan-siar~bru',
                    'label' => 'Brù, Écosse, Royaume-Uni',
                    'shortLabel' => 'Brù',
                ],
                [
                    'street' => null,
                    'locality' => 'Bruère-Allichamps',
                    'postalCode' => '18200',
                    'adminLevel1' => 'Centre-Val de Loire',
                    'adminLevel2' => 'Cher',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '46.76782',
                    'longitude' => '2.43129',
                    'key' => 'fr~centre-val-de-loire~cher~bruere-allichamps',
                    'label' => 'Bruère-Allichamps, Centre-Val de Loire',
                    'shortLabel' => 'Bruère-Allichamps (18)',
                ],
                [
                    'street' => null,
                    'locality' => 'Brugheas',
                    'postalCode' => '03700',
                    'adminLevel1' => 'Auvergne-Rhône-Alpes',
                    'adminLevel2' => 'Allier',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '46.0774',
                    'longitude' => '3.3676514',
                    'key' => 'fr~auvergne-rhone-alpes~allier~brugheas',
                    'label' => 'Brugheas, Auvergne-Rhône-Alpes',
                    'shortLabel' => 'Brugheas (3)',
                ],
            ],
        ];

        yield [
            'Bruxelles',
            [
                [
                    'street' => null,
                    'locality' => 'Ville de Bruxelles',
                    'postalCode' => null,
                    'adminLevel1' => 'Bruxelles-Capitale',
                    'adminLevel2' => 'Bruxelles-Capitale',
                    'country' => 'Belgique',
                    'countryCode' => 'BE',
                    'latitude' => '50.8465573',
                    'longitude' => '4.351697',
                    'key' => 'be~bruxelles-capitale~bruxelles-capitale~ville-de-bruxelles',
                    'label' => 'Ville de Bruxelles, Bruxelles-Capitale, Belgique',
                    'shortLabel' => 'Ville de Bruxelles',
                ],
                [
                    'street' => null,
                    'locality' => null,
                    'postalCode' => null,
                    'adminLevel1' => null,
                    'adminLevel2' => null,
                    'country' => 'Belgique',
                    'countryCode' => 'BE',
                    'latitude' => '50.83879505',
                    'longitude' => '4.37530413',
                    'key' => 'be~~~',
                    'label' => 'Belgique',
                    'shortLabel' => 'Belgique',
                ],
                [
                    'street' => null,
                    'locality' => null,
                    'postalCode' => null,
                    'adminLevel1' => 'Bruxelles-Capitale',
                    'adminLevel2' => 'Ville de Bruxelles',
                    'country' => 'Belgique',
                    'countryCode' => 'BE',
                    'latitude' => '50.8436353',
                    'longitude' => '4.36735375',
                    'key' => 'be~bruxelles-capitale~ville-de-bruxelles~',
                    'label' => 'Ville de Bruxelles, Belgique',
                    'shortLabel' => 'Ville de Bruxelles',
                ],
                [
                    'street' => null,
                    'locality' => 'Bruxelles',
                    'postalCode' => 'R0G 0G0',
                    'adminLevel1' => 'Manitoba',
                    'adminLevel2' => null,
                    'country' => 'Canada',
                    'countryCode' => 'CA',
                    'latitude' => '49.487322',
                    'longitude' => '-98.918504',
                    'key' => 'ca~manitoba~~bruxelles',
                    'label' => 'Bruxelles, Manitoba, Canada',
                    'shortLabel' => 'Bruxelles',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideCitiesCases
     */
    public function testCities(string $search, array $results): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/locations/mobilities', [
            'query' => [
                'search' => $search,
            ],
        ]);

        self::assertJsonContains($results);

        usleep(self::WAIT_BETWEEN_SEARCHES);
    }

    public static function provideAdminArea1Cases(): iterable
    {
        yield [
            'Ile',
            [
                [
                    'street' => null,
                    'locality' => null,
                    'postalCode' => null,
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => null,
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.6443057',
                    'longitude' => '2.7537863',
                    'key' => 'fr~ile-de-france~~',
                    'label' => 'Île-de-France, France',
                    'shortLabel' => 'Île-de-France',
                ],
                [
                    'street' => null,
                    'locality' => "L'Île-Bouchard",
                    'postalCode' => '37220',
                    'adminLevel1' => 'Centre-Val de Loire',
                    'adminLevel2' => 'Indre-et-Loire',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '47.1201355',
                    'longitude' => '0.4241574',
                    'key' => 'fr~centre-val-de-loire~indre-et-loire~lile-bouchard',
                    'label' => "L'Île-Bouchard, Centre-Val de Loire",
                    'shortLabel' => "L'Île-Bouchard (37)",
                ],
                [
                    'street' => null,
                    'locality' => "Île-d'Aix",
                    'postalCode' => '17123',
                    'adminLevel1' => 'Nouvelle-Aquitaine',
                    'adminLevel2' => 'Charente-Maritime',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '46.0123396',
                    'longitude' => '-1.1731608',
                    'key' => 'fr~nouvelle-aquitaine~charente-maritime~ile-daix',
                    'label' => "Île-d'Aix, Nouvelle-Aquitaine",
                    'shortLabel' => "Île-d'Aix (17)",
                ],
                [
                    'street' => null,
                    'locality' => 'Île-Tudy',
                    'postalCode' => '29980',
                    'adminLevel1' => 'Bretagne',
                    'adminLevel2' => 'Finistère',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '47.8427124',
                    'longitude' => '-4.1679541',
                    'key' => 'fr~bretagne~finistere~ile-tudy',
                    'label' => 'Île-Tudy, Bretagne',
                    'shortLabel' => 'Île-Tudy (29)',
                ],
                [
                    'street' => null,
                    'locality' => "L'Île-d'Elle",
                    'postalCode' => '85770',
                    'adminLevel1' => 'Pays de la Loire',
                    'adminLevel2' => 'Vendée',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '46.3316667',
                    'longitude' => '-0.9480556',
                    'key' => 'fr~pays-de-la-loire~vendee~lile-delle',
                    'label' => "L'Île-d'Elle, Pays de la Loire",
                    'shortLabel' => "L'Île-d'Elle (85)",
                ],
            ],
        ];

        yield [
            'Aquitaine',
            [
                [
                    'street' => null,
                    'locality' => null,
                    'postalCode' => null,
                    'adminLevel1' => 'Nouvelle-Aquitaine',
                    'adminLevel2' => null,
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '45.4039367',
                    'longitude' => '0.3756199',
                    'key' => 'fr~nouvelle-aquitaine~~',
                    'label' => 'Nouvelle-Aquitaine, France',
                    'shortLabel' => 'Nouvelle-Aquitaine',
                ],
                [
                    'street' => null,
                    'locality' => 'Domeyrot',
                    'postalCode' => '23140',
                    'adminLevel1' => 'Nouvelle-Aquitaine',
                    'adminLevel2' => 'Creuse',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '46.2498152',
                    'longitude' => '2.157501',
                    'key' => 'fr~nouvelle-aquitaine~creuse~domeyrot',
                    'label' => 'Domeyrot, Nouvelle-Aquitaine',
                    'shortLabel' => 'Domeyrot (23)',
                ],
                [
                    'street' => null,
                    'locality' => 'Clugnat',
                    'postalCode' => '23270',
                    'adminLevel1' => 'Nouvelle-Aquitaine',
                    'adminLevel2' => 'Creuse',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '46.30839',
                    'longitude' => '2.1177',
                    'key' => 'fr~nouvelle-aquitaine~creuse~clugnat',
                    'label' => 'Clugnat, Nouvelle-Aquitaine', 'shortLabel' => 'Clugnat (23)',
                ],
                [
                    'street' => null,
                    'locality' => 'Toulx-Sainte-Croix',
                    'postalCode' => '23600',
                    'adminLevel1' => 'Nouvelle-Aquitaine',
                    'adminLevel2' => 'Creuse',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '46.28477',
                    'longitude' => '2.2129',
                    'key' => 'fr~nouvelle-aquitaine~creuse~toulx-sainte-croix',
                    'label' => 'Toulx-Sainte-Croix, Nouvelle-Aquitaine',
                    'shortLabel' => 'Toulx-Sainte-Croix (23)',
                ],
                [
                    'street' => null,
                    'locality' => 'Auge',
                    'postalCode' => '23170',
                    'adminLevel1' => 'Nouvelle-Aquitaine',
                    'adminLevel2' => 'Creuse',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '46.2419725',
                    'longitude' => '2.3231481',
                    'key' => 'fr~nouvelle-aquitaine~creuse~auge',
                    'label' => 'Auge, Nouvelle-Aquitaine',
                    'shortLabel' => 'Auge (23)',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideAdminArea1Cases
     */
    public function testAdminArea1(string $search, array $results): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/locations/mobilities', [
            'query' => [
                'search' => $search,
            ],
        ]);

        self::assertJsonContains($results);

        usleep(self::WAIT_BETWEEN_SEARCHES);
    }

    public static function provideAdminArea2Cases(): iterable
    {
        yield [
            'Val-de-Marne',
            [
                [
                    'street' => null,
                    'locality' => null,
                    'postalCode' => null,
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Val-de-Marne',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.77448935',
                    'longitude' => '2.45433214',
                    'key' => 'fr~ile-de-france~val-de-marne~',
                    'label' => 'Val-de-Marne, France',
                    'shortLabel' => 'Val-de-Marne',
                ],
                [
                    'street' => null,
                    'locality' => 'Nogent-sur-Marne',
                    'postalCode' => '94130',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Val-de-Marne',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8388009',
                    'longitude' => '2.4917292',
                    'key' => 'fr~ile-de-france~val-de-marne~nogent-sur-marne',
                    'label' => 'Nogent-sur-Marne, Île-de-France',
                    'shortLabel' => 'Nogent-sur-Marne (94)',
                ],
                [
                    'street' => null,
                    'locality' => 'Champigny-sur-Marne',
                    'postalCode' => '94500',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Val-de-Marne',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8137759',
                    'longitude' => '2.5107384',
                    'key' => 'fr~ile-de-france~val-de-marne~champigny-sur-marne',
                    'label' => 'Champigny-sur-Marne, Île-de-France',
                    'shortLabel' => 'Champigny-sur-Marne (94)',
                ],
                [
                    'street' => null,
                    'locality' => 'Villiers-sur-Marne',
                    'postalCode' => '94350',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Val-de-Marne',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8262055',
                    'longitude' => '2.5406696',
                    'key' => 'fr~ile-de-france~val-de-marne~villiers-sur-marne',
                    'label' => 'Villiers-sur-Marne, Île-de-France',
                    'shortLabel' => 'Villiers-sur-Marne (94)',
                ],
                [
                    'street' => null,
                    'locality' => 'Bry-sur-Marne',
                    'postalCode' => '94360',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Val-de-Marne',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8352872',
                    'longitude' => '2.5193322',
                    'key' => 'fr~ile-de-france~val-de-marne~bry-sur-marne',
                    'label' => 'Bry-sur-Marne, Île-de-France',
                    'shortLabel' => 'Bry-sur-Marne (94)', ],
            ],
        ];

        yield [
            'Hauts-de-Seine',
            [
                [
                    'street' => null,
                    'locality' => null,
                    'postalCode' => null,
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Hauts-de-Seine',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8401859',
                    'longitude' => '2.19863',
                    'key' => 'fr~ile-de-france~hauts-de-seine~',
                    'label' => 'Hauts-de-Seine, France',
                    'shortLabel' => 'Hauts-de-Seine',
                ],
                [
                    'street' => null,
                    'locality' => 'Neuilly-sur-Seine',
                    'postalCode' => '92200',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Hauts-de-Seine',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.884683',
                    'longitude' => '2.2695658',
                    'key' => 'fr~ile-de-france~hauts-de-seine~neuilly-sur-seine',
                    'label' => 'Neuilly-sur-Seine, Île-de-France',
                    'shortLabel' => 'Neuilly-sur-Seine (92)',
                ],
                [
                    'street' => null,
                    'locality' => 'Asnières-sur-Seine',
                    'postalCode' => '92600',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Hauts-de-Seine',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.9105948',
                    'longitude' => '2.2890454',
                    'key' => 'fr~ile-de-france~hauts-de-seine~asnieres-sur-seine',
                    'label' => 'Asnières-sur-Seine, Île-de-France',
                    'shortLabel' => 'Asnières-sur-Seine (92)',
                ],
                [
                    'street' => null,
                    'locality' => null,
                    'postalCode' => '76190',
                    'adminLevel1' => 'Normandie',
                    'adminLevel2' => 'Les Hauts-de-Caux',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '49.6559762',
                    'longitude' => '0.7425663',
                    'key' => 'fr~normandie~les-hauts-de-caux~',
                    'label' => 'Les Hauts-de-Caux, France',
                    'shortLabel' => 'Les Hauts-de-Caux',
                ],
                [
                    'street' => null,
                    'locality' => null,
                    'postalCode' => '92100',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => 'Boulogne-Billancourt',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8275939',
                    'longitude' => '2.23714247',
                    'key' => 'fr~ile-de-france~boulogne-billancourt~',
                    'label' => 'Boulogne-Billancourt, France',
                    'shortLabel' => 'Boulogne-Billancourt',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideAdminArea2Cases
     */
    public function testAdminArea2(string $search, array $results): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/locations/mobilities', [
            'query' => [
                'search' => $search,
            ],
        ]);

        self::assertJsonContains($results);

        usleep(self::WAIT_BETWEEN_SEARCHES);
    }

    public function testNotFound(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/locations/mobilities', [
            'query' => [
                'search' => 'lsfhslqkfhslkfh',
            ],
        ]);

        self::assertJsonEquals([]);
    }

    public function testStoringLocationKeyLabel(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();

        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $locationKeyLabel = $em->getRepository(LocationKeyLabel::class)->findOneBy(['key' => 'fr~ile-de-france~val-de-marne~maisons-alfort']);
        $allLocationKeyLabel = $em->getRepository(LocationKeyLabel::class)->findAll();
        self::assertNull($locationKeyLabel);
        self::assertCount(5, $allLocationKeyLabel);

        // 2 - search
        $client->request('GET', '/locations/mobilities', [
            'query' => [
                'search' => 'Alfort',
            ],
        ]);
        self::assertResponseIsSuccessful();

        // 3 - after search
        $locationKeyLabel = $em->getRepository(LocationKeyLabel::class)->findOneBy(['key' => 'fr~ile-de-france~val-de-marne~maisons-alfort']);
        $allLocationKeyLabel = $em->getRepository(LocationKeyLabel::class)->findAll();
        self::assertNotNull($locationKeyLabel);
        self::assertCount(7, $allLocationKeyLabel);
    }
}
