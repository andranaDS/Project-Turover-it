<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220525122349 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_topic_trace DROP FOREIGN KEY FK_8C5CFA611F55203D');
        $this->addSql('DROP INDEX IDX_8C5CFA611F55203D ON forum_topic_trace');
        $this->addSql('CREATE TABLE forum_topic_data (id INT UNSIGNED NOT NULL, posts_count INT NOT NULL, replies_count INT NOT NULL, views_count INT NOT NULL, upvotes_count INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO forum_topic_data (id, posts_count, replies_count, views_count, upvotes_count) SELECT id, posts_count, replies_count, views_count, upvotes_count FROM forum_topic');
        $this->addSql('ALTER TABLE forum_topic DROP posts_count, DROP replies_count, DROP views_count, DROP upvotes_count');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_topic_trace ADD CONSTRAINT FK_8C5CFA611F55203D FOREIGN KEY (topic_id) REFERENCES forum_topic (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8C5CFA611F55203D ON forum_topic_trace (topic_id)');
        $this->addSql('ALTER TABLE forum_topic ADD posts_count INT NOT NULL, ADD replies_count INT NOT NULL, ADD views_count INT NOT NULL, ADD upvotes_count INT NOT NULL');
        $this->addSql('UPDATE forum_topic ft SET
            ft.posts_count = (SELECT ftd.posts_count FROM forum_topic_data ftd WHERE ft.id = ftd.id),
            ft.replies_count = (SELECT ftd.replies_count FROM forum_topic_data ftd WHERE ft.id = ftd.id),
            ft.views_count = (SELECT ftd.views_count FROM forum_topic_data ftd WHERE ft.id = ftd.id),
            ft.upvotes_count = (SELECT ftd.upvotes_count FROM forum_topic_data ftd WHERE ft.id = ftd.id),
        ');
        $this->addSql('DROP TABLE forum_topic_data');
    }
}
