<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220304113105 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE salary_experience_distribution_free salary_experience_distribution_free LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE salary_experience_distribution_work salary_experience_distribution_work LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE salary_experience_location_distribution_free salary_experience_location_distribution_free LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE salary_experience_location_distribution_work salary_experience_location_distribution_work LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE salary_experience_distribution_free salary_experience_distribution_free LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE salary_experience_distribution_work salary_experience_distribution_work LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE salary_experience_location_distribution_free salary_experience_location_distribution_free LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE salary_experience_location_distribution_work salary_experience_location_distribution_work LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
    }
}
