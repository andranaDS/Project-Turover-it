<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\LocationKeyLabel;
use Doctrine\Persistence\ObjectManager;

class LocationKeyLabelFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $d) {
            $locationKeyLabel = (new LocationKeyLabel($d['key']))
                ->setLabel($d['label'])
                ->setData($d['data'])
            ;
            $manager->persist($locationKeyLabel);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        return [
            [
                'label' => 'Paris, Île-de-France',
                'key' => 'fr~ile-de-france~~paris',
                'data' => [
                    'street' => null,
                    'locality' => 'Paris',
                    'postalCode' => '75000',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => null,
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8566969',
                    'longitude' => '2.3514616',
                    'key' => 'fr~ile-de-france~~paris',
                    'label' => 'Paris, Île-de-France',
                    'shortLabel' => 'Paris (75)',
                ],
            ],
            [
                'label' => 'Le Touquet-Paris-Plage, Hauts-de-France',
                'key' => 'fr~hauts-de-france~~le-touquet-paris-plage',
                'data' => [
                    'street' => null,
                    'locality' => 'Le Touquet-Paris-Plage',
                    'postalCode' => '62520',
                    'adminLevel1' => 'Hauts-de-France',
                    'adminLevel2' => 'Pas-de-Calais',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '50.5211202',
                    'longitude' => '1.5909325',
                    'key' => 'fr~hauts-de-france~pas-de-calais~le-touquet-paris-plage',
                    'label' => 'Le Touquet-Paris-Plage, Hauts-de-France',
                    'shortLabel' => 'Le Touquet-Paris-Plage (62)',
                ],
            ],
            [
                'label' => 'Nouvelle-Aquitaine, France',
                'key' => 'fr~nouvelle-aquitaine',
                'data' => [
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
            ],
            [
                'label' => 'Seyssinet-Pariset, Auvergne-Rhône-Alpes',
                'key' => 'fr~auvergne-rhone-alpes~seyssinet-pariset',
                'data' => [
                    'street' => null,
                    'locality' => 'Seyssinet-Pariset',
                    'postalCode' => '38170',
                    'adminLevel1' => 'Auvergne-Rhône-Alpes',
                    'adminLevel2' => 'Isère',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '45.1790768',
                    'longitude' => '5.6899617',
                    'key' => 'fr~auvergne-rhone-alpes~isere~seyssinet-pariset',
                    'label' => 'Seyssinet-Pariset, Auvergne-Rhône-Alpes',
                    'shortLabel' => 'Seyssinet-Pariset (38)',
                ],
            ],
            [
                'label' => 'Bordeaux, Nouvelle-Aquitaine',
                'key' => 'fr~nouvelle-aquitaine~~bordeaux',
                'data' => [
                    'street' => null,
                    'locality' => 'Bordeaux',
                    'postalCode' => '33000',
                    'adminLevel1' => 'Nouvelle-Aquitaine',
                    'adminLevel2' => null,
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '44.841225',
                    'longitude' => '-0.5800364',
                    'key' => 'fr~nouvelle-aquitaine~~bordeaux',
                    'label' => 'Bordeaux, Nouvelle-Aquitaine',
                    'shortLabel' => 'Bordeaux (33)',
                ],
            ],
        ];
    }

    public function getTestData(): array
    {
        return [
            [
                'label' => 'Paris, Île-de-France',
                'key' => 'fr~ile-de-france~~paris',
                'data' => [
                    'street' => null,
                    'locality' => 'Paris',
                    'postalCode' => '75000',
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => null,
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8566969',
                    'longitude' => '2.3514616',
                    'key' => 'fr~ile-de-france~~paris',
                    'label' => 'Paris, Île-de-France',
                    'shortLabel' => 'Paris (75)',
                ],
            ],
            [
                'label' => 'Le Touquet-Paris-Plage, Hauts-de-France',
                'key' => 'fr~hauts-de-france~~le-touquet-paris-plage',
                'data' => [
                    'street' => null,
                    'locality' => 'Le Touquet-Paris-Plage',
                    'postalCode' => '62520',
                    'adminLevel1' => 'Hauts-de-France',
                    'adminLevel2' => 'Pas-de-Calais',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '50.5211202',
                    'longitude' => '1.5909325',
                    'key' => 'fr~hauts-de-france~pas-de-calais~le-touquet-paris-plage',
                    'label' => 'Le Touquet-Paris-Plage, Hauts-de-France',
                    'shortLabel' => 'Le Touquet-Paris-Plage (62)',
                ],
            ],
            [
                'label' => 'Nouvelle-Aquitaine, France',
                'key' => 'fr~nouvelle-aquitaine',
                'data' => [
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
            ],
            [
                'label' => 'Seyssinet-Pariset, Auvergne-Rhône-Alpes',
                'key' => 'fr~auvergne-rhone-alpes~seyssinet-pariset',
                'data' => [
                    'street' => null,
                    'locality' => 'Seyssinet-Pariset',
                    'postalCode' => '38170',
                    'adminLevel1' => 'Auvergne-Rhône-Alpes',
                    'adminLevel2' => 'Isère',
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '45.1790768',
                    'longitude' => '5.6899617',
                    'key' => 'fr~auvergne-rhone-alpes~isere~seyssinet-pariset',
                    'label' => 'Seyssinet-Pariset, Auvergne-Rhône-Alpes',
                    'shortLabel' => 'Seyssinet-Pariset (38)',
                ],
            ],
            [
                'label' => 'Bordeaux, Nouvelle-Aquitaine',
                'key' => 'fr~nouvelle-aquitaine~~bordeaux',
                'data' => [
                    'street' => null,
                    'locality' => 'Bordeaux',
                    'postalCode' => '33000',
                    'adminLevel1' => 'Nouvelle-Aquitaine',
                    'adminLevel2' => null,
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '44.841225',
                    'longitude' => '-0.5800364',
                    'key' => 'fr~nouvelle-aquitaine~~bordeaux',
                    'label' => 'Bordeaux, Nouvelle-Aquitaine',
                    'shortLabel' => 'Bordeaux (33)',
                ],
            ],
        ];
    }
}
