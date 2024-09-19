<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221128170000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE job_posting_share (id INT AUTO_INCREMENT NOT NULL, job_posting_id INT NOT NULL, shared_by_id INT DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_73B6A1DBF09E15EB (job_posting_id), INDEX IDX_73B6A1DB5489CD19 (shared_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_posting_share ADD CONSTRAINT FK_73B6A1DBF09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_share ADD CONSTRAINT FK_73B6A1DB5489CD19 FOREIGN KEY (shared_by_id) REFERENCES recruiter (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE job_posting_share');
    }
}
