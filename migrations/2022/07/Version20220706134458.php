<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220706134458 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE mailjet_unsubscribe_log (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, email VARCHAR(180) DEFAULT NULL, payload LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', unsubscribed TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97A6F30A989D9B62 ON feed_rss (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE mailjet_unsubscribe_log');
        $this->addSql('DROP INDEX UNIQ_97A6F30A989D9B62 ON feed_rss');
    }
}
