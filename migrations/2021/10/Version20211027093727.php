<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211027093727 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $data = [
            'fr-fr' => 'fr_FR',
            'en-gb' => 'en_GB',
            'it-ch' => 'it_CH',
            'de-ch' => 'de_CH',
            'fr-ch' => 'fr_CH',
            'de-lu' => 'de_LU',
            'fr-be' => 'fr_BE',
            'nl-be' => 'nl_BE',
            'en' => 'en_GB',
            'fr' => 'fr_FR',
        ];

        foreach ($data as $search => $replace) {
            $this->addSql("UPDATE blog_category SET locales = REPLACE(locales, '\"$search\"', '\"$replace\"')");
            $this->addSql("UPDATE blog_post SET locales = REPLACE(locales, '\"$search\"', '\"$replace\"')");
        }
    }

    public function down(Schema $schema): void
    {
    }
}
