<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221104110000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, recruiter_id INT DEFAULT NULL, job_posting_id INT DEFAULT NULL, application_id INT DEFAULT NULL, created_at DATETIME NOT NULL, event VARCHAR(255) NOT NULL, read_at DATETIME DEFAULT NULL, variables LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_BF5476CA156BE243 (recruiter_id), INDEX IDX_BF5476CAF09E15EB (job_posting_id), INDEX IDX_BF5476CA3E030ACD (application_id), INDEX IDX_BF5476CA8B8E8428 (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA3E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA156BE243');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAF09E15EB');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA3E030ACD');
        $this->addSql('DROP TABLE notification');
    }
}
