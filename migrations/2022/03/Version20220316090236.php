<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220316090236 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE company_business_activity SET name = "ESN" WHERE name = "SSII"');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE company_business_activity SET name = "SSII" WHERE name = "ESN"');
    }
}
