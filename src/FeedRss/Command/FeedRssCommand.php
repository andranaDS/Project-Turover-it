<?php

namespace App\FeedRss\Command;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Handler\FeedRssHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FeedRssCommand extends Command
{
    protected static $defaultName = 'app:core:generate-feed-rss';

    private EntityManagerInterface $entityManager;
    private FeedRssHandler $handler;

    public function __construct(EntityManagerInterface $entityManager, FeedRssHandler $handler)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create Rss Feed')
            ->addArgument('slugs', InputArgument::IS_ARRAY, 'Type of the requested Feed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $io = new SymfonyStyle($input, $output);
        $feeds = [];

        if (empty($input->getArgument('slugs'))) {
            $feeds = $this->entityManager->getRepository(FeedRss::class)->findAll();
        } else {
            foreach ($input->getArgument('slugs') as $slug) {
                if (null === $feed = $this->entityManager->getRepository(FeedRss::class)->findOneBySlug($slug)) {
                    $io->error(sprintf('the RSS feed with slug %s does not exist.', $slug));
                    continue;
                }

                $feeds[] = $feed;
            }
        }

        foreach ($feeds as $feed) {
            try {
                $this->handler->process($feed);

                $io->info(sprintf('the rss feed %s has been generated.', $feed->getName()));
            } catch (\Exception $e) {
                $io->error(sprintf('an error occurred during the generation of the RSS feed %s.', $feed->getName()));
            }
        }

        $end = microtime(true);
        $duration = $end - $start;

        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }
}
