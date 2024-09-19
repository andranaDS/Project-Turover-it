<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221130142029 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE folder_user DROP deleted_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE folder_user ADD deleted_at DATETIME DEFAULT NULL');
    }
}
