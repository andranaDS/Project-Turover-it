<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220216153749 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE feed ADD application_id INT DEFAULT NULL, DROP job_application');
        $this->addSql('ALTER TABLE feed ADD CONSTRAINT FK_234044AB3E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_234044AB3E030ACD ON feed (application_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE feed DROP FOREIGN KEY FK_234044AB3E030ACD');
        $this->addSql('DROP INDEX IDX_234044AB3E030ACD ON feed');
        $this->addSql('ALTER TABLE feed ADD job_application TINYINT(1) NOT NULL, DROP application_id');
    }
}
