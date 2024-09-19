<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221116100413 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE partner ADD api_url VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499393F8FE');
        $this->addSql('DROP INDEX fk_8d93d6499393f8fe ON user');
        $this->addSql('CREATE INDEX IDX_8D93D6499393F8FE ON user (partner_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_lead ADD response_status_code INT UNSIGNED DEFAULT NULL');
        $this->addSql('UPDATE partner SET api_url = "https://hooks.zapier.com/hooks/catch/2252177/bp0j6lh/" WHERE partner = "freelancecom"');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE partner DROP api_url');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499393F8FE');
        $this->addSql('DROP INDEX idx_8d93d6499393f8fe ON user');
        $this->addSql('CREATE INDEX FK_8D93D6499393F8FE ON user (partner_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_lead DROP response_status_code');
    }
}
