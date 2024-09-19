<?php

namespace App\Core\Twig;

use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoutingExtension extends AbstractExtension
{
    private UrlGeneratorInterface $generator;
    private UrlSignerInterface $urlSigner;

    public function __construct(UrlGeneratorInterface $generator, UrlSignerInterface $urlSigner)
    {
        $this->generator = $generator;
        $this->urlSigner = $urlSigner;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('path_signed', [$this, 'getSignedPath']),
            new TwigFunction('url_signed', [$this, 'getSignedUrl']),
        ];
    }

    public function getSignedPath(string $name, array $parameters = [], bool $relative = false, int $duration = 3600): string
    {
        $path = $this->generator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);

        return $this->signed($path, $duration);
    }

    public function getSignedUrl(string $name, array $parameters = [], bool $schemeRelative = false, int $duration = 3600): string
    {
        $url = $this->generator->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->signed($url, $duration);
    }

    private function signed(string $url, int $duration = 3600): string
    {
        $expiration = (new \DateTime('now'))->modify("+$duration seconds");

        return $this->urlSigner->sign($url, $expiration);
    }
}
