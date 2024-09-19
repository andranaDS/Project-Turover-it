<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211105112150 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job ADD available_for_contribution TINYINT(1) NOT NULL, ADD name_for_contribution VARCHAR(255) DEFAULT NULL, ADD name_for_contribution_slug VARCHAR(255) DEFAULT NULL, ADD available_for_user TINYINT(1) NOT NULL, ADD name_for_user VARCHAR(255) DEFAULT NULL, ADD name_for_user_slug VARCHAR(255) DEFAULT NULL, CHANGE category_id category_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE slug slug VARCHAR(255) DEFAULT NULL, CHANGE salary_description salary_description LONGTEXT DEFAULT NULL, CHANGE salary_formation salary_formation LONGTEXT DEFAULT NULL, CHANGE salary_standard_mission salary_standard_mission LONGTEXT DEFAULT NULL, CHANGE salary_seo_meta_title salary_seo_meta_title VARCHAR(70) DEFAULT NULL, CHANGE salary_seo_meta_description salary_seo_meta_description VARCHAR(160) DEFAULT NULL, CHANGE faq_price faq_price LONGTEXT DEFAULT NULL, CHANGE faq_definition faq_definition LONGTEXT DEFAULT NULL, CHANGE faq_missions faq_missions LONGTEXT DEFAULT NULL, CHANGE faq_skills faq_skills LONGTEXT DEFAULT NULL, CHANGE faq_profile faq_profile LONGTEXT DEFAULT NULL, CHANGE faq_seo_meta_title faq_seo_meta_title VARCHAR(70) DEFAULT NULL, CHANGE faq_seo_meta_description faq_seo_meta_description VARCHAR(160) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job DROP available_for_contribution, DROP name_for_contribution, DROP name_for_contribution_slug, DROP available_for_user, DROP name_for_user, DROP name_for_user_slug, CHANGE category_id category_id INT NOT NULL, CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE slug slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE salary_description salary_description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE salary_formation salary_formation LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE salary_standard_mission salary_standard_mission LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE salary_seo_meta_title salary_seo_meta_title VARCHAR(70) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE salary_seo_meta_description salary_seo_meta_description VARCHAR(160) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE faq_price faq_price LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE faq_definition faq_definition LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE faq_missions faq_missions LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE faq_skills faq_skills LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE faq_profile faq_profile LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE faq_seo_meta_title faq_seo_meta_title VARCHAR(70) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE faq_seo_meta_description faq_seo_meta_description VARCHAR(160) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
