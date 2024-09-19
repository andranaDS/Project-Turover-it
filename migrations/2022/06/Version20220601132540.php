<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220601132540 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE skill ADD displayed TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE skill ADD synonym_slugs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE skill DROP displayed');
        $this->addSql('ALTER TABLE skill DROP synonym_slugs');
    }
}
