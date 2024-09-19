<?php

namespace App\Core\Command;

use Gaufrette\Filesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GenerateSiteMapCommand extends Command
{
    protected static $defaultName = 'app:core:generate-sitemap';
    private HttpClientInterface $client;
    private Filesystem $filesystem;
    private string $amazonS3Cloudfront;
    private string $candidatesFullUrl;

    public function __construct(
        FilesystemMap $filesystemMap,
        HttpClientInterface $client,
        string $amazonS3Cloudfront,
        string $candidatesScheme,
        string $candidatesBaseUrl
    ) {
        parent::__construct();
        $this->filesystem = $filesystemMap->get('sitemap_fs');
        $this->client = $client;
        $this->amazonS3Cloudfront = $amazonS3Cloudfront;
        $this->candidatesFullUrl = $candidatesScheme . '://' . $candidatesBaseUrl . '/';
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate sitemap into static s3')
            ->addOption('htaccess_login', null, InputOption::VALUE_OPTIONAL)
            ->addOption('htaccess_password', null, InputOption::VALUE_OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $options = [];

        $io = new SymfonyStyle($input, $output);
        $io->title('App - Core - Generate Sitemap');

        if (null !== $input->getOption('htaccess_login') && null !== $input->getOption('htaccess_password')) {
            $options = ['auth_basic' => [$input->getOption('htaccess_login'), $input->getOption('htaccess_password')]];
        }

        $dom = new \DOMDocument();
        $response = $this->client->request('GET', $this->candidatesFullUrl . 'sitemapindex_origin.xml', $options);

        $dom->loadXML($response->getContent());
        $root = $dom->documentElement;

        if ($root instanceof \DOMElement) {
            $sitemaps = $root->getElementsByTagName('sitemap');

            $io->progressStart($sitemaps->length + 2);

            foreach ($sitemaps as $sitemap) {
                $loc = $sitemap->getElementsByTagName('loc')[0];
                $filePath = $loc->nodeValue;
                $fileName = htmlentities(substr($filePath, strrpos($filePath, '/') + 1));

                $fileResponse = $this->client->request('GET', $filePath, $options);
                $this->filesystem->write($fileName, $fileResponse->getContent(), true);

                $loc->nodeValue = $this->amazonS3Cloudfront . '/' . $fileName;

                $io->progressAdvance();
            }

            $indexXML = $dom->saveXML();
            if (false !== $indexXML) {
                $this->filesystem->write('sitemapindex.xml', $indexXML, true);
            } else {
                $io->warning("Can't save sitemapindex.xml.");
            }
            $io->progressAdvance();

            $xslResponse = $this->client->request('GET', $this->candidatesFullUrl . 'sitemap.xsl', $options);
            $this->filesystem->write('sitemap.xsl', $xslResponse->getContent(), true);

            $io->progressFinish();
        } else {
            $io->warning('DOMDocument is null.');
        }

        $end = microtime(true);
        $duration = $end - $start;

        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }
}
