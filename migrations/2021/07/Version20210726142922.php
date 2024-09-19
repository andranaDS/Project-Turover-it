<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210726142922 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting ADD contracts LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP contract');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting ADD contract VARCHAR(12) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP contracts');
    }
}
