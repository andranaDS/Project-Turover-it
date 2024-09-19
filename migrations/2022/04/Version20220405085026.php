<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220405085026 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_4FBF094F39E6FA16 ON company (old_id)');
        $this->addSql('CREATE INDEX IDX_F4FB33A6AA9E377A ON trend (date)');
        $this->addSql('CREATE INDEX IDX_38E46E768790DBF3 ON user_document (default_resume)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_4FBF094F39E6FA16 ON company');
        $this->addSql('DROP INDEX IDX_F4FB33A6AA9E377A ON trend');
        $this->addSql('DROP INDEX IDX_38E46E768790DBF3 ON user_document');
    }
}
