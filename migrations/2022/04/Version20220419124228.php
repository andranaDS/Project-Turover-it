<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220419124228 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_EE9EE98D46B2649C ON company_data (job_postings_published_count)');
        $this->addSql('ALTER TABLE user_data ADD cron_alert_missions_exec_at DATETIME DEFAULT NULL, ADD cron_no_job_posting_search_exec_at DATETIME DEFAULT NULL, ADD cron_profile_uncompleted_exec_at DATETIME DEFAULT NULL, ADD cron_profile_not_visible_exec_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_D772BFAA4665A57A ON user_data (cron_alert_missions_exec_at)');
        $this->addSql('CREATE INDEX IDX_D772BFAA29861139 ON user_data (cron_no_job_posting_search_exec_at)');
        $this->addSql('CREATE INDEX IDX_D772BFAA18066885 ON user_data (cron_profile_uncompleted_exec_at)');
        $this->addSql('CREATE INDEX IDX_D772BFAAE1182B65 ON user_data (cron_profile_not_visible_exec_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_EE9EE98D46B2649C ON company_data');
        $this->addSql('DROP INDEX IDX_D772BFAA4665A57A ON user_data');
        $this->addSql('DROP INDEX IDX_D772BFAA29861139 ON user_data');
        $this->addSql('DROP INDEX IDX_D772BFAA18066885 ON user_data');
        $this->addSql('DROP INDEX IDX_D772BFAAE1182B65 ON user_data');
        $this->addSql('ALTER TABLE user_data DROP cron_alert_missions_exec_at, DROP cron_no_job_posting_search_exec_at, DROP cron_profile_uncompleted_exec_at, cron_profile_not_visible_exec_at');
    }
}
