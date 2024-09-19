<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211026160122 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE job_contribution_statistics (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, day DATE NOT NULL, telework_time_distribution_free LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', telework_time_distribution_work LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', experience_year_distribution_free LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', experience_year_distribution_work LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', employer_distribution_work LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', contract_duration_distribution_free LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', on_call_percentage_free INT UNSIGNED NOT NULL, on_call_percentage_work INT UNSIGNED NOT NULL, search_job_duration_free VARCHAR(24) NOT NULL, search_job_duration_work VARCHAR(24) NOT NULL, average_daily_salary_directly INT UNSIGNED NOT NULL, average_daily_salary_with_intermediary INT UNSIGNED NOT NULL, average_annual_salary_final_client INT UNSIGNED NOT NULL, average_annual_salary_non_final_client INT UNSIGNED NOT NULL, salary_experience_distribution_free LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', salary_experience_distribution_work LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', salary_experience_location_distribution_free LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', salary_experience_location_distribution_work LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_9FC089CDBE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_contribution_statistics ADD CONSTRAINT FK_9FC089CDBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE contribution CHANGE telework_time_per_week telework_time INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE job_contribution_statistics');
        $this->addSql('ALTER TABLE contribution CHANGE telework_time telework_time_per_week INT UNSIGNED NOT NULL');
    }
}
