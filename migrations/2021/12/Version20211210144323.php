<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211210144323 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ban_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, author_id INT DEFAULT NULL, reason LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_FE47009BA76ED395 (user_id), UNIQUE INDEX UNIQ_FE47009BF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ban_user ADD CONSTRAINT FK_FE47009BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ban_user ADD CONSTRAINT FK_FE47009BF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE forum_post ADD ip VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD banned TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ban_user');
        $this->addSql('ALTER TABLE forum_post DROP ip');
        $this->addSql('ALTER TABLE user DROP banned');
    }
}
