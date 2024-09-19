<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220406150329 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_data (id INT AUTO_INCREMENT NOT NULL, last_activity_at DATETIME DEFAULT NULL, last_forum_activity_at DATETIME DEFAULT NULL, INDEX IDX_D772BFAA149885F6 (last_activity_at), INDEX IDX_D772BFAAFB00E480 (last_forum_activity_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP INDEX IDX_8D93D649149885F6 ON user');
        $this->addSql('INSERT INTO user_data (id, last_activity_at, last_forum_activity_at) SELECT id, last_activity_at, last_forum_activity_at FROM user');
        $this->addSql('ALTER TABLE user ADD data_id INT DEFAULT NULL, DROP last_activity_at, DROP last_forum_activity_at');
        $this->addSql('UPDATE user SET data_id = id');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64937F5A13C FOREIGN KEY (data_id) REFERENCES user_data (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64937F5A13C ON user (data_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64937F5A13C');
        $this->addSql('DROP INDEX UNIQ_8D93D64937F5A13C ON user');
        $this->addSql('ALTER TABLE user ADD last_activity_at DATETIME DEFAULT NULL, ADD last_forum_activity_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE user u SET
            u.last_activity_at = (SELECT ud.last_activity_at FROM user_data ud WHERE ud.id = u.data_id),
            u.last_forum_activity_at = (SELECT ud.last_forum_activity_at FROM user_data ud WHERE ud.id = u.data_id)
        ');
        $this->addSql('ALTER TABLE user DROP data_id');
        $this->addSql('DROP TABLE user_data');
        $this->addSql('CREATE INDEX IDX_8D93D649149885F6 ON user (last_activity_at)');
    }
}
