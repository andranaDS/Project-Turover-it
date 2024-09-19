<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210902144326 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ADD location_locality_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level1_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level2_slug VARCHAR(255) DEFAULT NULL, ADD location_latitude NUMERIC(11, 7) DEFAULT NULL, ADD location_longitude NUMERIC(11, 7) DEFAULT NULL, DROP location_label, DROP location_coords, DROP location_short_label');
        $this->addSql('ALTER TABLE job_posting ADD location_locality_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level1_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level2_slug VARCHAR(255) DEFAULT NULL, ADD location_latitude NUMERIC(11, 7) DEFAULT NULL, ADD location_longitude NUMERIC(11, 7) DEFAULT NULL, DROP location_label, DROP location_coords, DROP location_short_label');
        $this->addSql('ALTER TABLE job_posting_search_location ADD location_locality_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level1_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level2_slug VARCHAR(255) DEFAULT NULL, ADD location_latitude NUMERIC(11, 7) DEFAULT NULL, ADD location_longitude NUMERIC(11, 7) DEFAULT NULL, DROP location_label, DROP location_coords, DROP location_short_label');
        $this->addSql('ALTER TABLE user ADD location_locality_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level1_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level2_slug VARCHAR(255) DEFAULT NULL, ADD location_latitude NUMERIC(11, 7) DEFAULT NULL, ADD location_longitude NUMERIC(11, 7) DEFAULT NULL, DROP location_label, DROP location_coords, DROP location_short_label');
        $this->addSql('ALTER TABLE user_mobility ADD location_locality_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level1_slug VARCHAR(255) DEFAULT NULL, ADD location_admin_level2_slug VARCHAR(255) DEFAULT NULL, ADD location_latitude NUMERIC(11, 7) DEFAULT NULL, ADD location_longitude NUMERIC(11, 7) DEFAULT NULL, DROP location_label, DROP location_coords, DROP location_short_label');
        $this->addSql('alter table job_posting modify contracts longtext null comment \'(DC2Type:json)\' after end_date');
        $this->addSql('alter table job_posting modify min_annual_salary int null after contracts');
        $this->addSql('alter table job_posting modify max_annual_salary int null after min_annual_salary');
        $this->addSql('alter table job_posting modify min_daily_salary int null after max_annual_salary');
        $this->addSql('alter table job_posting modify max_daily_salary int null after min_daily_salary');
        $this->addSql('alter table job_posting modify slug varchar(255) not null after title');
        $this->addSql('alter table job_posting modify location_value varchar(255) null after location_longitude');
        $this->addSql('alter table job_posting modify location_locality_slug varchar(255) null after location_locality');
        $this->addSql('alter table job_posting modify location_admin_level1_slug varchar(255) null after location_admin_level1');
        $this->addSql('alter table job_posting modify location_admin_level2_slug varchar(255) null after location_admin_level2');
        $this->addSql(' alter table company modify excerpt longtext null after description');
        $this->addSql('alter table company modify directory tinyint(1) not null after deleted_at');
        $this->addSql('alter table company modify location_value varchar(255) null after location_longitude');
        $this->addSql('alter table company modify old_id int null after location_value');
        $this->addSql('alter table company modify location_locality_slug varchar(255) null after location_locality');
        $this->addSql('alter table company modify location_admin_level1_slug varchar(255) null after location_admin_level1');
        $this->addSql('alter table company modify location_admin_level2_slug varchar(255) null after location_admin_level2');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ADD location_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_coords VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_short_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_locality_slug, DROP location_admin_level1_slug, DROP location_admin_level2_slug, DROP location_latitude, DROP location_longitude');
        $this->addSql('ALTER TABLE job_posting ADD location_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_coords VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_short_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_locality_slug, DROP location_admin_level1_slug, DROP location_admin_level2_slug, DROP location_latitude, DROP location_longitude');
        $this->addSql('ALTER TABLE job_posting_search_location ADD location_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_coords VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_short_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_locality_slug, DROP location_admin_level1_slug, DROP location_admin_level2_slug, DROP location_latitude, DROP location_longitude');
        $this->addSql('ALTER TABLE user ADD location_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_coords VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_short_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_locality_slug, DROP location_admin_level1_slug, DROP location_admin_level2_slug, DROP location_latitude, DROP location_longitude');
        $this->addSql('ALTER TABLE user_mobility ADD location_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_coords VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_short_label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_locality_slug, DROP location_admin_level1_slug, DROP location_admin_level2_slug, DROP location_latitude, DROP location_longitude');
    }
}
