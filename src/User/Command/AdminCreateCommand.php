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
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminCreateCommand extends Command
{
    protected static $defaultName = 'app:user:admin:create';

    private EntityManagerInterface $em;

    private ValidatorInterface $validator;

    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
        $this->em = $em;
        $this->validator = $validator;
        $this->hasher = $hasher;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create an admin')
            ->setDefinition(
                [
                    new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                    new InputArgument('nickname', InputArgument::REQUIRED, 'The nickname'),
                    new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                ]
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('App - User - Admin - Create');

        $email = $input->getArgument('email');
        if (false === \is_string($email)) {
            $io->error('"email" is not a string');

            return Command::FAILURE;
        }

        $nickname = $input->getArgument('nickname');
        if (false === \is_string($nickname)) {
            $io->error('"nickname" is not a string');

            return Command::FAILURE;
        }

        $password = $input->getArgument('password');
        if (false === \is_string($password)) {
            $io->error('"password" is not a string');

            return Command::FAILURE;
        }

        $admin = new User();
        $admin
            ->setEmail($email)
            ->setNickname($nickname)
            ->setPassword($this->hasher->hashPassword($admin, $password))
            ->setEnabled(true)
            ->setRoles(['ROLE_ADMIN'])
        ;

        $violations = $this->validator->validate($admin);

        if ($violations->count()) {
            foreach ($violations as $violation) {
                /* @var ConstraintViolation $violation */
                $io->error(sprintf('%s: %s', ucfirst($violation->getPropertyPath()), mb_strtolower($violation->getMessage())));
            }

            return Command::FAILURE;
        }

        $this->em->persist($admin);
        $this->em->flush();

        $io->success(sprintf('Admin "%s" created', $email));

        return Command::SUCCESS;
    }
}
