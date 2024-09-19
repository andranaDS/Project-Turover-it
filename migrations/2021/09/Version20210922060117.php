<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210922060117 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting ADD duration INT DEFAULT NULL, ADD starts_at DATETIME DEFAULT NULL, ADD old_id INT DEFAULT NULL, DROP employment_time, DROP work_time, DROP work_hours, DROP work_hours_interval, DROP duration_days, DROP duration_months, DROP duration_years, DROP start_date, DROP end_date, CHANGE duration_renewable renewable TINYINT(1) DEFAULT NULL, CHANGE start_availability reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting_search ADD min_duration INT DEFAULT NULL, ADD max_duration INT DEFAULT NULL, DROP employment_time, DROP min_duration_days, DROP max_duration_days');
        $this->addSql('ALTER TABLE sync_log ADD warnings LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE sync_log modify warnings LONGTEXT NULL COMMENT \'(DC2Type:json)\' AFTER errors;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting ADD employment_time VARCHAR(12) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD work_time VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD work_hours INT DEFAULT NULL, ADD work_hours_interval VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD duration_days INT DEFAULT NULL, ADD duration_months INT DEFAULT NULL, ADD duration_years INT DEFAULT NULL, ADD end_date DATETIME DEFAULT NULL, DROP duration, DROP old_id, CHANGE renewable duration_renewable TINYINT(1) DEFAULT NULL, CHANGE reference start_availability VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE starts_at start_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE job_posting_search ADD employment_time VARCHAR(12) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD min_duration_days INT DEFAULT NULL, ADD max_duration_days INT DEFAULT NULL, DROP min_duration, DROP max_duration');
        $this->addSql('ALTER TABLE sync_log DROP warnings');
    }
}
