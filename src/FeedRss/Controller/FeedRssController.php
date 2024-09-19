<?php

namespace App\FeedRss\Controller;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Handler\FeedRssHandler;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedRssController
{
    /**
     * @Route(
     *     "/flux-rss/free-work-rss-{slug}.xml",
     *     name="flux-rss", methods={"GET"},
     *     defaults={"_format"="xml"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     * @Cache(smaxage="0", maxage="0")
     */
    public function __invoke(
        string $slug,
        string $projectDir,
        EntityManagerInterface $entityManager,
        FeedRssHandler $feedRssHandler
    ): Response {
        if (null === $feedRss = $entityManager->getRepository(FeedRss::class)->findOneBySlug($slug)) {
            return new Response('<xml><error>This feed ' . $slug . ' is not supported.</error></xml>');
        }

        $fileSystem = new Filesystem();
        $file = $projectDir . '/public/feed_rss/free-work-rss-' . $feedRss->getSlug() . '.xml';

        if (false === $fileSystem->exists($file)) {
            try {
                $feedRssHandler->process($feedRss);
            } catch (\Exception $e) {
                return new Response('<xml><error>An error has occurred in the generation of the feed</error></xml>');
            }
        }
        $response = new BinaryFileResponse($file);

        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
