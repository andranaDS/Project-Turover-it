<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210915093849 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_formation CHANGE diploma_title diploma_title VARCHAR(255) DEFAULT NULL, CHANGE diploma_level diploma_level INT DEFAULT NULL, CHANGE school school VARCHAR(255) DEFAULT NULL, CHANGE diploma_year diploma_year INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_formation CHANGE diploma_title diploma_title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE diploma_level diploma_level INT NOT NULL, CHANGE school school VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE diploma_year diploma_year INT NOT NULL');
    }
}
