<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210925151122 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE config (name VARCHAR(255) NOT NULL, value VARCHAR(255) NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO config VALUES ("app_sync_execute_companies_last_datetime", null)');
        $this->addSql('INSERT INTO config VALUES ("app_sync_execute_job_postings_last_datetime", null)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE config');
    }
}
