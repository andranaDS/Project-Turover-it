<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220601130126 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE skill DROP profile_usage_count');
        $this->addSql('CREATE INDEX IDX_8D93D649F1BD3FAF ON user (last_login_at)');
        $this->addSql('CREATE INDEX IDX_8D93D6498B8E8428 ON user (created_at)');

        $this->addSql('ALTER TABLE user ADD origin INT DEFAULT NULL');
        $this->addSql('UPDATE user SET origin = 1 WHERE created_at < "2022-03-14 19:00:00"'); // origin CI
        $this->addSql('UPDATE user SET origin = 0 WHERE created_at > "2022-03-14 19:00:00"'); // origin FW

        $this->addSql('CREATE INDEX IDX_8D93D649DEF1561E ON user (origin)');

        $this->addSql('ALTER TABLE message ADD old_id INT DEFAULT NULL');

        $this->addSql('CREATE INDEX IDX_8C5CFA611F55203D ON forum_topic_trace (topic_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE skill ADD profile_usage_count INT NOT NULL');
        $this->addSql('DROP INDEX IDX_8D93D649F1BD3FAF ON user');
        $this->addSql('DROP INDEX IDX_8D93D6498B8E8428 ON user');

        $this->addSql('ALTER TABLE user DROP origin');

        $this->addSql('DROP INDEX IDX_8D93D649DEF1561E ON user');

        $this->addSql('ALTER TABLE message DROP old_id');

        $this->addSql('DROP INDEX IDX_8C5CFA611F55203D ON forum_topic_trace');
    }
}
