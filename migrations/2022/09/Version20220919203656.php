<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220919203656 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_template DROP FOREIGN KEY FK_CF05A4DBF675F31B');
        $this->addSql('DROP INDEX IDX_CF05A4DBF675F31B ON job_posting_template');
        $this->addSql('ALTER TABLE job_posting_template DROP duration, CHANGE author_id created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE job_posting_template ADD CONSTRAINT FK_CF05A4DBB03A8386 FOREIGN KEY (created_by_id) REFERENCES recruiter (id)');
        $this->addSql('CREATE INDEX IDX_CF05A4DBB03A8386 ON job_posting_template (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_template DROP FOREIGN KEY FK_CF05A4DBB03A8386');
        $this->addSql('DROP INDEX IDX_CF05A4DBB03A8386 ON job_posting_template');
        $this->addSql('ALTER TABLE job_posting_template ADD duration INT DEFAULT NULL, CHANGE created_by_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE job_posting_template ADD CONSTRAINT FK_CF05A4DBF675F31B FOREIGN KEY (author_id) REFERENCES recruiter (id)');
        $this->addSql('CREATE INDEX IDX_CF05A4DBF675F31B ON job_posting_template (author_id)');
    }
}
