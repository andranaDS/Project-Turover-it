<?php

namespace App\User\Command;

use App\Core\Util\Arrays;
use App\Sync\Turnover\Client;
use App\User\Entity\User;
use App\User\Entity\UserProfileViews;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserProfileViewsFetchCommand extends Command
{
    protected static $defaultName = 'app:user:user-profile-views:fetch';
    private EntityManagerInterface $em;
    private Client $turnover;

    public function __construct(EntityManagerInterface $em, Client $turnover)
    {
        parent::__construct();
        $this->em = $em;
        $this->turnover = $turnover;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Fetch UserProfileViews of a day')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, '', 'yesterday')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $io = new SymfonyStyle($input, $output);
        $io->title('App - User - Create UserProfileViews of the last day');

        if ('yesterday' === $date = $input->getOption('date')) {
            $startsAt = Carbon::yesterday();
        } elseif (false === $startsAt = Carbon::createFromFormat('Y-m-d', $date)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid format', $date));
        }

        // 0 - determine day
        $startsAt->startOfDay();
        $endsEnd = $startsAt->copy()->endOfDay();

        $io->info(sprintf('Date: %s', $startsAt->format('Y-m-d')));

        // 1 - fetch data to delete and create
        $userProfileViewsToDelete = $this->em->getRepository(UserProfileViews::class)->findBy(['date' => $startsAt]);
        $userProfileViewsToDeleteCount = \count($userProfileViewsToDelete);

        $userProfileViewsToCreate = $this->turnover->getUserProfileViews($startsAt, $endsEnd);
        $userProfileViewsToCreateCount = \count($userProfileViewsToCreate);

        if (0 !== $userProfileViewsToDeleteCount || 0 !== $userProfileViewsToCreateCount) {
            $io->info([
                sprintf('%d UserProfileViews to delete.', $userProfileViewsToDeleteCount),
                sprintf('%d UserProfileViews to create.', $userProfileViewsToCreateCount),
            ]);
        } else {
            $io->info('Nothing to delete and create.');

            return Command::SUCCESS;
        }

        if (false === $input->getOption('no-interaction') && false === $io->confirm('Confirm ?')) {
            return Command::SUCCESS;
        }

        // 2 - delete UserProfileViews of the day
        if ($userProfileViewsToDeleteCount > 0) {
            foreach ($userProfileViewsToDelete as $uv) {
                $this->em->remove($uv);
            }
            $this->em->flush();
        }

        $io->success(sprintf('%d UserProfileViews deleted.', $userProfileViewsToDeleteCount));

        // 3 - create UserProfileViews of the day
        $userProfileViewsCreated = $userProfileViewsSkipped = 0;

        if ($userProfileViewsToCreateCount > 0) {
            $emails = Arrays::map($userProfileViewsToCreate, function (array $d) {
                return $d['email'];
            });

            $users = [];
            foreach ($this->em->getRepository(User::class)->findBy(['email' => $emails]) as $user) {
                /* @var User $user */
                $users[$user->getEmail()] = $user;
            }

            foreach ($userProfileViewsToCreate as $uv) {
                if (false === isset($users[$uv['email']])) {
                    ++$userProfileViewsSkipped;
                    continue;
                }

                $userProfileViews = (new UserProfileViews())
                    ->setUser($users[$uv['email']])
                    ->setCount($uv['views'])
                    ->setDate($startsAt)
                ;

                $this->em->persist($userProfileViews);
                ++$userProfileViewsCreated;
            }

            $this->em->flush();
        }

        if ($userProfileViewsCreated > 0) {
            $io->success(sprintf('%d UserProfileViews created.', $userProfileViewsCreated));
        }

        if ($userProfileViewsSkipped > 0) {
            $io->warning(sprintf('%d UserProfileViews skipped (email not found in the database).', $userProfileViewsSkipped));
        }

        $end = microtime(true);
        $duration = $end - $start;

        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }
}
