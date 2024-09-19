<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221010122500 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE company_recruiter_favorite (id INT AUTO_INCREMENT NOT NULL, recruiter_id INT DEFAULT NULL, company_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9250392E156BE243 (recruiter_id), INDEX IDX_9250392E979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_recruiter_favorite ADD CONSTRAINT FK_9250392E156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_recruiter_favorite ADD CONSTRAINT FK_9250392E979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE company_recruiter_favorite');
    }
}
