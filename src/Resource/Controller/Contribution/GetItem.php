<?php

namespace App\Resource\Controller\Contribution;

use App\Resource\Entity\Contribution;
use App\Resource\Entity\ContributionCard;
use App\Resource\Repository\ContributionRepository;

class GetItem
{
    public function __invoke(Contribution $contribution, ContributionRepository $contributionRepository): ContributionCard
    {
        return new ContributionCard(
            $contribution,
            $contributionRepository->getPreviousFromContribution($contribution),
            $contributionRepository->getNextFromContribution($contribution)
        );
    }
}
