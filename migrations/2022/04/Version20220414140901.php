<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220414140901 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_A45BDDC143B9FE3C ON application (step)');
        $this->addSql('CREATE INDEX IDX_27C8EAE8683C6017E0D4FDE1 ON job_posting (published, published_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_A45BDDC143B9FE3C ON application');
        $this->addSql('DROP INDEX IDX_27C8EAE8683C6017E0D4FDE1 ON job_posting');
    }
}
