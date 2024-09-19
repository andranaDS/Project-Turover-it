<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211011092122 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD form_step VARCHAR(30) DEFAULT NULL, ADD profile_completed TINYINT(1) NOT NULL, CHANGE company_registration_number company_registration_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_formation ADD self_taught TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP form_step, DROP profile_completed, CHANGE company_registration_number company_registration_number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_formation DROP self_taught');
    }
}
