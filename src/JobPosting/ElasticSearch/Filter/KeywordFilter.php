<?php

namespace App\JobPosting\ElasticSearch\Filter;

use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;
use Elastica\Query\FunctionScore;
use Elastica\Query\MatchPhrase;
use Elastica\Query\MatchQuery;
use Elastica\Query\Nested;
use Elastica\Query\Terms;

class KeywordFilter
{
    public function build(array $keywords): AbstractQuery
    {
        $functionScore = new FunctionScore();

        $functionScore->addDecayFunction('exp', 'publishedAt', 'now', '14d', '2d');

        $boolQuery = new BoolQuery();

        foreach ($keywords as $keyword) {
            $boolQuery->addShould($this->addKeyword($keyword));
        }

        $boolQuery->setMinimumShouldMatch(1);

        $functionScore->setQuery($boolQuery);

        return $functionScore;
    }

    private function addKeyword(string $keyword): AbstractQuery
    {
        $boolQuery = new BoolQuery();

        $titlePhraseQuery = new MatchPhrase(); // match keyword inside title
        $titlePhraseQuery->setFieldQuery('title.french', $keyword);
        $titlePhraseQuery->setFieldBoost('title.french', 150);

        $descPhraseQuery = new MatchPhrase(); // match keyword inside description
        $descPhraseQuery->setFieldQuery('description.french', $keyword);

        $companyNameQuery = new MatchQuery(); // match keyword inside company name
        $companyNameQuery->setFieldQuery('company.name', $keyword);
        $companyNameQuery->setFieldOperator('company.name', 'and');
        $companyNameQuery->setFieldFuzziness('company.name', 'auto:4,6');
        $companyNameQuery->setFieldBoost('company.name', 30); // we boost the company

        $titleQuery = new MatchQuery(); // match keyword inside title
        $titleQuery->setFieldQuery('title', $keyword);
        $titleQuery->setFieldOperator('title', 'and');
        $titleQuery->setFieldFuzziness('title', 'auto:4,6');
        $titleQuery->setFieldBoost('title', 10); // we boost the title

        $companyNameNestedQuery = new Nested();
        $companyNameNestedQuery->setPath('company');
        $companyNameNestedQuery->setQuery($companyNameQuery);

        $skillsNestedQuery = new Nested();
        $skillsNestedQuery->setPath('skills');
        $skillsNestedQuery->setQuery((new Terms('skills.name', [$keyword]))->setBoost(10));
        /* TODO: Add boost on skill name ? */

        // we add our match in our bool query
        $boolQuery->addShould($titlePhraseQuery);
        $boolQuery->addShould($descPhraseQuery);
        $boolQuery->addShould($companyNameQuery);
        $boolQuery->addShould($titleQuery);
        $boolQuery->addShould($companyNameNestedQuery);
        $boolQuery->addShould($skillsNestedQuery);
        $boolQuery->setMinimumShouldMatch(1);

        return $boolQuery;
    }
}
