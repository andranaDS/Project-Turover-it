<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220505073058 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company_business_activity ADD slug VARCHAR(128) NOT NULL');
        $this->addSql('CREATE INDEX IDX_2ED3D82D91C0F487 ON job_posting_search (active_alert)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company_business_activity DROP slug');
        $this->addSql('DROP INDEX IDX_2ED3D82D91C0F487 ON job_posting_search');
    }
}
