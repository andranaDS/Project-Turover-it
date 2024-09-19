<?php

namespace App\User\Command;

use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminChangePasswordCommand extends Command
{
    protected static $defaultName = 'app:user:admin:change-password';

    private EntityManagerInterface $em;

    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
        $this->em = $em;
        $this->hasher = $hasher;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Change user password')
            ->setDefinition(
                [
                    new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                    new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                ]
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('App - User - Admin - Change password');

        $email = $input->getArgument('email');
        if (false === \is_string($email)) {
            $io->error('"email" is not a string');

            return Command::FAILURE;
        }

        $password = $input->getArgument('password');
        if (false === \is_string($password)) {
            $io->error('"password" is not a string');

            return Command::FAILURE;
        }

        if (null === ($admin = $this->em->getRepository(User::class)->findOneByEmail($email))) {
            $io->error(sprintf('Admin %s was not found', $email));

            return Command::FAILURE;
        }

        $admin->setPassword($this->hasher->hashPassword($admin, $password));
        $this->em->flush();

        $io->success(sprintf('Password of the admin "%s" has been changed to "%s"', $email, $password));

        return Command::SUCCESS;
    }
}
