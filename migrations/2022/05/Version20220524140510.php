<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220524140510 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE job_posting SET application_contact = NULL, application_url = NULL WHERE application_type = 'turnover'");
        $this->addSql("UPDATE job_posting SET application_contact = NULL WHERE application_type = 'url'");
        $this->addSql("UPDATE job_posting SET application_url = NULL WHERE application_type = 'contact'");
    }

    public function down(Schema $schema): void
    {
    }
}
