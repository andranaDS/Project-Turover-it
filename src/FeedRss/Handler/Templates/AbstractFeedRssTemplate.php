<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Entity\FeedRssForbiddenWord;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingUserTrace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractFeedRssTemplate implements FeedRssTemplateInterface
{
    protected EntityManagerInterface $entityManager;
    protected string $candidatesScheme;
    protected string $candidatesBaseUrl;
    private RouterInterface $router;
    protected string $feedRSSPublisher;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $candidatesScheme,
        string $candidatesBaseUrl,
        RouterInterface $router,
        string $feedRSSPublisher
    ) {
        $this->entityManager = $entityManager;
        $this->candidatesScheme = $candidatesScheme;
        $this->candidatesBaseUrl = $candidatesBaseUrl;
        $this->router = $router;
        $this->feedRSSPublisher = $feedRSSPublisher;
    }

    abstract public function getType(): string;

    abstract public function getPartner(): string;

    public function getXmlConfig(): array
    {
        return [
            'template' => 'dynamic',
            'rootName' => 'source',
            'paramNameElementFlux' => 'job',
            'fieldsBeforeList' => [
                'publisher' => $this->feedRSSPublisher,
                'publisherurl' => $this->candidatesScheme . '://' . $this->candidatesBaseUrl,
                'lastBuildDate' => date('r'),
            ],
        ];
    }

    public function getJobPostings(FeedRss $feedRss): array
    {
        return $this->entityManager->getRepository(JobPosting::class)->findForFeed($feedRss);
    }

    public function filterJobPostings(array $jobPostings): array
    {
        $forbiddenWords = $this->entityManager->getRepository(FeedRssForbiddenWord::class)->getForbiddenWords();
        $jobPostingIds = [];

        /** @var JobPosting $jobPosting */
        foreach ($jobPostings as $key => $jobPosting) {
            if (
                false === self::checkTitle($jobPosting->getTitle(), $forbiddenWords) ||
                false === self::checkDescription($jobPosting->getDescription())
            ) {
                unset($jobPostings[$key]);
                continue;
            }

            $jobPostingIds[] = $jobPosting->getId();
        }

        $jobPostingsTraceCount = $this->entityManager->getRepository(JobPostingUserTrace::class)->findByJobPostingIds($jobPostingIds, 1500);

        foreach ($jobPostingsTraceCount as $jobPosting) {
            unset($jobPosting[$jobPosting->getId()]);
        }

        return $jobPostings;
    }

    private static function cleanString(string $string): string
    {
        $string = strtr($string, '�����������������������������������������������������', 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn');
        $string = strtolower($string);
        $string = preg_replace('[^a-zA-Z ]', ' ', $string);
        $string = str_replace('rsquo', ' ', (string) $string);
        $string = preg_replace('/\s\s+/', ' ', $string);

        return trim((string) $string);
    }

    private static function checkTitle(?string $title, array $forbiddenWords): bool
    {
        $title = self::cleanString((string) $title);

        foreach ($forbiddenWords as $forbiddenWord) {
            if (false !== strpos($title, strtolower($forbiddenWord))) {
                return false;
            }
        }

        return true;
    }

    private static function checkDescription(?string $description): bool
    {
        $words = ['month', ' the ', ' with ', ' at '];

        foreach ($words as $word) {
            if (false !== strpos((string) $description, $word)) {
                return false;
            }
        }

        return true;
    }

    public function generateFeedRssData(FeedRss $feedRss, array $jobPostings): iterable
    {
        $fluxData = [];
        $classToCall = $this->getDTOClass();

        if (false === class_exists($this->getDTOClass())) {
            throw new \RuntimeException(sprintf('%s does not exist', $this->getDTOClass()));
        }

        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, []);

        $jobPostingsDTO = [];

        /** @var JobPosting $jobPosting */
        foreach ($jobPostings as $jobPosting) {
            $jobPostingsDTO[] = new $classToCall($jobPosting, $feedRss, $this->router);
        }

        $fluxData['items'] = $serializer->normalize($jobPostingsDTO);
        $fluxData['config'] = $this->getXmlConfig();

        return $fluxData;
    }
}
