<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\Core\Util\Arrays;
use App\JobPosting\ElasticSearch\JobPostingsFilters\JobPostingsFilters;
use App\JobPosting\ElasticSearch\JobPostingsFilters\JobPostingsJobPostingSearchFiltersBuilder;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingSearch;
use App\JobPosting\Entity\JobPostingSearchLocation;
use App\User\Email\AlertUserMissionsEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozarts\Console\Parallelization\ContainerAwareCommand;
use Webmozarts\Console\Parallelization\Parallelization;
use Webmozarts\Console\Parallelization\ParallelizationInput;

class AlertUserMissionsCommand extends ContainerAwareCommand
{
    use Parallelization;

    protected static $defaultName = 'app:alert:missions';
    private EntityManagerInterface $em;
    private Mailer $mailer;
    private JobPostingsJobPostingSearchFiltersBuilder $jobPostingFiltersBuilder;
    private RouterInterface $router;

    public function __construct(
        EntityManagerInterface $em,
        JobPostingsJobPostingSearchFiltersBuilder $jobPostingFiltersBuilder,
        Mailer $mailer,
        RouterInterface $router
    ) {
        parent::__construct();
        $this->em = $em;
        $this->mailer = $mailer;
        $this->jobPostingFiltersBuilder = $jobPostingFiltersBuilder;
        $this->router = $router;
    }

    protected function configure(): void
    {
        $this->setDescription('Send JobPostings to users in relation to their alerts');

        self::configureParallelization($this);
    }

    protected function fetchItems(InputInterface $input): array
    {
        return Arrays::map(
            $this->em->getRepository(User::class)->findAllWithActiveJobPostingSearchByAlertMissionDate(Carbon::today()),
            static function (User $user) {
                return $user->getEmail();
            });
    }

    protected function getItemName(int $count): string
    {
        return 1 === $count ? 'user' : 'users';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $parallelizationInput = new ParallelizationInput($input);

        $start = microtime(true);

        if ($parallelizationInput->isChildProcess()) {
            $this->executeChildProcess($input, $output);

            return Command::SUCCESS;
        }

        $this->executeMasterProcess($parallelizationInput, $input, $output);

        $end = microtime(true);
        $duration = $end - $start;
        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }

    protected function runAfterBatch(InputInterface $input, OutputInterface $output, array $items): void
    {
        $this->em->flush();
    }

    protected function runSingleCommand(string $email, InputInterface $input, OutputInterface $output): void
    {
        $now = Carbon::now();

        $start = Carbon::yesterday();
        $end = $start->copy()->endOfDay();
        $io = new SymfonyStyle($input, $output);
        $user = $this->em->getRepository(User::class)->findOneByEmail($email);
        $data = [];
        $jobPostingCount = 0;

        foreach ($this->em->getRepository(JobPostingSearch::class)->findByUserWithData($user) as $jobPostingSearch) {
            $filters = $this->jobPostingFiltersBuilder->build($jobPostingSearch)
                ->setPublishedAfter($start)
                ->setPublishedBefore($end)
            ;

            $jobPostings = $this->em->getRepository(JobPosting::class)->findSearch($filters, 1);

            if (0 === \count($jobPostings)) {
                continue;
            }

            $data[] = [
                'jobPostings' => $jobPostings,
                'jobPostingSearch' => $jobPostingSearch,
                'jobPostingSearchUrl' => $this->generateCandidatesJobPostingSearchUrl($jobPostingSearch),
            ];
            $jobPostingCount += \count($jobPostings);
        }

        if (0 !== $jobPostingCount && null !== $user->getEmail()) {
            $email = (new AlertUserMissionsEmail())
                ->to($user->getEmail())
                ->context([
                    'user' => $user,
                    'data' => $data,
                    'jobPostingCount' => $jobPostingCount,
                    'urlSignedTll' => 86400,
                ])
            ;

            try {
                $this->mailer->send($email);
            } catch (\Exception $e) {
                $io->error($e->getMessage());
            }
        }

        if (null !== $user->getData()) {
            $user->getData()->setCronAlertMissionsExecAt($now);
        }
    }

    protected function getBatchSize(): int
    {
        return 64;
    }

    protected function getSegmentSize(): int
    {
        return 64;
    }

    private function generateCandidatesJobPostingSearchUrl(JobPostingSearch $jobPostingSearch): string
    {
        $params = [
            'sort' => JobPostingsFilters::ORDER_DATE,
        ];

        if (null !== $jobPostingSearch->getSearchKeywords()) {
            $params['query'] = $jobPostingSearch->getSearchKeywords();
        }

        if (false === empty($jobPostingSearch->getContracts())) {
            $params['contracts'] = implode(',', $jobPostingSearch->getContracts());
        }

        if (null !== $jobPostingSearch->getRemoteMode()) {
            $params['remote'] = implode(',', $jobPostingSearch->getRemoteMode());
        }

        if (null !== $jobPostingSearch->getPublishedSince()) {
            $params['freshness'] = $jobPostingSearch->getPublishedSince();
        }

        if (null !== $jobPostingSearch->getMinAnnualSalary()) {
            $params['min_salary'] = $jobPostingSearch->getMinAnnualSalary();
        }

        if (null !== $jobPostingSearch->getMinDailySalary()) {
            $params['min_daily_rate'] = $jobPostingSearch->getMinDailySalary();
        }

        if (false === $jobPostingSearch->getLocations()->isEmpty()) {
            $params['locations'] = implode(
                ',',
                $jobPostingSearch->getLocations()
                    ->filter(static function (JobPostingSearchLocation $jobPostingSearchLocation) {
                        return null !== $jobPostingSearchLocation->getLocation();
                    })
                    ->map(static function (JobPostingSearchLocation $jobPostingSearchLocation) {
                        return $jobPostingSearchLocation->getLocation()?->getKey();
                    })->getValues()
            );
        }

        if (null !== $jobPostingSearch->getMinDuration() || null !== $jobPostingSearch->getMaxDuration()) {
            $params['duration'] = sprintf(
                '%d,%d',
                $jobPostingSearch->getMinDuration() ?? 1,
                $jobPostingSearch->getMaxDuration() ?? 48
            );
        }

        return sprintf('%s?%s', $this->router->generate('candidates_job_postings', [], UrlGeneratorInterface::ABSOLUTE_URL), http_build_query($params));
    }
}
