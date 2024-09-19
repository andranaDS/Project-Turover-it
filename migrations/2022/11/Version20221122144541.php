<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221122144541 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE insurance_company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(55) NOT NULL, slug VARCHAR(255) NOT NULL, locales LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD insurance_company_id INT DEFAULT NULL, ADD naf_code VARCHAR(5) NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649ECB24509 FOREIGN KEY (insurance_company_id) REFERENCES insurance_company (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8D93D649ECB24509 ON user (insurance_company_id)');
        $this->addSql('ALTER TABLE user ADD insurance_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD insurance TINYINT(1) NOT NULL, ADD insurance_expired_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE insurance_company');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649ECB24509');
        $this->addSql('DROP TABLE insurance_company');
        $this->addSql('DROP INDEX IDX_8D93D649ECB24509 ON user');
        $this->addSql('ALTER TABLE user DROP insurance_number');
        $this->addSql('ALTER TABLE user DROP insurance, DROP insurance_expired_at');
    }
}
