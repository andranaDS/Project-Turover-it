<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210723133050 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search ADD published_since VARCHAR(25) DEFAULT NULL, ADD contracts LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD min_annual_salary INT DEFAULT NULL, ADD max_annual_salary INT DEFAULT NULL, ADD min_daily_salary INT DEFAULT NULL, ADD max_daily_salary INT DEFAULT NULL, DROP min_salary, DROP max_salary, DROP salary_interval, DROP contract, CHANGE title title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD id INT AUTO_INCREMENT NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search ADD min_salary INT DEFAULT NULL, ADD max_salary INT DEFAULT NULL, ADD salary_interval VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD contract VARCHAR(12) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP published_since, DROP contracts, DROP min_annual_salary, DROP max_annual_salary, DROP min_daily_salary, DROP max_daily_salary, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE job_posting_search_skill MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE job_posting_search_skill DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE job_posting_search_skill DROP id, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD PRIMARY KEY (skill_id, job_posting_search_id)');
    }
}
