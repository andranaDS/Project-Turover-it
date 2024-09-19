<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221031152424 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE hr_flow_log (id INT AUTO_INCREMENT NOT NULL, user_document_id INT DEFAULT NULL, created_at DATETIME NOT NULL, log VARCHAR(255) DEFAULT NULL, INDEX IDX_D515BD4D6A24B1A2 (user_document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hr_flow_log ADD CONSTRAINT FK_D515BD4D6A24B1A2 FOREIGN KEY (user_document_id) REFERENCES user_document (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE hr_flow_log DROP FOREIGN KEY FK_D515BD4D6A24B1A2');
        $this->addSql('DROP TABLE hr_flow_log');
    }
}
