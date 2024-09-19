<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220830150030 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE company_soft_skill (company_id INT NOT NULL, soft_skill_id INT NOT NULL, INDEX IDX_ABC40542979B1AD6 (company_id), INDEX IDX_ABC4054288034CA4 (soft_skill_id), PRIMARY KEY(company_id, soft_skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_soft_skill ADD CONSTRAINT FK_ABC40542979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_soft_skill ADD CONSTRAINT FK_ABC4054288034CA4 FOREIGN KEY (soft_skill_id) REFERENCES soft_skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD directory_turnover TINYINT(1) NOT NULL, ADD legal_name VARCHAR(255) DEFAULT NULL, ADD baseline VARCHAR(100) DEFAULT NULL, ADD video VARCHAR(255) DEFAULT NULL, ADD intracommunity_vat VARCHAR(255) DEFAULT NULL, ADD billing_email VARCHAR(180) DEFAULT NULL, CHANGE directory directory_free_work TINYINT(1) NOT NULL');
        $this->addSql('UPDATE company SET legal_name = name');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE company_soft_skill');
        $this->addSql('ALTER TABLE company ADD directory TINYINT(1) NOT NULL, DROP directory_free_work, DROP directory_turnover, DROP legal_name, DROP baseline, DROP video, DROP intracommunity_vat, DROP billing_email');
    }
}
