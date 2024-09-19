<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220921145214 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_job ADD id INT AUTO_INCREMENT NOT NULL, ADD main TINYINT(1) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_job MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE user_job DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_job DROP id, DROP main, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE user_job ADD PRIMARY KEY (user_id, job_id)');
    }
}
