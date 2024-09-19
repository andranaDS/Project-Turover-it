<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221202150548 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_trace (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recruiter_id INT NOT NULL, ip VARCHAR(255) NOT NULL, viewed_at DATETIME NOT NULL, INDEX IDX_D3992EF9A76ED395 (user_id), INDEX IDX_D3992EF9156BE243 (recruiter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_trace ADD CONSTRAINT FK_D3992EF9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_trace ADD CONSTRAINT FK_D3992EF9156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_trace');
    }
}
