<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211118113506 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_post ADD old_freelance_info_id INT DEFAULT NULL, ADD old_carriere_info_id INT DEFAULT NULL, ADD old_url VARCHAR(255) DEFAULT NULL, CHANGE category_id category_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_post DROP old_freelance_info_id, DROP old_carriere_info_id, DROP old_url, CHANGE category_id category_id INT NOT NULL');
    }
}
