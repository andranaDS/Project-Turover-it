<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210803140213 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search DROP max_annual_salary, DROP max_daily_salary');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search ADD max_annual_salary INT DEFAULT NULL, ADD max_daily_salary INT DEFAULT NULL');
    }
}
