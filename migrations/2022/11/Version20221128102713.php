<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221128102713 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // user
        $this->addSql('RENAME TABLE job_posting_favorite to job_posting_user_favorite');
        $this->addSql('ALTER TABLE job_posting_user_favorite DROP FOREIGN KEY FK_30A03581A76ED395');
        $this->addSql('ALTER TABLE job_posting_user_favorite DROP FOREIGN KEY FK_30A03581F09E15EB');
        $this->addSql('DROP INDEX idx_30a03581f09e15eb ON job_posting_user_favorite');
        $this->addSql('CREATE INDEX IDX_7626F9DFF09E15EB ON job_posting_user_favorite (job_posting_id)');
        $this->addSql('DROP INDEX idx_30a03581a76ed395 ON job_posting_user_favorite');
        $this->addSql('CREATE INDEX IDX_7626F9DFA76ED395 ON job_posting_user_favorite (user_id)');
        $this->addSql('ALTER TABLE job_posting_user_favorite ADD CONSTRAINT FK_30A03581A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_user_favorite ADD CONSTRAINT FK_30A03581F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');

        // recruiter
        $this->addSql('CREATE TABLE job_posting_recruiter_favorite (id INT AUTO_INCREMENT NOT NULL, job_posting_id INT NOT NULL, recruiter_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_C9A94F62F09E15EB (job_posting_id), INDEX IDX_C9A94F62156BE243 (recruiter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_posting_recruiter_favorite ADD CONSTRAINT FK_C9A94F62F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_recruiter_favorite ADD CONSTRAINT FK_C9A94F62156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // user
        $this->addSql('RENAME TABLE job_posting_user_favorite to job_posting_favorite');
        $this->addSql('ALTER TABLE job_posting_user_favorite DROP FOREIGN KEY FK_7626F9DFF09E15EB');
        $this->addSql('ALTER TABLE job_posting_user_favorite DROP FOREIGN KEY FK_7626F9DFA76ED395');
        $this->addSql('DROP INDEX idx_7626f9dff09e15eb ON job_posting_user_favorite');
        $this->addSql('CREATE INDEX IDX_30A03581F09E15EB ON job_posting_user_favorite (job_posting_id)');
        $this->addSql('DROP INDEX idx_7626f9dfa76ed395 ON job_posting_user_favorite');
        $this->addSql('CREATE INDEX IDX_30A03581A76ED395 ON job_posting_user_favorite (user_id)');
        $this->addSql('ALTER TABLE job_posting_user_favorite ADD CONSTRAINT FK_7626F9DFF09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_user_favorite ADD CONSTRAINT FK_7626F9DFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');

        // recruiter
        $this->addSql('ALTER TABLE job_posting_recruiter_favorite DROP FOREIGN KEY FK_C9A94F62F09E15EB');
        $this->addSql('ALTER TABLE job_posting_recruiter_favorite DROP FOREIGN KEY FK_C9A94F62156BE243');
        $this->addSql('DROP TABLE job_posting_recruiter_favorite');
    }
}
