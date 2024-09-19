<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211103161651 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD visibility TINYINT(1) DEFAULT NULL, CHANGE next_availability_at next_availability_at DATE DEFAULT NULL, CHANGE availability_updated_at status_updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP visibility, CHANGE next_availability_at next_availability_at DATETIME DEFAULT NULL, CHANGE status_updated_at availability_updated_at DATETIME DEFAULT NULL');
    }
}
