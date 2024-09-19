<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220926130403 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE recruiter_notification (id INT AUTO_INCREMENT NOT NULL, new_application_email TINYINT(1) NOT NULL, new_application_notification TINYINT(1) NOT NULL, end_broadcast_job_posting_email TINYINT(1) NOT NULL, end_broadcast_job_posting_notification TINYINT(1) NOT NULL, daily_resume_email TINYINT(1) NOT NULL, daily_job_posting_email TINYINT(1) NOT NULL, job_posting_publish_atsemail TINYINT(1) NOT NULL, job_posting_publish_atsnotification TINYINT(1) NOT NULL, newsletter_email TINYINT(1) NOT NULL, subscription_end_email TINYINT(1) NOT NULL, subscription_end_notification TINYINT(1) NOT NULL, invoice_email TINYINT(1) NOT NULL, invoice_notification TINYINT(1) NOT NULL, order_email TINYINT(1) NOT NULL, order_notification TINYINT(1) NOT NULL, subscription_end_reminder_email TINYINT(1) NOT NULL, subscription_end_reminder_notification TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recruiter ADD notification_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recruiter ADD CONSTRAINT FK_DE8633D8EF1A9D84 FOREIGN KEY (notification_id) REFERENCES recruiter_notification (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DE8633D8EF1A9D84 ON recruiter (notification_id)');
    }

    public function down(Schema $schema): void
    {
    }
}
