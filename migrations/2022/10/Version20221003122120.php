<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221003122120 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company_data ADD job_postings_intercontract_total_count INT NOT NULL, ADD job_postings_intercontract_published_count INT NOT NULL, ADD users_intercontract_count INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company_data DROP job_postings_intercontract_total_count, DROP job_postings_intercontract_published_count, DROP users_intercontract_count');
    }
}
