<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220513135336 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE blog_post_data (id INT UNSIGNED NOT NULL, upvotes_count INT NOT NULL, views_count INT NOT NULL, recent_views_count INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO blog_post_data (id, upvotes_count, views_count, recent_views_count) SELECT id, upvotes_count, views_count, recent_views_count FROM blog_post');
        $this->addSql('ALTER TABLE blog_post DROP upvotes_count, DROP views_count, DROP recent_views_count');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_post ADD upvotes_count INT NOT NULL, ADD views_count INT NOT NULL, ADD recent_views_count INT NOT NULL');
        $this->addSql('UPDATE blog_post bp SET
            bp.upvotes_count = (SELECT bpd.upvotes_count FROM blog_post_data bpd WHERE bp.id = bpd.id),
            bp.views_count = (SELECT bpd.views_count FROM blog_post_data bpd WHERE bp.id = bpd.id),
            bp.recent_views_count = (SELECT bpd.recent_views_count FROM blog_post_data bpd WHERE bp.id = bpd.id),
        ');
        $this->addSql('DROP TABLE blog_post_data');
    }
}
