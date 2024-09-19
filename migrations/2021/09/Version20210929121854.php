<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210929121854 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search_skill MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE job_posting_search_skill DROP FOREIGN KEY FK_D08057065585C142');
        $this->addSql('ALTER TABLE job_posting_search_skill DROP FOREIGN KEY FK_D0805706EFDCD4');
        $this->addSql('ALTER TABLE job_posting_search_skill DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE job_posting_search_skill DROP id, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD CONSTRAINT FK_D08057065585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD CONSTRAINT FK_D0805706EFDCD4 FOREIGN KEY (job_posting_search_id) REFERENCES job_posting_search (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD PRIMARY KEY (job_posting_search_id, skill_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job_posting_search_skill DROP FOREIGN KEY FK_D0805706EFDCD4');
        $this->addSql('ALTER TABLE job_posting_search_skill DROP FOREIGN KEY FK_D08057065585C142');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD id INT AUTO_INCREMENT NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD CONSTRAINT FK_D0805706EFDCD4 FOREIGN KEY (job_posting_search_id) REFERENCES job_posting_search (id)');
        $this->addSql('ALTER TABLE job_posting_search_skill ADD CONSTRAINT FK_D08057065585C142 FOREIGN KEY (skill_id) REFERENCES skill (id)');
    }
}
