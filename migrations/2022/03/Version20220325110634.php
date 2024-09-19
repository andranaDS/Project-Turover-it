<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220325110634 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_27C8EAE839E6FA16 ON job_posting (old_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A188FE64 ON user (nickname)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_27C8EAE839E6FA16 ON job_posting');
        $this->addSql('DROP INDEX IDX_8D93D649A188FE64 ON user');
    }
}
