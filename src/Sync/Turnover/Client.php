<?php

namespace App\Sync\Turnover;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private HttpClientInterface $turnover;

    public function __construct(HttpClientInterface $turnoverItClient)
    {
        $this->turnover = $turnoverItClient;
    }

    public function getUserProfileViews(?\DateTime $start = null, ?\DateTime $end = null): array
    {
        $response = $this->turnover->request('GET', 'resumes/views', [
            'query' => array_filter([
                'start' => null !== $start ? $start->getTimestamp() : null,
                'end' => null !== $end ? $end->getTimestamp() : null,
            ]),
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('The status code of the TurnoverIT API response is not a 200 (%s instead).', $response->getStatusCode()));
        }

        return $response->toArray();
    }

    public function getTrendSkills(array $values, ?\DateTime $start = null, ?\DateTime $end = null): array
    {
        $response = $this->turnover->request('GET', 'trends/skills', [
            'query' => array_filter([
                'start' => null !== $start ? $start->getTimestamp() : null,
                'end' => null !== $end ? $end->getTimestamp() : null,
            ]),
            'json' => [
                'values' => $values,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('The status code of the TurnoverIT API response is not a 200 (%s instead).', $response->getStatusCode()));
        }

        return $response->toArray();
    }

    public function getTrendJobs(array $values, ?\DateTime $start = null, ?\DateTime $end = null): array
    {
        $response = $this->turnover->request('GET', 'trends/jobs', [
            'query' => array_filter([
                'start' => null !== $start ? $start->getTimestamp() : null,
                'end' => null !== $end ? $end->getTimestamp() : null,
            ]),
            'json' => [
                'values' => $values,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('The status code of the TurnoverIT API response is not a 200 (%s instead).', $response->getStatusCode()));
        }

        return $response->toArray();
    }

    public function getJobPostings(?\DateTime $minUpdatedAt = null, ?int $limit = null, array $properties = [], ?bool $published = null): array
    {
        if (true === $published) {
            $active = '1';
        } elseif (false === $published) {
            $active = '0';
        } else {
            $active = null;
        }

        $query = array_filter([
            'min_update_date' => null !== $minUpdatedAt ? $minUpdatedAt->getTimestamp() : null,
            'limit' => $limit,
            'properties' => 0 !== \count($properties) ? implode(',', $properties) : null,
            'active' => $active,
        ], static function ($d) { // @phpstan-ignore-line
            return null !== $d;
        });

        $response = $this->turnover->request('GET', 'jobs', [
            'query' => $query,
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Turnover API job_postings not available.');
        }

        return $response->toArray();
    }

    public function getCompanies(?\DateTime $minUpdatedAt = null, ?int $limit = null): array
    {
        $response = $this->turnover->request('GET', 'companies', [
            'query' => array_filter([
                'min_update_date' => null !== $minUpdatedAt ? $minUpdatedAt->getTimestamp() : null,
                'limit' => $limit,
            ]),
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Turnover API companies not available.');
        }

        return $response->toArray();
    }

    public function getApplications(?\DateTime $minUpdatedAt = null, ?int $limit = null): array
    {
        $response = $this->turnover->request('GET', 'applications/views', [
            'query' => array_filter([
                'min_update_date' => null !== $minUpdatedAt ? $minUpdatedAt->getTimestamp() : null,
                'limit' => $limit,
            ]),
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Turnover API applications not available.');
        }

        return $response->toArray();
    }
}
