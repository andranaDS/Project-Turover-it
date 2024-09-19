<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211217164636 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contribution CHANGE employer employer VARCHAR(24) DEFAULT NULL, CHANGE on_call on_call TINYINT(1) DEFAULT NULL, CHANGE remote_days_per_week remote_days_per_week INT UNSIGNED DEFAULT NULL, CHANGE contract_duration contract_duration INT UNSIGNED DEFAULT NULL, CHANGE search_job_duration search_job_duration INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE on_call_percentage_free on_call_percentage_free INT UNSIGNED DEFAULT NULL, CHANGE on_call_percentage_work on_call_percentage_work INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE on_call_percentage_free on_call_percentage_free INT UNSIGNED DEFAULT NULL, CHANGE on_call_percentage_work on_call_percentage_work INT UNSIGNED DEFAULT NULL, CHANGE average_search_job_duration_work average_search_job_duration_work INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE average_search_job_duration_free average_search_job_duration_free INT UNSIGNED DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contribution CHANGE employer employer VARCHAR(24) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE on_call on_call TINYINT(1) NOT NULL, CHANGE remote_days_per_week remote_days_per_week INT UNSIGNED NOT NULL, CHANGE contract_duration contract_duration INT UNSIGNED NOT NULL, CHANGE search_job_duration search_job_duration INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE on_call_percentage_free on_call_percentage_free INT UNSIGNED NOT NULL, CHANGE on_call_percentage_work on_call_percentage_work INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE on_call_percentage_free on_call_percentage_free INT UNSIGNED NOT NULL, CHANGE on_call_percentage_work on_call_percentage_work INT UNSIGNED NOT NULL, CHANGE average_search_job_duration_work average_search_job_duration_work INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE job_contribution_statistics CHANGE average_search_job_duration_free average_search_job_duration_free INT UNSIGNED NOT NULL');
    }
}
