<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221122091228 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_recruiter_trace DROP FOREIGN KEY FK_EEDDAAA1F09E15EB');
        $this->addSql('ALTER TABLE job_posting_recruiter_trace DROP FOREIGN KEY FK_EEDDAAA1156BE243');
        $this->addSql('DROP INDEX idx_eeddaaa1156be243 ON job_posting_recruiter_trace');
        $this->addSql('CREATE INDEX IDX_D3F60FE2156BE243 ON job_posting_recruiter_trace (recruiter_id)');
        $this->addSql('DROP INDEX idx_eeddaaa1f09e15eb ON job_posting_recruiter_trace');
        $this->addSql('CREATE INDEX IDX_D3F60FE2F09E15EB ON job_posting_recruiter_trace (job_posting_id)');
        $this->addSql('DROP INDEX idx_eeddaaa1e2da3872 ON job_posting_recruiter_trace');
        $this->addSql('CREATE INDEX IDX_D3F60FE2E2DA3872 ON job_posting_recruiter_trace (read_at)');
        $this->addSql('ALTER TABLE job_posting_recruiter_trace ADD CONSTRAINT FK_EEDDAAA1F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_recruiter_trace ADD CONSTRAINT FK_EEDDAAA1156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE job_posting_user_trace DROP FOREIGN KEY FK_ADEBE920A76ED395');
        $this->addSql('ALTER TABLE job_posting_user_trace DROP FOREIGN KEY FK_ADEBE920F09E15EB');
        $this->addSql('DROP INDEX idx_adebe920f09e15eb ON job_posting_user_trace');
        $this->addSql('CREATE INDEX IDX_FD48CBC4F09E15EB ON job_posting_user_trace (job_posting_id)');
        $this->addSql('DROP INDEX idx_adebe920a76ed395 ON job_posting_user_trace');
        $this->addSql('CREATE INDEX IDX_FD48CBC4A76ED395 ON job_posting_user_trace (user_id)');
        $this->addSql('DROP INDEX idx_adebe920e2da3872 ON job_posting_user_trace');
        $this->addSql('CREATE INDEX IDX_FD48CBC4E2DA3872 ON job_posting_user_trace (read_at)');
        $this->addSql('ALTER TABLE job_posting_user_trace ADD CONSTRAINT FK_ADEBE920A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_user_trace ADD CONSTRAINT FK_ADEBE920F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_user_trace DROP FOREIGN KEY FK_FD48CBC4F09E15EB');
        $this->addSql('ALTER TABLE job_posting_user_trace DROP FOREIGN KEY FK_FD48CBC4A76ED395');
        $this->addSql('DROP INDEX idx_fd48cbc4a76ed395 ON job_posting_user_trace');
        $this->addSql('CREATE INDEX IDX_ADEBE920A76ED395 ON job_posting_user_trace (user_id)');
        $this->addSql('DROP INDEX idx_fd48cbc4e2da3872 ON job_posting_user_trace');
        $this->addSql('CREATE INDEX IDX_ADEBE920E2DA3872 ON job_posting_user_trace (read_at)');
        $this->addSql('DROP INDEX idx_fd48cbc4f09e15eb ON job_posting_user_trace');
        $this->addSql('CREATE INDEX IDX_ADEBE920F09E15EB ON job_posting_user_trace (job_posting_id)');
        $this->addSql('ALTER TABLE job_posting_user_trace ADD CONSTRAINT FK_FD48CBC4F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_user_trace ADD CONSTRAINT FK_FD48CBC4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_recruiter_trace DROP FOREIGN KEY FK_D3F60FE2156BE243');
        $this->addSql('ALTER TABLE job_posting_recruiter_trace DROP FOREIGN KEY FK_D3F60FE2F09E15EB');
        $this->addSql('DROP INDEX idx_d3f60fe2f09e15eb ON job_posting_recruiter_trace');
        $this->addSql('CREATE INDEX IDX_EEDDAAA1F09E15EB ON job_posting_recruiter_trace (job_posting_id)');
        $this->addSql('DROP INDEX idx_d3f60fe2156be243 ON job_posting_recruiter_trace');
        $this->addSql('CREATE INDEX IDX_EEDDAAA1156BE243 ON job_posting_recruiter_trace (recruiter_id)');
        $this->addSql('DROP INDEX idx_d3f60fe2e2da3872 ON job_posting_recruiter_trace');
        $this->addSql('CREATE INDEX IDX_EEDDAAA1E2DA3872 ON job_posting_recruiter_trace (read_at)');
        $this->addSql('ALTER TABLE job_posting_recruiter_trace ADD CONSTRAINT FK_D3F60FE2156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE job_posting_recruiter_trace ADD CONSTRAINT FK_D3F60FE2F09E15EB FOREIGN KEY (job_posting_id) REFERENCES job_posting (id) ON DELETE CASCADE');
    }
}
