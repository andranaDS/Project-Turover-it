<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221125110614 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company_user_favorite DROP FOREIGN KEY FK_70529BB4A76ED395');
        $this->addSql('ALTER TABLE company_user_favorite DROP FOREIGN KEY FK_70529BB4979B1AD6');
        $this->addSql('DROP INDEX idx_70529bb4979b1ad6 ON company_user_favorite');
        $this->addSql('CREATE INDEX IDX_F46A48A4979B1AD6 ON company_user_favorite (company_id)');
        $this->addSql('DROP INDEX idx_70529bb4a76ed395 ON company_user_favorite');
        $this->addSql('CREATE INDEX IDX_F46A48A4A76ED395 ON company_user_favorite (user_id)');
        $this->addSql('ALTER TABLE company_user_favorite ADD CONSTRAINT FK_70529BB4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_user_favorite ADD CONSTRAINT FK_70529BB4979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499393F8FE');
        $this->addSql('ALTER TABLE company_user_favorite DROP FOREIGN KEY FK_F46A48A4979B1AD6');
        $this->addSql('ALTER TABLE company_user_favorite DROP FOREIGN KEY FK_F46A48A4A76ED395');
        $this->addSql('DROP INDEX idx_f46a48a4979b1ad6 ON company_user_favorite');
        $this->addSql('CREATE INDEX IDX_70529BB4979B1AD6 ON company_user_favorite (company_id)');
        $this->addSql('DROP INDEX idx_f46a48a4a76ed395 ON company_user_favorite');
        $this->addSql('CREATE INDEX IDX_70529BB4A76ED395 ON company_user_favorite (user_id)');
        $this->addSql('ALTER TABLE company_user_favorite ADD CONSTRAINT FK_F46A48A4979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_user_favorite ADD CONSTRAINT FK_F46A48A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}
