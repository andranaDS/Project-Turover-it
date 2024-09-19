<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220919000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE job_posting_template (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, contracts LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', min_annual_salary INT DEFAULT NULL, max_annual_salary INT DEFAULT NULL, min_daily_salary INT DEFAULT NULL, max_daily_salary INT DEFAULT NULL, currency VARCHAR(5) DEFAULT NULL, duration INT DEFAULT NULL, duration_value INT DEFAULT NULL, duration_period VARCHAR(10) DEFAULT NULL, application_type VARCHAR(255) DEFAULT NULL, application_email VARCHAR(255) DEFAULT NULL, application_url VARCHAR(255) DEFAULT NULL, application_contact VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, location_street_number VARCHAR(255) DEFAULT NULL, location_street_name VARCHAR(255) DEFAULT NULL, location_sub_locality VARCHAR(255) DEFAULT NULL, location_locality VARCHAR(255) DEFAULT NULL, location_locality_slug VARCHAR(255) DEFAULT NULL, location_postal_code VARCHAR(255) DEFAULT NULL, location_admin_level1 VARCHAR(255) DEFAULT NULL, location_admin_level1_slug VARCHAR(255) DEFAULT NULL, location_admin_level2 VARCHAR(255) DEFAULT NULL, location_admin_level2_slug VARCHAR(255) DEFAULT NULL, location_country VARCHAR(255) DEFAULT NULL, location_country_code VARCHAR(255) DEFAULT NULL, location_value VARCHAR(255) DEFAULT NULL, location_latitude NUMERIC(11, 7) DEFAULT NULL, location_longitude NUMERIC(11, 7) DEFAULT NULL, INDEX IDX_CF05A4DBF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_posting_template_skill (job_posting_template_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_8476FC1E5D9C924C (job_posting_template_id), INDEX IDX_8476FC1E5585C142 (skill_id), PRIMARY KEY(job_posting_template_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_posting_template_soft_skill (job_posting_template_id INT NOT NULL, soft_skill_id INT NOT NULL, INDEX IDX_10DDC92C5D9C924C (job_posting_template_id), INDEX IDX_10DDC92C88034CA4 (soft_skill_id), PRIMARY KEY(job_posting_template_id, soft_skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_posting_template ADD CONSTRAINT FK_CF05A4DBF675F31B FOREIGN KEY (author_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE job_posting_template_skill ADD CONSTRAINT FK_8476FC1E5D9C924C FOREIGN KEY (job_posting_template_id) REFERENCES job_posting_template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_template_skill ADD CONSTRAINT FK_8476FC1E5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_template_soft_skill ADD CONSTRAINT FK_10DDC92C5D9C924C FOREIGN KEY (job_posting_template_id) REFERENCES job_posting_template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_template_soft_skill ADD CONSTRAINT FK_10DDC92C88034CA4 FOREIGN KEY (soft_skill_id) REFERENCES soft_skill (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_template_skill DROP FOREIGN KEY FK_8476FC1E5D9C924C');
        $this->addSql('ALTER TABLE job_posting_template_soft_skill DROP FOREIGN KEY FK_10DDC92C5D9C924C');
        $this->addSql('DROP TABLE job_posting_template');
        $this->addSql('DROP TABLE job_posting_template_skill');
        $this->addSql('DROP TABLE job_posting_template_soft_skill');
    }
}
