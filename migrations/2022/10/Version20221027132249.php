<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221027132249 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search_skill DROP FOREIGN KEY FK_D08057065585C142');
        $this->addSql('ALTER TABLE job_posting_search_skill DROP FOREIGN KEY FK_D0805706EFDCD4');
        $this->addSql('DROP TABLE job_posting_search_skill');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE job_posting_search_skill (skill_id INT NOT NULL, job_posting_search_id INT NOT NULL, INDEX IDX_D08057065585C142 (skill_id), INDEX IDX_D0805706EFDCD4 (job_posting_search_id), PRIMARY KEY(job_posting_search_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD CONSTRAINT FK_D08057065585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD CONSTRAINT FK_D0805706EFDCD4 FOREIGN KEY (job_posting_search_id) REFERENCES job_posting_search (id) ON DELETE CASCADE');
    }
}
