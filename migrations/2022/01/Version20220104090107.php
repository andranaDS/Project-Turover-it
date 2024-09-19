<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220104090107 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE feed ADD last_message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD CONSTRAINT FK_234044ABBA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_234044ABBA0E79C3 ON feed (last_message_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE feed DROP FOREIGN KEY FK_234044ABBA0E79C3');
        $this->addSql('DROP INDEX UNIQ_234044ABBA0E79C3 ON feed');
        $this->addSql('ALTER TABLE feed DROP last_message_id');
    }
}
