<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210824131950 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE location_key_label (`key` VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(`key`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD location_short_label VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting ADD location_short_label VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting_search_location ADD location_short_label VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD location_short_label VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_mobility ADD location_short_label VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE location_key_label');
        $this->addSql('ALTER TABLE company DROP location_short_label');
        $this->addSql('ALTER TABLE job_posting DROP location_short_label');
        $this->addSql('ALTER TABLE job_posting_search_location DROP location_short_label');
        $this->addSql('ALTER TABLE user DROP location_short_label');
        $this->addSql('ALTER TABLE user_mobility DROP location_short_label');
    }
}
