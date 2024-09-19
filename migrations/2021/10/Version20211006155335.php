<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211006155335 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE contribution (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, user_status VARCHAR(16) DEFAULT NULL, contract_type VARCHAR(24) NOT NULL, location VARCHAR(24) NOT NULL, experience_year VARCHAR(24) NOT NULL, employer VARCHAR(24) NOT NULL, found_by VARCHAR(12) NOT NULL, on_call TINYINT(1) NOT NULL, annual_salary INT UNSIGNED DEFAULT NULL, bonus INT UNSIGNED DEFAULT NULL, daily_salary INT UNSIGNED DEFAULT NULL, telework_time_per_week INT UNSIGNED NOT NULL, contract_duration INT UNSIGNED NOT NULL, search_job_duration VARCHAR(24) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_EA351E15BE04EA9 (job_id), INDEX IDX_EA351E15B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contribution ADD CONSTRAINT FK_EA351E15BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE contribution ADD CONSTRAINT FK_EA351E15B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE contribution');
    }
}
