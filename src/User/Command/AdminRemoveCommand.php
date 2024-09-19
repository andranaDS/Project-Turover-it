<?php

namespace App\User\Command;

use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminRemoveCommand extends Command
{
    protected static $defaultName = 'app:user:admin:remove';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Remove an admin')
            ->setDefinition(
                [
                    new InputArgument('admin', InputArgument::REQUIRED, 'The admin'),
                ]
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('App - User - Admin - Remove');

        $email = $input->getArgument('email');
        if (false === \is_string($email)) {
            $io->error('"email" is not a string');

            return Command::FAILURE;
        }

        if (null === ($admin = $this->em->getRepository(User::class)->findOneByEmail($email))) {
            $io->error(sprintf('Admin %s was not found', $email));

            return Command::FAILURE;
        }

        $this->em->remove($admin);
        $this->em->flush();

        $io->success(sprintf('Admin "%s" removed', $email));

        return Command::SUCCESS;
    }
}
