<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221110133544 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE folder CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(50) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_BF5476CA8B8E8428 ON notification');
        $this->addSql('CREATE INDEX IDX_BF5476CA3BAE0AA7 ON notification (event)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_BF5476CA3BAE0AA7 ON notification');
        $this->addSql('CREATE INDEX IDX_BF5476CA8B8E8428 ON notification (created_at)');
        $this->addSql('ALTER TABLE folder CHANGE name name VARCHAR(250) DEFAULT NULL, CHANGE type type VARCHAR(50) NOT NULL');
    }
}
