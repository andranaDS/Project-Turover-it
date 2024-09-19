<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211130153209 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE average_daily_salary_directly average_daily_salary_directly INT UNSIGNED DEFAULT NULL, CHANGE average_daily_salary_with_intermediary average_daily_salary_with_intermediary INT UNSIGNED DEFAULT NULL, CHANGE average_annual_salary_final_client average_annual_salary_final_client INT UNSIGNED DEFAULT NULL, CHANGE average_annual_salary_non_final_client average_annual_salary_non_final_client INT UNSIGNED DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE average_daily_salary_directly average_daily_salary_directly INT UNSIGNED NOT NULL, CHANGE average_daily_salary_with_intermediary average_daily_salary_with_intermediary INT UNSIGNED NOT NULL, CHANGE average_annual_salary_final_client average_annual_salary_final_client INT UNSIGNED NOT NULL, CHANGE average_annual_salary_non_final_client average_annual_salary_non_final_client INT UNSIGNED NOT NULL');
    }
}
