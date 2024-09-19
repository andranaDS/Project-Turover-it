<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220103145404 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('TRUNCATE TABLE sync_log');
        $this->addSql('ALTER TABLE sync_log ADD new_company_id INT DEFAULT NULL, ADD new_job_posting_id INT DEFAULT NULL, ADD old_company_id INT DEFAULT NULL, ADD old_job_posting_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sync_log ADD CONSTRAINT FK_317111764AA4F91A FOREIGN KEY (new_company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE sync_log ADD CONSTRAINT FK_31711176C71DAEF9 FOREIGN KEY (new_job_posting_id) REFERENCES job_posting (id)');
        $this->addSql('CREATE INDEX IDX_317111762BB82D88 ON sync_log (old_company_id)');
        $this->addSql('CREATE INDEX IDX_317111764AA4F91A ON sync_log (new_company_id)');
        $this->addSql('CREATE INDEX IDX_31711176861616A9 ON sync_log (old_job_posting_id)');
        $this->addSql('CREATE INDEX IDX_31711176C71DAEF9 ON sync_log (new_job_posting_id)');
        $this->addSql('ALTER TABLE sync_log MODIFY old_company_id int NULL AFTER id');
        $this->addSql('ALTER TABLE sync_log MODIFY new_company_id int NULL AFTER old_company_id');
        $this->addSql('ALTER TABLE sync_log MODIFY old_job_posting_id int NULL AFTER new_company_id');
        $this->addSql('ALTER TABLE sync_log MODIFY new_job_posting_id int NULL AFTER old_job_posting_id');
        $this->addSql('ALTER TABLE sync_log DROP COLUMN entity, DROP COLUMN old_id, DROP COLUMN new_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_21BF9426989D9B62 ON forum_category');
        $this->addSql('DROP INDEX IDX_853478CC989D9B62 ON forum_topic');
        $this->addSql('ALTER TABLE sync_log DROP FOREIGN KEY FK_317111764AA4F91A');
        $this->addSql('ALTER TABLE sync_log DROP FOREIGN KEY FK_31711176C71DAEF9');
        $this->addSql('DROP INDEX IDX_317111764AA4F91A ON sync_log');
        $this->addSql('DROP INDEX IDX_31711176C71DAEF9 ON sync_log');
        $this->addSql('DROP INDEX IDX_317111762BB82D88 ON sync_log');
        $this->addSql('DROP INDEX IDX_31711176861616A9 ON sync_log');
        $this->addSql('ALTER TABLE sync_log ADD entity VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD old_id INT DEFAULT NULL, ADD new_id INT DEFAULT NULL, DROP new_company_id, DROP new_job_posting_id, DROP old_company_id, DROP old_job_posting_id');
        $this->addSql('CREATE INDEX IDX_31711176E284468 ON sync_log (entity)');
        $this->addSql('CREATE INDEX IDX_3171117639E6FA16 ON sync_log (old_id)');
    }
}
