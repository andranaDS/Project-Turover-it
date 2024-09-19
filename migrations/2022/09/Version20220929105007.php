<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220929105007 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD created_by_id INT DEFAULT NULL, ADD reference VARCHAR(255) DEFAULT NULL, ADD contact TINYTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B03A8386 FOREIGN KEY (created_by_id) REFERENCES recruiter (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B03A8386 ON user (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE recruiter DROP FOREIGN KEY FK_DE8633D8EF1A9D84');
        $this->addSql('DROP TABLE job_posting_soft_skill');
        $this->addSql('DROP TABLE recruiter_notification');
        $this->addSql('ALTER TABLE job_posting DROP FOREIGN KEY FK_27C8EAE8F4BD7827');
        $this->addSql('ALTER TABLE job_posting DROP FOREIGN KEY FK_27C8EAE8B03A8386');
        $this->addSql('DROP INDEX IDX_27C8EAE8F4BD7827 ON job_posting');
        $this->addSql('ALTER TABLE job_posting DROP assigned_to_id, DROP duration_value, DROP duration_period, DROP application_email, DROP multicast, DROP status, DROP supply_entry_channel, DROP views_count, DROP days_online_count, DROP push_to_top, DROP pushed_to_top_count, DROP pushed_to_top_at, DROP quality, DROP received, DROP deleted_at');
        $this->addSql('ALTER TABLE job_posting ADD CONSTRAINT FK_27C8EAE8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_DE8633D8EF1A9D84 ON recruiter');
        $this->addSql('ALTER TABLE recruiter DROP notification_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B03A8386');
        $this->addSql('DROP INDEX IDX_8D93D649B03A8386 ON user');
        $this->addSql('ALTER TABLE user DROP created_by_id, DROP reference, DROP contact');
    }
}
