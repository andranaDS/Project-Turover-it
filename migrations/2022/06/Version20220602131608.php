<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220602131608 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_category DROP FOREIGN KEY FK_21BF94262D053F64');
        $this->addSql('ALTER TABLE forum_category ADD CONSTRAINT FK_21BF94262D053F64 FOREIGN KEY (last_post_id) REFERENCES forum_post (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_category DROP FOREIGN KEY FK_21BF94262D053F64');
        $this->addSql('ALTER TABLE forum_category ADD CONSTRAINT FK_21BF94262D053F64 FOREIGN KEY (last_post_id) REFERENCES forum_post (id) ON DELETE CASCADE');
    }
}
