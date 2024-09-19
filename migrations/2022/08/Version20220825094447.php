<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220825094447 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE recruiter (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, site_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, username VARCHAR(180) DEFAULT NULL, gender VARCHAR(10) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', enabled TINYINT(1) NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, email_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) DEFAULT NULL, main TINYINT(1) NOT NULL, job VARCHAR(255) DEFAULT NULL, terms_of_service TINYINT(1) NOT NULL, terms_of_service_accepted_at DATETIME DEFAULT NULL, webinar_viewed_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DE8633D8979B1AD6 (company_id), INDEX IDX_DE8633D8F6BD1646 (site_id), INDEX IDX_DE8633D8B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recruiter_access_token (id INT AUTO_INCREMENT NOT NULL, recruiter_id INT NOT NULL, value VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, expired_at DATETIME NOT NULL, INDEX IDX_A221DA18156BE243 (recruiter_id), INDEX IDX_A221DA181D775834 (value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recruiter_job (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_694309E4989D9B62 (slug), INDEX IDX_694309E4979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recruiter ADD CONSTRAINT FK_DE8633D8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE recruiter ADD CONSTRAINT FK_DE8633D8F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE recruiter ADD CONSTRAINT FK_DE8633D8B03A8386 FOREIGN KEY (created_by_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE recruiter_access_token ADD CONSTRAINT FK_A221DA18156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E4979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD country_code VARCHAR(255) DEFAULT NULL, ADD registration_number VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting DROP FOREIGN KEY FK_27C8EAE8979B1AD6');
        $this->addSql('ALTER TABLE job_posting ADD CONSTRAINT FK_27C8EAE8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE recruiter DROP FOREIGN KEY FK_DE8633D8B03A8386');
        $this->addSql('ALTER TABLE recruiter_access_token DROP FOREIGN KEY FK_A221DA18156BE243');
        $this->addSql('ALTER TABLE recruiter DROP FOREIGN KEY FK_DE8633D8F6BD1646');
        $this->addSql('DROP TABLE recruiter');
        $this->addSql('DROP TABLE recruiter_access_token');
        $this->addSql('DROP TABLE recruiter_job');
        $this->addSql('DROP TABLE site');
        $this->addSql('ALTER TABLE company DROP country_code, DROP registration_number, CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE job_posting DROP FOREIGN KEY FK_27C8EAE8979B1AD6');
        $this->addSql('ALTER TABLE job_posting ADD CONSTRAINT FK_27C8EAE8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
    }
}
