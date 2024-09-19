<?php

namespace App\Core\UrlSigner;

use CoopTilleuls\UrlSignerBundle\UrlSigner\AbstractUrlSigner;
use League\Uri\QueryString;
use Psr\Http\Message\UriInterface;

class FreeWorkUrlSigner extends AbstractUrlSigner
{
    public const MAILJET_URL_QUERY_PARAMS = [
        'utm_campaign',
        'utm_medium',
        'utm_source',
        'utm_subject',
    ];

    /**
     * Retrieve the intended URL by stripping off the UrlSigner specific parameters.
     */
    protected function getIntendedUrl(UriInterface $url): UriInterface
    {
        $intendedQuery = QueryString::extract($url->getQuery());

        unset($intendedQuery[$this->expiresParameter], $intendedQuery[$this->signatureParameter]);

        foreach (self::MAILJET_URL_QUERY_PARAMS as $param) {
            if (isset($intendedQuery[$param])) {
                unset($intendedQuery[$param]);
            }
        }

        return $url->withQuery((string) $this->buildQueryStringFromArray($intendedQuery));
    }

    /**
     * Generate a token to identify the secure action.
     */
    protected function createSignature($url, $expiration): string
    {
        $url = (string) $url;

        return md5("{$url}::{$expiration}::{$this->signatureKey}");
    }

    public static function getName(): string
    {
        return 'free-work';
    }
}
