<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Partner\Enum\Partner;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221108133000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, partner VARCHAR(16) NOT NULL, distribution INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_312B3E16312B3E16 ON partner (partner)');

        foreach (Partner::getConstants() as $partner) {
            $this->addSql("INSERT INTO partner VALUES (null,'$partner', 50, now(), now())");
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP INDEX UNIQ_312B3E16312B3E16 ON partner');
    }
}
