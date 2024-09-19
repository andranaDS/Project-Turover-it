<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221116144203 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search_recruiter_alert DROP FOREIGN KEY FK_37B447B39D37CF68');
        $this->addSql('DROP INDEX IDX_37B447B39D37CF68 ON job_posting_search_recruiter_alert');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_alert ADD business_activity_id INT DEFAULT NULL, DROP company_business_activity_id, DROP contracts, DROP min_annual_salary, DROP max_annual_salary');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_alert ADD CONSTRAINT FK_37B447B3C004B9FE FOREIGN KEY (business_activity_id) REFERENCES company_business_activity (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_37B447B3C004B9FE ON job_posting_search_recruiter_alert (business_activity_id)');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_favorite DROP FOREIGN KEY FK_29ABFAB29D37CF68');
        $this->addSql('DROP INDEX IDX_29ABFAB29D37CF68 ON job_posting_search_recruiter_favorite');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_favorite ADD business_activity_id INT DEFAULT NULL, DROP company_business_activity_id, DROP contracts, DROP min_annual_salary, DROP max_annual_salary');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_favorite ADD CONSTRAINT FK_29ABFAB2C004B9FE FOREIGN KEY (business_activity_id) REFERENCES company_business_activity (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_29ABFAB2C004B9FE ON job_posting_search_recruiter_favorite (business_activity_id)');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_log DROP FOREIGN KEY FK_D2CEB1F59D37CF68');
        $this->addSql('DROP INDEX IDX_D2CEB1F59D37CF68 ON job_posting_search_recruiter_log');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_log ADD business_activity_id INT DEFAULT NULL, DROP company_business_activity_id, DROP contracts, DROP min_annual_salary, DROP max_annual_salary');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_log ADD CONSTRAINT FK_D2CEB1F5C004B9FE FOREIGN KEY (business_activity_id) REFERENCES company_business_activity (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_D2CEB1F5C004B9FE ON job_posting_search_recruiter_log (business_activity_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499393F8FE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499393F8FE');
        $this->addSql('DROP INDEX idx_8d93d6499393f8fe ON user');
        $this->addSql('CREATE INDEX FK_8D93D6499393F8FE ON user (partner_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_favorite DROP FOREIGN KEY FK_29ABFAB2C004B9FE');
        $this->addSql('DROP INDEX IDX_29ABFAB2C004B9FE ON job_posting_search_recruiter_favorite');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_favorite ADD contracts LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD min_annual_salary INT DEFAULT NULL, ADD max_annual_salary INT DEFAULT NULL, CHANGE business_activity_id company_business_activity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_favorite ADD CONSTRAINT FK_29ABFAB29D37CF68 FOREIGN KEY (company_business_activity_id) REFERENCES company_business_activity (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_29ABFAB29D37CF68 ON job_posting_search_recruiter_favorite (company_business_activity_id)');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_alert DROP FOREIGN KEY FK_37B447B3C004B9FE');
        $this->addSql('DROP INDEX IDX_37B447B3C004B9FE ON job_posting_search_recruiter_alert');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_alert ADD contracts LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD min_annual_salary INT DEFAULT NULL, ADD max_annual_salary INT DEFAULT NULL, CHANGE business_activity_id company_business_activity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_alert ADD CONSTRAINT FK_37B447B39D37CF68 FOREIGN KEY (company_business_activity_id) REFERENCES company_business_activity (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_37B447B39D37CF68 ON job_posting_search_recruiter_alert (company_business_activity_id)');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_log DROP FOREIGN KEY FK_D2CEB1F5C004B9FE');
        $this->addSql('DROP INDEX IDX_D2CEB1F5C004B9FE ON job_posting_search_recruiter_log');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_log ADD contracts LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD min_annual_salary INT DEFAULT NULL, ADD max_annual_salary INT DEFAULT NULL, CHANGE business_activity_id company_business_activity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_log ADD CONSTRAINT FK_D2CEB1F59D37CF68 FOREIGN KEY (company_business_activity_id) REFERENCES company_business_activity (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_D2CEB1F59D37CF68 ON job_posting_search_recruiter_log (company_business_activity_id)');
    }
}
