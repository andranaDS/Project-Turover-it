<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220506121114 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job ADD rome_code VARCHAR(16) DEFAULT NULL, ADD ogr_code VARCHAR(16) DEFAULT NULL, ADD ogr_label VARCHAR(128) DEFAULT NULL');
        $this->addSql('CREATE TABLE feed_rss (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(16) NOT NULL, partner VARCHAR(32) NOT NULL, ga_tag VARCHAR(2500) DEFAULT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_97A6F30A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feed_rss_blacklist_company (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, feed_rss_id INT DEFAULT NULL, INDEX IDX_200699E1979B1AD6 (company_id), INDEX IDX_200699E16BF2F4D2 (feed_rss_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feed_rss_forbidden_word (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feed_rss_blacklist_company ADD CONSTRAINT FK_200699E1979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE feed_rss_blacklist_company ADD CONSTRAINT FK_200699E16BF2F4D2 FOREIGN KEY (feed_rss_id) REFERENCES feed_rss (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job DROP rome_code, DROP ogr_code, DROP ogr_label');
        $this->addSql('ALTER TABLE feed_rss_blacklist_company DROP FOREIGN KEY FK_200699E16BF2F4D2');
        $this->addSql('DROP TABLE feed_rss');
        $this->addSql('DROP TABLE feed_rss_blacklist_company');
        $this->addSql('DROP TABLE feed_rss_forbidden_word');
    }
}
