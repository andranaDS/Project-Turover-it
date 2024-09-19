<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220505125611 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE company_business_activity SET slug = "agence-web-communication" WHERE id = 1');
        $this->addSql('UPDATE company_business_activity SET slug = "cabinet-de-conseil" WHERE id = 2');
        $this->addSql('UPDATE company_business_activity SET slug = "cabinet-de-recrutement-placement" WHERE id = 3');
        $this->addSql('UPDATE company_business_activity SET slug = "commercial-independant" WHERE id = 4');
        $this->addSql('UPDATE company_business_activity SET slug = "dsi-client-final" WHERE id = 5');
        $this->addSql('UPDATE company_business_activity SET slug = "editeur-de-logiciels" WHERE id = 6');
        $this->addSql('UPDATE company_business_activity SET slug = "societe-de-portage" WHERE id = 7');
        $this->addSql('UPDATE company_business_activity SET slug = "sourcing-chasseur-de-tetes" WHERE id = 8');
        $this->addSql('UPDATE company_business_activity SET slug = "esn" WHERE id = 9');
        $this->addSql('UPDATE company_business_activity SET slug = "start-up" WHERE id = 10');
        $this->addSql('UPDATE company_business_activity SET slug = "centre-de-formation" WHERE id = 11');
        $this->addSql('UPDATE company_business_activity SET slug = "ecole-it-universite" WHERE id = 12');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE company_business_activity SET slug = ""');
    }
}
