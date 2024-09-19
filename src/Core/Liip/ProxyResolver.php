<?php

namespace App\Core\Liip;

use Liip\ImagineBundle\Imagine\Cache\Resolver\AwsS3Resolver;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ProxyResolver as LiipProxyResolver;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;

class ProxyResolver extends LiipProxyResolver
{
    protected string $s3Bucket;

    public function __construct(ResolverInterface $resolver, array $hosts, string $s3Bucket)
    {
        parent::__construct($resolver, $hosts);
        $this->s3Bucket = $s3Bucket;

        if ($this->resolver instanceof AwsS3Resolver) {
            $this->resolver->setCachePrefix('thumbnails');
        }
    }

    // @phpstan-ignore-next-line
    protected function rewriteUrl($url): array|string|null
    {
        if (empty($this->hosts)) {
            return $url;
        }

        $randKey = array_rand($this->hosts, 1);

        // BC
        if (is_numeric($randKey)) {
            $port = parse_url($url, \PHP_URL_PORT);
            $host = parse_url($url, \PHP_URL_SCHEME) . '://' . parse_url($url, \PHP_URL_HOST) . ($port ? ':' . $port : '');
            $proxyHost = $this->hosts[$randKey];

            // hack to remove s3 bucket name part in cloudfront url
            return str_replace([$host, $this->s3Bucket . '/'], [$proxyHost, ''], $url);
        }

        if (0 === mb_strpos($randKey, 'regexp/')) {
            $regExp = mb_substr($randKey, 6);

            return preg_replace($regExp, $this->hosts[$randKey], $url);
        }

        return str_replace($randKey, $this->hosts[$randKey], $url);
    }
}
