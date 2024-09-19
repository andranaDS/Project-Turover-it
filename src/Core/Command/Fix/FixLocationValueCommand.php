<?php

namespace App\Core\Command\Fix;

use App\JobPosting\Entity\JobPosting;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixLocationValueCommand extends Command
{
    protected static $defaultName = 'app:fix:location-value';

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $locationsUpdatedCount = 0;
        $jobPostingIds = [];

        $query = "SELECT * FROM ((SELECT id,
						'company' as entity,
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
                     WHERE location_value = 'La Défense, Hauts-de-Seine, Île-de-France'
                 )
                 UNION
                 (
                     SELECT

                     id,
                     'job_posting' as entity,
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
                     WHERE location_value = 'La Défense, Hauts-de-Seine, Île-de-France'
                 )
                 UNION
                 (
                     SELECT

						id,
						'user_mobility' as entity,
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
                     WHERE location_value = 'La Défense, Hauts-de-Seine, Île-de-France'
                 )
                 UNION
                 (
                     SELECT

                     	id,
						'user' as entity,
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
                     WHERE location_value = 'La Défense, Hauts-de-Seine, Île-de-France'
                 )
             ) AS locations
";

        $stmt = $this->entityManager->getConnection()->prepare($query);

        $updateQuery = "UPDATE %s
            set location_street = NULL,
                location_locality = 'La Défense',
                location_locality_slug = 'la-defense',
                location_sub_locality = 'Quartier Gambetta',
                location_postal_code = '92400',
                location_admin_level1 = 'Île-de-France',
                location_admin_level1_slug = 'ile-de-france',
                location_admin_level2 = 'Hauts-de-Seine',
                location_admin_level2_slug = 'hauts-de-seine',
                location_country = 'France',
                location_country_code = 'FR',
                location_latitude = '48.8910080',
                location_longitude = '2.2412078'
            where id = %d";

        foreach ($stmt->executeQuery()->fetchAllAssociative() as $data) {
            ++$locationsUpdatedCount;
            $query = sprintf($updateQuery, $data['entity'], $data['id']);

            $this->entityManager->getConnection()->executeStatement($query);

            if ('job_posting' === $data['entity']) {
                $jobPostingIds[] = $data['id'];
            }
        }
        $this->entityManager->getConnection()->executeStatement($query);

        if (!empty($jobPostingIds)) {
            $jobPostings = $this->entityManager->getRepository(JobPosting::class)->findById($jobPostingIds);
            $now = Carbon::now();

            foreach ($jobPostings as $jobPosting) {
                /* @var JobPosting $jobPosting */
                $jobPosting->setUpdatedAt($now);
            }

            $this->entityManager->flush();
        }

        $io->success("$locationsUpdatedCount locations updated");

        return Command::SUCCESS;
    }
}
