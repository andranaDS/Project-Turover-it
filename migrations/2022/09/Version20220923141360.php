<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220923141360 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE job_posting_soft_skill (job_posting_id INT NOT NULL, soft_skill_id INT NOT NULL, INDEX IDX_389B09E9F09E15EB (job_posting_id), INDEX IDX_389B09E988034CA4 (soft_skill_id), PRIMARY KEY(job_posting_id, soft_skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_posting_soft_skill ADD CONSTRAINT FK_389B09E9F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_soft_skill ADD CONSTRAINT FK_389B09E988034CA4 FOREIGN KEY (soft_skill_id) REFERENCES soft_skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting ADD assigned_to_id INT DEFAULT NULL, ADD duration_value INT DEFAULT NULL, ADD duration_period VARCHAR(10) DEFAULT NULL, ADD multicast TINYINT(1) NOT NULL, ADD status VARCHAR(50) DEFAULT NULL, ADD supply_entry_channel VARCHAR(50) DEFAULT NULL, ADD views_count INT NOT NULL, ADD days_online_count INT NOT NULL, ADD push_to_top TINYINT(1) DEFAULT NULL, ADD pushed_to_top_count INT NOT NULL, ADD pushed_to_top_at DATETIME DEFAULT NULL, ADD quality INT DEFAULT NULL, ADD application_email VARCHAR(50) NOT NULL, ADD received TINYINT(1) DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, CHANGE created_by_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting ADD CONSTRAINT FK_27C8EAE8F675F31B FOREIGN KEY (author_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE job_posting ADD CONSTRAINT FK_27C8EAE8F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES recruiter (id)');
        $this->addSql('CREATE INDEX IDX_27C8EAE8F675F31B ON job_posting (author_id)');
        $this->addSql('CREATE INDEX IDX_27C8EAE8F4BD7827 ON job_posting (assigned_to_id)');
        $this->addSql('ALTER TABLE job_posting DROP FOREIGN KEY FK_27C8EAE8F675F31B');
        $this->addSql('ALTER TABLE job_posting DROP FOREIGN KEY FK_27C8EAE8B03A8386');
        $this->addSql('DROP INDEX IDX_27C8EAE8F675F31B ON job_posting');
        $this->addSql('DROP INDEX IDX_27C8EAE8B03A8386 ON job_posting');
        $this->addSql('ALTER TABLE job_posting CHANGE author_id created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting ADD CONSTRAINT FK_27C8EAE8B03A8386 FOREIGN KEY (created_by_id) REFERENCES recruiter (id)');
        $this->addSql('CREATE INDEX IDX_27C8EAE8B03A8386 ON job_posting (created_by_id)');
        $this->addSql('UPDATE job_posting SET duration_period = "year", duration_value = 1 WHERE duration = 12');
        $this->addSql('UPDATE job_posting SET duration_period = "year", duration_value = 2 WHERE duration = 24');
        $this->addSql('UPDATE job_posting SET duration_period = "year", duration_value = 3 WHERE duration = 36');
        $this->addSql('UPDATE job_posting SET duration_period = "year", duration_value = 4 WHERE duration = 48');
        $this->addSql('UPDATE job_posting SET duration_period = "month", duration_value = duration WHERE duration_period IS NULL');
        $this->addSql('ALTER TABLE job_posting CHANGE application_email application_email VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting CHANGE multicast multicast TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
