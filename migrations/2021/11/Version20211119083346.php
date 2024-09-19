<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211119083346 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_category ADD old_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_post ADD old_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_topic ADD old_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_topic_trace ADD old_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_topic_trace CHANGE ip ip VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_category DROP old_id');
        $this->addSql('ALTER TABLE forum_post DROP old_id');
        $this->addSql('ALTER TABLE forum_topic DROP old_id');
        $this->addSql('ALTER TABLE forum_topic_trace CHANGE ip ip VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE forum_topic_trace DROP old_id');
    }
}
