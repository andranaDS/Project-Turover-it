<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221107000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD views_count INT NOT NULL, ADD applications_count INT NOT NULL');
        $this->addSql('CREATE TABLE user_share (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, shared_by_id INT DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_DC46602A76ED395 (user_id), INDEX IDX_DC466025489CD19 (shared_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_share ADD CONSTRAINT FK_DC46602A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_share ADD CONSTRAINT FK_DC466025489CD19 FOREIGN KEY (shared_by_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE company_picture CHANGE position position INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP views_count, DROP applications_count');
        $this->addSql('ALTER TABLE user_share DROP FOREIGN KEY FK_DC46602A76ED395');
        $this->addSql('ALTER TABLE user_share DROP FOREIGN KEY FK_DC466025489CD19');
        $this->addSql('DROP TABLE user_share');
        $this->addSql('ALTER TABLE company_picture CHANGE position position INT NOT NULL');
    }
}
