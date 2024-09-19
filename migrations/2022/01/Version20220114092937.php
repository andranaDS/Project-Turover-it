<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220114092937 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE job_posting_trace (id INT AUTO_INCREMENT NOT NULL, job_posting_id INT NOT NULL, user_id INT DEFAULT NULL, ip VARCHAR(255) NOT NULL, read_at DATETIME NOT NULL, INDEX IDX_ADEBE920F09E15EB (job_posting_id), INDEX IDX_ADEBE920A76ED395 (user_id), INDEX IDX_ADEBE920E2DA3872 (read_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_posting_trace ADD CONSTRAINT FK_ADEBE920F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_trace ADD CONSTRAINT FK_ADEBE920A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A45BDDC18B8E8428 ON application (created_at)');
        $this->addSql('CREATE INDEX IDX_8D93D64943625D9F ON user (updated_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE job_posting_trace');
        $this->addSql('DROP INDEX IDX_A45BDDC18B8E8428 ON application');
        $this->addSql('DROP INDEX IDX_8D93D64943625D9F ON user');
    }
}
