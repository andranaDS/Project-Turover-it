<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220330082623 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_38E46E7660C1D0A0 ON user_document (resume)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_38E46E7660C1D0A0 ON user_document');
    }
}
