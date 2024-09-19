<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211119170220 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE home_statistics (id INT AUTO_INCREMENT NOT NULL, date VARCHAR(255) NOT NULL, visible_resume_count INT NOT NULL, job_posting_free_count INT NOT NULL, job_posting_work_count INT NOT NULL, t_it_recruiter_count INT NOT NULL, forum_topics_count INT NOT NULL, UNIQUE INDEX UNIQ_931CDAD2AA9E377A (date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_profile_views DROP FOREIGN KEY FK_F33267DFA76ED395');
        $this->addSql('DROP INDEX idx_f33267dfa76ed395 ON user_profile_views');
        $this->addSql('CREATE INDEX IDX_8AAAE05A76ED395 ON user_profile_views (user_id)');
        $this->addSql('DROP INDEX idx_f33267dfaa9e377a ON user_profile_views');
        $this->addSql('CREATE INDEX IDX_8AAAE05AA9E377A ON user_profile_views (date)');
        $this->addSql('ALTER TABLE user_profile_views ADD CONSTRAINT FK_F33267DFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE home_statistics');
        $this->addSql('ALTER TABLE user_profile_views DROP FOREIGN KEY FK_8AAAE05A76ED395');
        $this->addSql('DROP INDEX idx_8aaae05aa9e377a ON user_profile_views');
        $this->addSql('CREATE INDEX IDX_F33267DFAA9E377A ON user_profile_views (date)');
        $this->addSql('DROP INDEX idx_8aaae05a76ed395 ON user_profile_views');
        $this->addSql('CREATE INDEX IDX_F33267DFA76ED395 ON user_profile_views (user_id)');
        $this->addSql('ALTER TABLE user_profile_views ADD CONSTRAINT FK_8AAAE05A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
