<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211027151440 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_3171117697CA47AB ON sync_log (mode)');
        $this->addSql('CREATE INDEX IDX_317111764D58BEDB ON sync_log (requested_at)');
        $this->addSql('CREATE INDEX IDX_31711176E284468 ON sync_log (entity)');
        $this->addSql('CREATE INDEX IDX_3171117639E6FA16 ON sync_log (old_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_3171117697CA47AB ON sync_log');
        $this->addSql('DROP INDEX IDX_317111764D58BEDB ON sync_log');
        $this->addSql('DROP INDEX IDX_31711176E284468 ON sync_log');
        $this->addSql('DROP INDEX IDX_3171117639E6FA16 ON sync_log');
    }
}
