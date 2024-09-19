<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220929145709 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO recruiter_notification (id, new_application_email, new_application_notification, end_broadcast_job_posting_email, end_broadcast_job_posting_notification, daily_resume_email, daily_job_posting_email, job_posting_publish_atsemail, job_posting_publish_atsnotification, newsletter_email, subscription_end_email, subscription_end_notification, invoice_email, invoice_notification, order_email, order_notification, subscription_end_reminder_email, subscription_end_reminder_notification, created_at, updated_at) SELECT id, "1", "1", "0", "1", "1", "0", "0", "1", "0", "0", "1", "0", "1", "0", "1", "1", "1", "2022-09-01 00:00:00", "2022-09-01 00:00:00" FROM recruiter');
        $this->addSql('UPDATE recruiter AS r SET r.notification_id = r.id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE recruiter AS r SET r.notification_id = NULL');
        $this->addSql('DELETE FROM recruiter_notification WHERE 1=1');
    }
}
