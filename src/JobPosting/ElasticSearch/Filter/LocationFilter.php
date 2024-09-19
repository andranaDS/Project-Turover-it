<?php

namespace App\JobPosting\ElasticSearch\Filter;

use App\Core\Entity\Location;
use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;
use Elastica\Query\Nested;
use Elastica\Query\Term;

class LocationFilter
{
    public function build(array $locationsKeys): AbstractQuery
    {
        $boolQuery = new BoolQuery();

        foreach ($locationsKeys as $locationsKey) {
            $boolQuery->addShould($this->addLocation($locationsKey));
        }

        $boolQuery->setMinimumShouldMatch(1);

        return $boolQuery;
    }

    public function addLocation(string $locationKey): AbstractQuery
    {
        $boolQuery = new BoolQuery();

        $location = Location::explodeKey($locationKey);

        foreach ($location as $p => $v) {
            $query = new Term();
            $query->setTerm(sprintf('location.%s', $p), $v);
            $boolQuery->addMust($query);
        }

        $query = new Nested();
        $query->setPath('location');
        $query->setQuery($boolQuery);

        return $query;
    }
}
