<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220414143245 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE company_data (id INT AUTO_INCREMENT NOT NULL, job_postings_total_count INT NOT NULL, job_postings_free_total_count INT NOT NULL, job_postings_work_total_count INT NOT NULL, job_postings_published_count INT NOT NULL, job_postings_free_published_count INT NOT NULL, job_postings_work_published_count INT NOT NULL, last_job_posting_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO company_data (id, job_postings_total_count, job_postings_free_total_count, job_postings_work_total_count, job_postings_published_count, job_postings_free_published_count, job_postings_work_published_count, last_job_posting_date) SELECT id, job_postings_total_count, job_postings_free_total_count, job_postings_work_total_count, job_postings_published_count, job_postings_free_published_count, job_postings_work_published_count, last_job_posting_date FROM company');
        $this->addSql('ALTER TABLE company ADD data_id INT DEFAULT NULL, DROP job_postings_total_count, DROP job_postings_free_total_count, DROP job_postings_work_total_count, DROP job_postings_published_count, DROP job_postings_free_published_count, DROP job_postings_work_published_count, DROP applications_count, DROP last_job_posting_date');
        $this->addSql('UPDATE company SET data_id = id');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F37F5A13C FOREIGN KEY (data_id) REFERENCES company_data (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094F37F5A13C ON company (data_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ADD job_postings_total_count INT NOT NULL, ADD job_postings_free_total_count INT NOT NULL, ADD job_postings_work_total_count INT NOT NULL, ADD job_postings_published_count INT NOT NULL, ADD job_postings_free_published_count INT NOT NULL, ADD job_postings_work_published_count INT NOT NULL, ADD applications_count INT NOT NULL, ADD last_job_posting_date DATETIME DEFAULT NULL');
        $this->addSql('UPDATE company c SET
            c.job_postings_total_count = (SELECT cd.job_postings_total_count FROM company_data cd WHERE cd.id = c.data_id),
            c.job_postings_free_total_count = (SELECT cd.job_postings_free_total_count FROM company_data cd WHERE cd.id = c.data_id),
            c.job_postings_work_total_count = (SELECT cd.job_postings_work_total_count FROM company_data cd WHERE cd.id = c.data_id),
            c.job_postings_published_count = (SELECT cd.job_postings_published_count FROM company_data cd WHERE cd.id = c.data_id),
            c.job_postings_free_published_count = (SELECT cd.job_postings_free_published_count FROM company_data cd WHERE cd.id = c.data_id),
            c.job_postings_work_published_count = (SELECT cd.job_postings_work_published_count FROM company_data cd WHERE cd.id = c.data_id),
            c.last_job_posting_date = (SELECT cd.last_job_posting_date FROM company_data cd WHERE cd.id = c.data_id)
        ');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F37F5A13C');
        $this->addSql('DROP TABLE company_data');
        $this->addSql('DROP INDEX UNIQ_4FBF094F37F5A13C ON company');
        $this->addSql('ALTER TABLE company DROP data_id');
    }
}
