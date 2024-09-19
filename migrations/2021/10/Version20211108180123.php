<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211108180123 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job ADD parent_for_contribution_id INT DEFAULT NULL, ADD faq_description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F820C706AE FOREIGN KEY (parent_for_contribution_id) REFERENCES job (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F820C706AE ON job (parent_for_contribution_id)');
        $this->addSql('ALTER TABLE job MODIFY created_at datetime NOT NULL AFTER faq_seo_meta_description');
        $this->addSql('ALTER TABLE job MODIFY updated_at datetime NOT NULL AFTER created_at');
        $this->addSql('ALTER TABLE job MODIFY salary_skills longtext NOT NULL comment \'(DC2Type:json)\' AFTER salary_seo_meta_description');
        $this->addSql('ALTER TABLE job MODIFY available_for_contribution tinyint(1) NOT NULL AFTER slug');
        $this->addSql('ALTER TABLE job MODIFY name_for_contribution varchar(255) null AFTER available_for_contribution');
        $this->addSql('ALTER TABLE job MODIFY name_for_contribution_slug varchar(255) null AFTER name_for_contribution');
        $this->addSql('ALTER TABLE job MODIFY faq_description longtext null AFTER salary_skills');
        $this->addSql('ALTER TABLE job MODIFY parent_for_contribution_id int null AFTER name_for_contribution_slug');
        $this->addSql('ALTER TABLE job MODIFY available_for_user tinyint(1) NOT NULL AFTER parent_for_contribution_id');
        $this->addSql('ALTER TABLE job MODIFY name_for_user varchar(255) null AFTER available_for_user');
        $this->addSql('ALTER TABLE job MODIFY name_for_user_slug varchar(255) null AFTER name_for_user');
        $this->addSql('CREATE TABLE trend (id INT AUTO_INCREMENT NOT NULL, candidate_skills_table_id INT NOT NULL, recruiter_skills_table_id INT NOT NULL, candidate_jobs_table_id INT NOT NULL, recruiter_jobs_table_id INT NOT NULL, date VARCHAR(255) NOT NULL, resumes_count INT NOT NULL, gender_distribution LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', status_distribution LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', remote_distribution LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_F4FB33A6AA9E377A (date), UNIQUE INDEX UNIQ_F4FB33A66EE0084A (candidate_skills_table_id), UNIQUE INDEX UNIQ_F4FB33A6ABAE9926 (recruiter_skills_table_id), UNIQUE INDEX UNIQ_F4FB33A67E987A5E (candidate_jobs_table_id), UNIQUE INDEX UNIQ_F4FB33A6FF9348 (recruiter_jobs_table_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trend_job_line (id INT AUTO_INCREMENT NOT NULL, table_id INT NOT NULL, job_id INT NOT NULL, position INT NOT NULL, evolution INT DEFAULT NULL, count INT NOT NULL, INDEX IDX_C9E1D03ECFF285C (table_id), INDEX IDX_C9E1D03BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trend_job_table (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trend_skill_line (id INT AUTO_INCREMENT NOT NULL, table_id INT NOT NULL, skill_id INT NOT NULL, position INT NOT NULL, evolution INT DEFAULT NULL, count INT NOT NULL, INDEX IDX_90D6FBECFF285C (table_id), INDEX IDX_90D6FB5585C142 (skill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trend_skill_table (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trend ADD CONSTRAINT FK_F4FB33A66EE0084A FOREIGN KEY (candidate_skills_table_id) REFERENCES trend_skill_table (id)');
        $this->addSql('ALTER TABLE trend ADD CONSTRAINT FK_F4FB33A6ABAE9926 FOREIGN KEY (recruiter_skills_table_id) REFERENCES trend_skill_table (id)');
        $this->addSql('ALTER TABLE trend ADD CONSTRAINT FK_F4FB33A67E987A5E FOREIGN KEY (candidate_jobs_table_id) REFERENCES trend_job_table (id)');
        $this->addSql('ALTER TABLE trend ADD CONSTRAINT FK_F4FB33A6FF9348 FOREIGN KEY (recruiter_jobs_table_id) REFERENCES trend_job_table (id)');
        $this->addSql('ALTER TABLE trend_job_line ADD CONSTRAINT FK_C9E1D03ECFF285C FOREIGN KEY (table_id) REFERENCES trend_job_table (id)');
        $this->addSql('ALTER TABLE trend_job_line ADD CONSTRAINT FK_C9E1D03BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE trend_skill_line ADD CONSTRAINT FK_90D6FBECFF285C FOREIGN KEY (table_id) REFERENCES trend_skill_table (id)');
        $this->addSql('ALTER TABLE trend_skill_line ADD CONSTRAINT FK_90D6FB5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id)');
        $this->addSql('ALTER TABLE trend MODIFY date varchar(255) NOT NULL AFTER id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F820C706AE');
        $this->addSql('DROP INDEX IDX_FBD8E0F820C706AE ON job');
        $this->addSql('ALTER TABLE job DROP parent_for_contribution_id, DROP faq_description');
        $this->addSql('ALTER TABLE trend DROP FOREIGN KEY FK_F4FB33A67E987A5E');
        $this->addSql('ALTER TABLE trend DROP FOREIGN KEY FK_F4FB33A6FF9348');
        $this->addSql('ALTER TABLE trend_job_line DROP FOREIGN KEY FK_C9E1D03ECFF285C');
        $this->addSql('ALTER TABLE trend DROP FOREIGN KEY FK_F4FB33A66EE0084A');
        $this->addSql('ALTER TABLE trend DROP FOREIGN KEY FK_F4FB33A6ABAE9926');
        $this->addSql('ALTER TABLE trend_skill_line DROP FOREIGN KEY FK_90D6FBECFF285C');
        $this->addSql('DROP TABLE trend');
        $this->addSql('DROP TABLE trend_job_line');
        $this->addSql('DROP TABLE trend_job_table');
        $this->addSql('DROP TABLE trend_skill_line');
        $this->addSql('DROP TABLE trend_skill_table');
    }
}
