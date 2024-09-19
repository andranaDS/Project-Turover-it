<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Entity\Location;
use App\Core\Util\Arrays;
use App\User\Entity\User;
use App\User\Entity\UserMobility;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserMobilitiesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];
    private array $locations = [];

    private DenormalizerInterface $denormalizer;

    public function __construct(string $env, DenormalizerInterface $denormalizer)
    {
        parent::__construct($env);

        $this->denormalizer = $denormalizer;
    }

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            if ($user->getProfileJobTitle()) {
                $this->users[$user->getEmail()] = $user;
            }
        }

        // fetch locations
        $this->locations = array_map(static function (string $location) {
            return json_decode($location, true);
        }, [
            'lyon' => '{"subLocality":null,"locality":"Lyon","localitySlug":"lyon","postalCode":null,"adminLevel1":"Auvergne-Rhône-Alpes","adminLevel1Slug":"auvergne-rhone-alpes","adminLevel2":"Métropole de Lyon","adminLevel2Slug":"metropole-de-lyon","country":"France","countryCode":"FR","latitude":"45.7578137","longitude":"4.8320114"}',
            'paris' => '{"subLocality":"Paris","locality":"Paris","localitySlug":"paris","postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.8588897","longitude":"2.3200410217201"}',
            'idf' => '{"subLocality":null,"locality":null,"localitySlug":null,"postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.6443057","longitude":"2.7537863"}',
        ]);

        foreach ($this->getData() as $d) {
            $mobility = (new UserMobility())
                ->setUser($d['user'])
                ->setLocation($this->denormalizer->denormalize($d['location'], Location::class))
            ;
            $manager->persist($mobility);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        foreach ($this->users as $user) {
            if (0 !== mt_rand(0, 4)) {
                $mobilityCount = mt_rand(1, 3);
                for ($i = 0; $i < $mobilityCount; ++$i) {
                    $data[] = [
                        'user' => $user,
                        'location' => Arrays::getRandom($this->locations),
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
                'user' => $this->users['claude.monet@free-work.fr'],
                'location' => $this->locations['paris'],
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'location' => $this->locations['lyon'],
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'location' => $this->locations['paris'],
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
