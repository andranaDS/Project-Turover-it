<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220421141217 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_8D93D6494231B919 ON user (status_updated_at)');
        $this->addSql('CREATE INDEX IDX_8D93D64980D3563A ON user (next_availability_at)');
        $this->addSql('ALTER TABLE user_data ADD cron_no_immediate_availability_exec_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_D772BFAA26498A2B ON user_data (cron_no_immediate_availability_exec_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_8D93D6494231B919 ON user');
        $this->addSql('DROP INDEX IDX_8D93D64980D3563A ON user');
        $this->addSql('DROP INDEX IDX_D772BFAA26498A2B ON user_data');
        $this->addSql('ALTER TABLE user_data DROP cron_no_immediate_availability_exec_at');
    }
}
