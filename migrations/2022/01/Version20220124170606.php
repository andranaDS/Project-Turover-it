<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220124170606 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search ADD old_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD old_freelance_info_profile_ids LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD old_carriere_info_profile_ids LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search DROP old_id');
        $this->addSql('ALTER TABLE user DROP old_freelance_info_profile_ids, DROP old_carriere_info_profile_ids');
    }
}
