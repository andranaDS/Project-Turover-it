<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211115090832 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contribution CHANGE user_status user_company_status VARCHAR(16) DEFAULT NULL, CHANGE bonus variable_annual_salary INT UNSIGNED DEFAULT NULL, CHANGE telework_time remote_days_per_week INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contribution CHANGE user_company_status user_status VARCHAR(16) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE variable_annual_salary bonus INT UNSIGNED DEFAULT NULL, CHANGE remote_days_per_week telework_time INT UNSIGNED NOT NULL');
    }
}
