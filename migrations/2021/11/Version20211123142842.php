<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211123142842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE feed (id INT AUTO_INCREMENT NOT NULL, job_application TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feed_user (id INT AUTO_INCREMENT NOT NULL, feed_id INT NOT NULL, user_id INT NOT NULL, favorite TINYINT(1) NOT NULL, view_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D043D1E51A5BC03 (feed_id), INDEX IDX_D043D1EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, feed_id INT NOT NULL, content LONGTEXT DEFAULT NULL, content_html LONGTEXT DEFAULT NULL, content_json LONGTEXT DEFAULT NULL, document VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B6BD307FF675F31B (author_id), INDEX IDX_B6BD307F51A5BC03 (feed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feed_user ADD CONSTRAINT FK_D043D1E51A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id)');
        $this->addSql('ALTER TABLE feed_user ADD CONSTRAINT FK_D043D1EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F51A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id)');
        $this->addSql('ALTER TABLE user ADD unread_messages_count INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE feed_user DROP FOREIGN KEY FK_D043D1E51A5BC03');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F51A5BC03');
        $this->addSql('DROP TABLE feed');
        $this->addSql('DROP TABLE feed_user');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE user DROP unread_messages_count');
    }
}
