<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210901135236 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sync_log (id INT AUTO_INCREMENT NOT NULL, entity VARCHAR(100) NOT NULL, old_id INT DEFAULT NULL, new_id INT DEFAULT NULL, source VARCHAR(10) NOT NULL, mode VARCHAR(10) DEFAULT NULL, in_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', out_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', errors LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, requested_at DATETIME DEFAULT NULL, processed_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD directory TINYINT(1) NOT NULL, ADD old_id INT DEFAULT NULL, ADD location_value VARCHAR(255) DEFAULT NULL, CHANGE size size VARCHAR(32) DEFAULT NULL, CHANGE creation_year creation_year INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting ADD location_value VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting_search_location ADD location_value VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD location_value VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_mobility ADD location_value VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sync_log');
        $this->addSql('ALTER TABLE company DROP directory, DROP old_id, DROP location_value, CHANGE size size VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creation_year creation_year INT NOT NULL');
        $this->addSql('ALTER TABLE job_posting DROP location_value');
        $this->addSql('ALTER TABLE job_posting_search_location DROP location_value');
        $this->addSql('ALTER TABLE user DROP location_value');
        $this->addSql('ALTER TABLE user_mobility DROP location_value');
    }
}
