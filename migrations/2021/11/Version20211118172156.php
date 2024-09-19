<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211118172156 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_profile_views (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, count INT NOT NULL, date DATE NOT NULL, INDEX IDX_F33267DFA76ED395 (user_id), INDEX IDX_F33267DFAA9E377A (date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_profile_views ADD CONSTRAINT FK_F33267DFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_profile_views');
    }
}
