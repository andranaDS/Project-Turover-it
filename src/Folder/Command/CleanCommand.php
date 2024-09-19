<?php

namespace App\Folder\Command;

use App\Folder\Enum\FolderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanCommand extends Command
{
    protected static $defaultName = 'app:folder:clean';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('App - Folder - Clean');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $connection = $this->em->getConnection();

        $io->title('App - Folder - Clean');

        $connection->beginTransaction();
        try {
            // 1. remove users from folders viewed/yesterday_cart and reset usersCount
            $io->text('1. remove users from folders viewed/yesterday_cart');
            $connection->executeQuery(sprintf("DELETE FROM folder_user WHERE folder_id IN (SELECT f.id FROM folder f WHERE f.type IN ('%s', '%s'));", FolderType::VIEWED, FolderType::YESTERDAY_CART));

            // 2. move users from cart to yesterday_cart and update usersCount
            $io->text('2. move users from cart to yesterday_cart');
            $connection->executeQuery(sprintf("UPDATE folder_user fu JOIN folder f on f.id = fu.folder_id SET fu.folder_id = (SELECT f2.id FROM folder f2 WHERE f2.recruiter_id = f.recruiter_id AND f2.type = '%s') WHERE f.type = '%s'", FolderType::YESTERDAY_CART, FolderType::CART));

            // 3. update usersCount
            $io->text('3. update usersCount');
            $connection->executeQuery(sprintf("UPDATE folder f SET f.users_count = (SELECT f2.users_count FROM folder f2 WHERE f2.type = '%s' and f2.recruiter_id = f.recruiter_id) WHERE f.type = '%s'", FolderType::CART, FolderType::YESTERDAY_CART));
            $connection->executeQuery(sprintf("UPDATE folder f SET f.users_count = 0 WHERE f.type IN ('%s', '%s')", FolderType::VIEWED, FolderType::CART));

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return Command::SUCCESS;
    }
}
