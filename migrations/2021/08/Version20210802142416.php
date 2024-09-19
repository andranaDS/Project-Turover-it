<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210802142416 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting ADD min_annual_salary INT DEFAULT NULL, ADD max_annual_salary INT DEFAULT NULL, ADD min_daily_salary INT DEFAULT NULL, ADD max_daily_salary INT DEFAULT NULL, DROP min_salary, DROP max_salary');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting ADD min_salary INT DEFAULT NULL, ADD max_salary INT DEFAULT NULL, DROP min_annual_salary, DROP max_annual_salary, DROP min_daily_salary, DROP max_daily_salary');
    }
}
