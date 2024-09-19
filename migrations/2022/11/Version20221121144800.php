<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221121144800 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE job_posting_trace to job_posting_user_trace');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE job_posting_user_trace to job_posting_trace');
    }
}
