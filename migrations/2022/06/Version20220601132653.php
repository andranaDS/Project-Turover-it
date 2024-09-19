<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220601132653 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_996BCC5A4AF38FD1 ON forum_post (deleted_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_996BCC5A4AF38FD1 ON forum_post');
    }
}
