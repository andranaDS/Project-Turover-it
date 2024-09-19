<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211229150333 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP INDEX UNIQ_8D93D649C76F1F52, ADD INDEX IDX_8D93D649C76F1F52 (deleted_by_id)');
        $this->addSql('ALTER TABLE user ADD locked_by_id INT DEFAULT NULL, ADD ip_on_create VARCHAR(255) NOT NULL, ADD password_updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497A88E00 FOREIGN KEY (locked_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6497A88E00 ON user (locked_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP INDEX IDX_8D93D649C76F1F52, ADD UNIQUE INDEX UNIQ_8D93D649C76F1F52 (deleted_by_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497A88E00');
        $this->addSql('DROP INDEX IDX_8D93D6497A88E00 ON user');
        $this->addSql('ALTER TABLE user DROP locked_by_id, DROP ip_on_create, DROP password_updated_at');
    }
}
