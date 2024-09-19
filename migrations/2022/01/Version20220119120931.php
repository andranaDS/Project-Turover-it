<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220119120931 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_notification (id INT AUTO_INCREMENT NOT NULL, marketing_newsletter TINYINT(1) NOT NULL, forum_topic_reply TINYINT(1) NOT NULL, forum_topic_favorite TINYINT(1) NOT NULL, forum_post_reply TINYINT(1) NOT NULL, forum_post_like TINYINT(1) NOT NULL, messaging_new_message TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD notification_id INT NULL, DROP email_consent, DROP sms_consent');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EF1A9D84 FOREIGN KEY (notification_id) REFERENCES user_notification (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EF1A9D84 ON user (notification_id)');
        $this->addSql('INSERT INTO user_notification (id, marketing_newsletter, forum_topic_reply, forum_topic_favorite, forum_post_reply, forum_post_like, messaging_new_message, created_at, updated_at) SELECT id, "1", "1", "1", "1", "1", "1", "2022-01-01 00:00:00", "2022-01-01 00:00:00" FROM user');
        $this->addSql('UPDATE user AS u SET u.notification_id = u.id');
        $this->addSql('ALTER TABLE user CHANGE notification_id notification_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EF1A9D84');
        $this->addSql('DROP TABLE user_notification');
        $this->addSql('DROP INDEX UNIQ_8D93D649EF1A9D84 ON user');
        $this->addSql('ALTER TABLE user ADD email_consent TINYINT(1) NOT NULL, ADD sms_consent TINYINT(1) NOT NULL, DROP notification_id');
    }
}
