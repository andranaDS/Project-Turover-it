<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210824080216 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting DROP salary_interval');
        $this->addSql('ALTER TABLE job_posting_skill DROP FOREIGN KEY FK_C28DD8F65585C142');
        $this->addSql('ALTER TABLE job_posting_skill DROP FOREIGN KEY FK_C28DD8F6F09E15EB');
        $this->addSql('ALTER TABLE job_posting_skill DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE job_posting_skill DROP required, DROP skill_level');
        $this->addSql('ALTER TABLE job_posting_skill ADD CONSTRAINT FK_C28DD8F65585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_skill ADD CONSTRAINT FK_C28DD8F6F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_skill ADD PRIMARY KEY (job_posting_id, skill_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting ADD salary_interval VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE job_posting_skill DROP FOREIGN KEY FK_C28DD8F6F09E15EB');
        $this->addSql('ALTER TABLE job_posting_skill DROP FOREIGN KEY FK_C28DD8F65585C142');
        $this->addSql('ALTER TABLE job_posting_skill DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE job_posting_skill ADD required TINYINT(1) NOT NULL, ADD skill_level VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE job_posting_skill ADD CONSTRAINT FK_C28DD8F6F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id)');
        $this->addSql('ALTER TABLE job_posting_skill ADD CONSTRAINT FK_C28DD8F65585C142 FOREIGN KEY (skill_id) REFERENCES skill (id)');
        $this->addSql('ALTER TABLE job_posting_skill ADD PRIMARY KEY (skill_id, job_posting_id)');
    }
}
