<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211117133152 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contribution CHANGE search_job_duration search_job_duration INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE job_contribution_statistics ADD average_search_job_duration_free INT UNSIGNED NOT NULL, ADD average_search_job_duration_work INT UNSIGNED NOT NULL, DROP search_job_duration_free, DROP search_job_duration_work');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contribution CHANGE search_job_duration search_job_duration VARCHAR(24) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD search_job_duration_free VARCHAR(24) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD search_job_duration_work VARCHAR(24) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP average_search_job_duration_free, DROP average_search_job_duration_work');
        $this->addSql('ALTER TABLE job_contribution_statistics ADD search_job_duration_free VARCHAR(24) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD search_job_duration_work VARCHAR(24) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP average_search_job_duration_free, DROP average_search_job_duration_work');
    }
}
