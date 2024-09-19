<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210830134657 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD deleted_at DATETIME DEFAULT NULL, CHANGE email email VARCHAR(180) DEFAULT NULL, CHANGE last_activity_at last_activity_at DATETIME DEFAULT NULL, CHANGE last_forum_activity_at last_forum_activity_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP deleted_at, CHANGE email email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE last_activity_at last_activity_at DATETIME NOT NULL, CHANGE last_forum_activity_at last_forum_activity_at DATETIME NOT NULL');
    }
}
