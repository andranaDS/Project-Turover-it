<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221014102430 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE job_posting_recruiter_alert (id INT AUTO_INCREMENT NOT NULL, company_business_activity_id INT DEFAULT NULL, recruiter_id INT DEFAULT NULL, active TINYINT(1) DEFAULT NULL, search_keywords VARCHAR(255) DEFAULT NULL, remote_mode LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', published_since VARCHAR(25) DEFAULT NULL, contracts LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', min_annual_salary INT DEFAULT NULL, max_annual_salary INT DEFAULT NULL, min_daily_salary INT DEFAULT NULL, max_daily_salary INT DEFAULT NULL, currency VARCHAR(5) DEFAULT NULL, min_duration INT DEFAULT NULL, max_duration INT DEFAULT NULL, intercontract_only TINYINT(1) DEFAULT NULL, starts_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F5509C829D37CF68 (company_business_activity_id), INDEX IDX_F5509C82156BE243 (recruiter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_posting_recruiter_favorite (id INT AUTO_INCREMENT NOT NULL, company_business_activity_id INT DEFAULT NULL, recruiter_id INT DEFAULT NULL, search_keywords VARCHAR(255) DEFAULT NULL, remote_mode LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', published_since VARCHAR(25) DEFAULT NULL, contracts LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', min_annual_salary INT DEFAULT NULL, max_annual_salary INT DEFAULT NULL, min_daily_salary INT DEFAULT NULL, max_daily_salary INT DEFAULT NULL, currency VARCHAR(5) DEFAULT NULL, min_duration INT DEFAULT NULL, max_duration INT DEFAULT NULL, intercontract_only TINYINT(1) DEFAULT NULL, starts_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C9A94F629D37CF68 (company_business_activity_id), INDEX IDX_C9A94F62156BE243 (recruiter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_posting_recruiter_search (id INT AUTO_INCREMENT NOT NULL, company_business_activity_id INT DEFAULT NULL, recruiter_id INT DEFAULT NULL, search_keywords VARCHAR(255) DEFAULT NULL, remote_mode LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', published_since VARCHAR(25) DEFAULT NULL, contracts LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', min_annual_salary INT DEFAULT NULL, max_annual_salary INT DEFAULT NULL, min_daily_salary INT DEFAULT NULL, max_daily_salary INT DEFAULT NULL, currency VARCHAR(5) DEFAULT NULL, min_duration INT DEFAULT NULL, max_duration INT DEFAULT NULL, intercontract_only TINYINT(1) DEFAULT NULL, starts_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5BC766579D37CF68 (company_business_activity_id), INDEX IDX_5BC76657156BE243 (recruiter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_posting_recruiter_alert ADD CONSTRAINT FK_F5509C829D37CF68 FOREIGN KEY (company_business_activity_id) REFERENCES company_business_activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_recruiter_alert ADD CONSTRAINT FK_F5509C82156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_recruiter_favorite ADD CONSTRAINT FK_C9A94F629D37CF68 FOREIGN KEY (company_business_activity_id) REFERENCES company_business_activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_recruiter_favorite ADD CONSTRAINT FK_C9A94F62156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_recruiter_search ADD CONSTRAINT FK_5BC766579D37CF68 FOREIGN KEY (company_business_activity_id) REFERENCES company_business_activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_recruiter_search ADD CONSTRAINT FK_5BC76657156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE job_posting_recruiter_alert');
        $this->addSql('DROP TABLE job_posting_recruiter_favorite');
        $this->addSql('DROP TABLE job_posting_recruiter_search');
    }
}
