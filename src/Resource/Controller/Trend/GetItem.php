<?php

namespace App\Resource\Controller\Trend;

use App\Resource\Entity\Trend;
use App\Resource\Manager\TrendManager;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetItem
{
    public function __invoke(string $date, TrendManager $tm): Trend
    {
        $maxResults = 20;

        if ('last' === $date) {
            $trend = $tm->getLastTrend(true, $maxResults);
        } else {
            try {
                if (false !== $date = Carbon::createFromFormat('Y-m-d', $date)) {
                    $date->startOfDay();
                    $trend = $tm->getDateTrend($date, true, $maxResults);
                } else {
                    $trend = null;
                }
            } catch (\Exception $e) {
                $trend = null;
            }
        }

        if (null === $trend) {
            throw new NotFoundHttpException();
        }

        return $trend;
    }
}
