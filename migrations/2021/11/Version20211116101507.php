<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211116101507 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_contribution_statistics ADD remote_days_per_week_distribution_free LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD remote_days_per_week_distribution_work LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', DROP telework_time_distribution_free, DROP telework_time_distribution_work');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_contribution_statistics ADD telework_time_distribution_free LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', ADD telework_time_distribution_work LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', DROP remote_days_per_week_distribution_free, DROP remote_days_per_week_distribution_work');
    }
}
