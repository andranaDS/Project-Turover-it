<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220209135258 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting ADD job_id INT NULL');
        $this->addSql('SET foreign_key_checks = 0;UPDATE job_posting SET job_id = 183');
        $this->addSql('ALTER TABLE job_posting MODIFY job_id INT NOT NULL');
        $this->addSql('ALTER TABLE job_posting ADD CONSTRAINT FK_27C8EAE8BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_27C8EAE8BE04EA9 ON job_posting (job_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting DROP FOREIGN KEY FK_27C8EAE8BE04EA9');
        $this->addSql('DROP INDEX IDX_27C8EAE8BE04EA9 ON job_posting');
        $this->addSql('ALTER TABLE job_posting DROP job_id');
    }
}
