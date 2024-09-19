<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221010081141 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company_data ADD users_visible_count INT NOT NULL, CHANGE users_intercontract_count users_count INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company_data ADD users_intercontract_count INT NOT NULL, DROP users_count, DROP users_visible_count');
    }
}
