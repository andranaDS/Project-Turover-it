<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221124134051 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE company_favorite to company_user_favorite');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE company_user_favorite to company_favorite');
    }
}