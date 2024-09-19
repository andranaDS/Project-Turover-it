<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211221155556 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_7882EFEF4AF38FD1 ON blog_comment (deleted_at)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_21BF9426989D9B62 ON forum_category (slug)');
        $this->addSql('CREATE INDEX IDX_996BCC5A8B8E8428 ON forum_post (created_at)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_853478CC989D9B62 ON forum_topic (slug)');
        $this->addSql('CREATE INDEX IDX_8D93D649149885F6 ON user (last_activity_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_996BCC5A8B8E8428 ON forum_post');
        $this->addSql('DROP INDEX IDX_8D93D649149885F6 ON user');
        $this->addSql('DROP INDEX IDX_7882EFEF4AF38FD1 ON blog_comment');
        $this->addSql('DROP INDEX UNIQ_21BF9426989D9B62 ON forum_category');
        $this->addSql('DROP INDEX UNIQ_853478CC989D9B62 ON forum_topic');
    }
}
