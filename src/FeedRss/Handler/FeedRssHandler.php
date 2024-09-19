<?php

namespace App\FeedRss\Handler;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Handler\Templates\AbstractFeedRssTemplate;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

class FeedRssHandler
{
    private iterable $feedRssServices;
    private Environment $twig;
    private string $projectDir;

    public function __construct(
        iterable $feedRssServices,
        Environment $twig,
        string $projectDir
    ) {
        $this->feedRssServices = $feedRssServices;
        $this->twig = $twig;
        $this->projectDir = $projectDir;
    }

    public function process(FeedRss $feedRss): void
    {
        foreach ($this->feedRssServices as $service) {
            /** @var AbstractFeedRssTemplate $service */
            if ($this->canSupport($feedRss, $service)) {
                $jobPostings = $service->getJobPostings($feedRss);
                $jobPostings = $service->filterJobPostings($jobPostings);
                $jobPostingsDTO = $service->generateFeedRssData($feedRss, $jobPostings);

                $fileSystem = new Filesystem();
                $xmlContent = $this->twig->render('@feedRss/free-work-rss-generic.xml.twig', [
                    'jobPostings' => $jobPostingsDTO,
                ]);

                $finalPath = $this->projectDir . '/public/feed_rss/free-work-rss-' . $feedRss->getSlug() . '.xml';
                $fileSystem->dumpFile($finalPath, $xmlContent);

                return;
            }
        }

        throw new \RuntimeException(sprintf('The template for the feed #%d does not exist', $feedRss->getId()));
    }

    private function canSupport(FeedRss $feedRss, AbstractFeedRssTemplate $service): bool
    {
        return $feedRss->getType() === $service->getType() && $feedRss->getPartner() === $service->getPartner();
    }
}
