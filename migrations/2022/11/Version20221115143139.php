<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20221115143139 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private array $usersToUpdate = [];

    public function preUp(Schema $schema): void
    {
        $query = 'SELECT u.id, u.partner FROM user u WHERE u.partner IS NOT NULL AND u.deleted_at IS NULL';
        $stmt = $this->connection->prepare($query);

        foreach ($stmt->executeQuery()->fetchAllAssociative() as $user) {
            if (!isset($this->usersToUpdate[$user['partner']])) {
                $this->usersToUpdate[$user['partner']] = [];
            }

            $this->usersToUpdate[$user['partner']][] = $user['id'];
        }
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD partner_id INT DEFAULT NULL, DROP partner');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499393F8FE');
        $this->addSql('ALTER TABLE user ADD partner VARCHAR(180) DEFAULT NULL, DROP partner_id');
    }

    public function postUp(Schema $schema): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $query = 'SELECT p.partner, p.id, p.partner as partner FROM partner as p';
        $partners = $this->connection->fetchAllAssociativeIndexed($query);

        foreach ($this->usersToUpdate as $partner => $users) {
            if (isset($partners[$partner])) {
                $em->getConnection()->executeStatement(sprintf('UPDATE user set partner_id = %d where id IN (%s)', $partners[$partner]['id'], implode(',', $users)));
            }
        }
    }
}
