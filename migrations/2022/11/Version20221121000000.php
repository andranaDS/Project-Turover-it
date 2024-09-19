<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221121000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE job_posting_recruiter_trace (id INT AUTO_INCREMENT NOT NULL, job_posting_id INT NOT NULL, recruiter_id INT DEFAULT NULL, ip VARCHAR(255) NOT NULL, read_at DATETIME NOT NULL, INDEX IDX_EEDDAAA1F09E15EB (job_posting_id), INDEX IDX_EEDDAAA1156BE243 (recruiter_id), INDEX IDX_EEDDAAA1E2DA3872 (read_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_posting_recruiter_trace ADD CONSTRAINT FK_EEDDAAA1F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_recruiter_trace ADD CONSTRAINT FK_EEDDAAA1156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE job_posting_recruiter_trace');
    }
}
