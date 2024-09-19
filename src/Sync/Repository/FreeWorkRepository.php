<?php

namespace App\Sync\Repository;

use App\Core\Entity\Location;
use App\Core\Util\Arrays;
use App\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class FreeWorkRepository extends ServiceEntityRepository
{
    private Connection $connection;
    private DenormalizerInterface $denormalizer;

    public function __construct(ManagerRegistry $registry, DenormalizerInterface $denormalizer)
    {
        parent::__construct($registry, User::class);
        $this->connection = $this->_em->getConnection();
        $this->denormalizer = $denormalizer;
    }

    public function findLocation(string $value): ?Location
    {
        $literalValue = $this->_em->getExpressionBuilder()->literal($value);

        $query = sprintf('
        SELECT *
        FROM (
                 (
                     SELECT
                        location_street as street,
                        location_locality as locality,
                        location_locality_slug as localitySlug,
                        location_sub_locality as subLocality,
                        location_postal_code as postalCode,
                        location_admin_level1 as adminLevel1,
                        location_admin_level1_slug as adminLevel1Slug,
                        location_admin_level2 as adminLevel2,
                        location_admin_level2_slug as adminLevel2Slug,
                        location_country as country,
                        location_country_code as countryCode,
                        location_latitude as latitude,
                        location_longitude as longitude,
                        location_value as value
                     FROM company
                     WHERE location_value = %s
                     LIMIT 0,1
                 )
                 UNION
                 (
                     SELECT
                        location_street as street,
                        location_locality as locality,
                        location_locality_slug as localitySlug,
                        location_sub_locality as subLocality,
                        location_postal_code as postalCode,
                        location_admin_level1 as adminLevel1,
                        location_admin_level1_slug as adminLevel1Slug,
                        location_admin_level2 as adminLevel2,
                        location_admin_level2_slug as adminLevel2Slug,
                        location_country as country,
                        location_country_code as countryCode,
                        location_latitude as latitude,
                        location_longitude as longitude,
                        location_value as value
                     FROM job_posting
                     WHERE location_value = %s
                     LIMIT 0,1
                 )
                 UNION
                 (
                     SELECT
                        location_street as street,
                        location_locality as locality,
                        location_locality_slug as localitySlug,
                        location_sub_locality as subLocality,
                        location_postal_code as postalCode,
                        location_admin_level1 as adminLevel1,
                        location_admin_level1_slug as adminLevel1Slug,
                        location_admin_level2 as adminLevel2,
                        location_admin_level2_slug as adminLevel2Slug,
                        location_country as country,
                        location_country_code as countryCode,
                        location_latitude as latitude,
                        location_longitude as longitude,
                        location_value as value
                     FROM user_mobility
                     WHERE location_value = %s
                     LIMIT 0,1
                 )
                 UNION
                 (
                     SELECT
                        location_street as street,
                        location_locality as locality,
                        location_locality_slug as localitySlug,
                        location_sub_locality as subLocality,
                        location_postal_code as postalCode,
                        location_admin_level1 as adminLevel1,
                        location_admin_level1_slug as adminLevel1Slug,
                        location_admin_level2 as adminLevel2,
                        location_admin_level2_slug as adminLevel2Slug,
                        location_country as country,
                        location_country_code as countryCode,
                        location_latitude as latitude,
                        location_longitude as longitude,
                        location_value as value
                     FROM user
                     WHERE location_value = %s
                     LIMIT 0,1
                 )
             ) AS locations
        LIMIT 0,1', $literalValue, $literalValue, $literalValue, $literalValue);

        $stmt = $this->connection->prepare($query);
        $data = Arrays::first($stmt->executeQuery()->fetchAllAssociative());

        return null === $data ? null : $this->denormalizer->denormalize($data, Location::class);
    }
}
