<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221031150000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE folder (id INT AUTO_INCREMENT NOT NULL, recruiter_id INT DEFAULT NULL, name VARCHAR(250) DEFAULT NULL, type VARCHAR(50) NOT NULL, users_count INT DEFAULT 0 NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_ECA209CD156BE243 (recruiter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE folder_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, folder_id INT DEFAULT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_940CF05CA76ED395 (user_id), INDEX IDX_940CF05C162CB942 (folder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CD156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE folder_user ADD CONSTRAINT FK_940CF05CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE folder_user ADD CONSTRAINT FK_940CF05C162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE folder_user DROP FOREIGN KEY FK_940CF05C162CB942');
        $this->addSql('DROP TABLE folder');
        $this->addSql('DROP TABLE folder_user');
    }
}
