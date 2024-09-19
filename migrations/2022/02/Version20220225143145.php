<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220225143145 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE home_statistics');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FC50D86A0');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FC50D86A0 FOREIGN KEY (cover_picture_id) REFERENCES company_picture (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4FBF094F6B2B1D11 ON company (location_value)');
        $this->addSql('CREATE INDEX IDX_27C8EAE86B2B1D11 ON job_posting (location_value)');
        $this->addSql('ALTER TABLE sync_log DROP FOREIGN KEY FK_317111764AA4F91A');
        $this->addSql('ALTER TABLE sync_log DROP FOREIGN KEY FK_31711176C71DAEF9');
        $this->addSql('ALTER TABLE sync_log ADD CONSTRAINT FK_317111764AA4F91A FOREIGN KEY (new_company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sync_log ADD CONSTRAINT FK_31711176C71DAEF9 FOREIGN KEY (new_job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8D93D6496B2B1D11 ON user (location_value)');
        $this->addSql('CREATE INDEX IDX_36DDC41C6B2B1D11 ON user_mobility (location_value)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE home_statistics (id INT AUTO_INCREMENT NOT NULL, date VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, visible_resume_count INT NOT NULL, job_posting_free_count INT NOT NULL, job_posting_work_count INT NOT NULL, turnover_it_recruiters_count INT NOT NULL, forum_topics_count INT NOT NULL, UNIQUE INDEX UNIQ_931CDAD2AA9E377A (date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FC50D86A0');
        $this->addSql('DROP INDEX IDX_4FBF094F6B2B1D11 ON company');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FC50D86A0 FOREIGN KEY (cover_picture_id) REFERENCES company_picture (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX IDX_27C8EAE86B2B1D11 ON job_posting');
        $this->addSql('ALTER TABLE sync_log DROP FOREIGN KEY FK_317111764AA4F91A');
        $this->addSql('ALTER TABLE sync_log DROP FOREIGN KEY FK_31711176C71DAEF9');
        $this->addSql('ALTER TABLE sync_log ADD CONSTRAINT FK_317111764AA4F91A FOREIGN KEY (new_company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE sync_log ADD CONSTRAINT FK_31711176C71DAEF9 FOREIGN KEY (new_job_posting_id) REFERENCES job_posting (id)');
        $this->addSql('DROP INDEX IDX_8D93D6496B2B1D11 ON user');
        $this->addSql('DROP INDEX IDX_36DDC41C6B2B1D11 ON user_mobility');
    }
}
