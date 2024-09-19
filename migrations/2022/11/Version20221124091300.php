<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221124091300 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search_recruiter_alert ADD ip VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_favorite ADD ip VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_log ADD ip VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search_recruiter_favorite DROP ip');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_alert DROP ip');
        $this->addSql('ALTER TABLE job_posting_search_recruiter_log DROP ip');
    }
}
