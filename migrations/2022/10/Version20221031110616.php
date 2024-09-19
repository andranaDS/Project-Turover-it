<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221031110616 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting DROP short_description');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting ADD short_description LONGTEXT DEFAULT NULL');
    }
}
