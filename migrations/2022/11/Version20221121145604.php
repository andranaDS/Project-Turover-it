<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221121145604 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE company_features_usage (id INT AUTO_INCREMENT NOT NULL, search_display_array_complete TINYINT(1) NOT NULL, search_display_array_complete_at DATETIME DEFAULT NULL, search_display_list_complete TINYINT(1) NOT NULL, search_display_list_complete_at DATETIME DEFAULT NULL, search_boolean_complete TINYINT(1) NOT NULL, search_boolean_complete_at DATETIME DEFAULT NULL, search_query_complete TINYINT(1) NOT NULL, search_query_complete_at DATETIME DEFAULT NULL, search_job_complete TINYINT(1) NOT NULL, search_job_complete_at DATETIME DEFAULT NULL, search_location_complete TINYINT(1) NOT NULL, search_location_complete_at DATETIME DEFAULT NULL, search_folder_complete TINYINT(1) NOT NULL, search_folder_complete_at DATETIME DEFAULT NULL, search_order_complete TINYINT(1) NOT NULL, search_order_complete_at DATETIME DEFAULT NULL, search_availability_and_language_complete TINYINT(1) NOT NULL, search_availability_and_language_complete_at DATETIME DEFAULT NULL, user_cart_complete TINYINT(1) NOT NULL, user_cart_complete_at DATETIME DEFAULT NULL, user_favorite_complete TINYINT(1) NOT NULL, user_favorite_complete_at DATETIME DEFAULT NULL, user_hide_complete TINYINT(1) NOT NULL, user_hide_complete_at DATETIME DEFAULT NULL, user_download_resume_complete TINYINT(1) NOT NULL, user_download_resume_complete_at DATETIME DEFAULT NULL, user_comment_complete TINYINT(1) NOT NULL, user_comment_complete_at DATETIME DEFAULT NULL, user_folder_complete TINYINT(1) NOT NULL, user_folder_complete_at DATETIME DEFAULT NULL, user_job_posting_complete TINYINT(1) NOT NULL, user_job_posting_complete_at DATETIME DEFAULT NULL, user_email_transfer_complete TINYINT(1) NOT NULL, user_email_transfer_complete_at DATETIME DEFAULT NULL, user_email_send_complete TINYINT(1) NOT NULL, user_email_send_complete_at DATETIME DEFAULT NULL, user_multiple_folder_complete TINYINT(1) NOT NULL, user_multiple_folder_complete_at DATETIME DEFAULT NULL, user_multiple_export_complete TINYINT(1) NOT NULL, user_multiple_export_complete_at DATETIME DEFAULT NULL, user_alert_complete TINYINT(1) NOT NULL, user_alert_complete_at DATETIME DEFAULT NULL, job_posting_free_work_complete TINYINT(1) NOT NULL, job_posting_free_work_complete_at DATETIME DEFAULT NULL, job_posting_turnover_complete TINYINT(1) NOT NULL, job_posting_turnover_complete_at DATETIME DEFAULT NULL, job_posting_public_complete TINYINT(1) NOT NULL, job_posting_public_complete_at DATETIME DEFAULT NULL, job_posting_internal_complete TINYINT(1) NOT NULL, job_posting_internal_complete_at DATETIME DEFAULT NULL, intercontract_search_by_company_complete TINYINT(1) NOT NULL, intercontract_search_by_company_complete_at DATETIME DEFAULT NULL, intercontract_publish_complete TINYINT(1) NOT NULL, intercontract_publish_complete_at DATETIME DEFAULT NULL, intercontract_only_complete TINYINT(1) NOT NULL, intercontract_only_complete_at DATETIME DEFAULT NULL, company_publish_complete TINYINT(1) NOT NULL, company_publish_complete_at DATETIME DEFAULT NULL, company_log_complete TINYINT(1) NOT NULL, company_log_complete_at DATETIME DEFAULT NULL, export_job_posting_publish_complete TINYINT(1) NOT NULL, export_job_posting_publish_complete_at DATETIME DEFAULT NULL, export_user_log_and_download_complete TINYINT(1) NOT NULL, export_user_log_and_download_complete_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD features_usage_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FECEE8036 FOREIGN KEY (features_usage_id) REFERENCES company_features_usage (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094FECEE8036 ON company (features_usage_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FECEE8036');
        $this->addSql('DROP TABLE company_features_usage');
        $this->addSql('DROP INDEX UNIQ_4FBF094FECEE8036 ON company');
        $this->addSql('ALTER TABLE company DROP features_usage_id');
    }
}
