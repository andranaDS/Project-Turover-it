<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211019092421 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job ADD salary_description LONGTEXT NOT NULL, ADD salary_formation LONGTEXT NOT NULL, ADD salary_standard_mission LONGTEXT NOT NULL, ADD salary_seo_meta_title VARCHAR(70) NOT NULL, ADD salary_seo_meta_description VARCHAR(160) NOT NULL, ADD faq_price LONGTEXT NOT NULL, ADD faq_definition LONGTEXT NOT NULL, ADD faq_missions LONGTEXT NOT NULL, ADD faq_skills LONGTEXT NOT NULL, ADD faq_profile LONGTEXT NOT NULL, ADD faq_seo_meta_title VARCHAR(70) NOT NULL, ADD faq_seo_meta_description VARCHAR(160) NOT NULL, ADD salary_skills LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job DROP salary_description, DROP salary_formation, DROP salary_standard_mission, DROP salary_seo_meta_title, DROP salary_seo_meta_description, DROP faq_price, DROP faq_definition, DROP faq_missions, DROP faq_skills, DROP faq_profile, DROP faq_seo_meta_title, DROP faq_seo_meta_description, DROP salary_skills');
    }
}
