<?php

namespace App\Resource\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

class ContributionCard
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"contribution:get"})
     */
    private Contribution $contribution;

    /**
     * @Groups({"contribution:get"})
     */
    private ?Contribution $previousContribution;
    /**
     * @Groups({"contribution:get"})
     */
    private ?Contribution $nextContribution;

    public function __construct(Contribution $contribution, ?Contribution $previousContribution, ?Contribution $nextContribution)
    {
        $this->contribution = $contribution;
        $this->previousContribution = $previousContribution;
        $this->nextContribution = $nextContribution;
    }

    public function getContribution(): Contribution
    {
        return $this->contribution;
    }

    public function getPreviousContribution(): ?Contribution
    {
        return $this->previousContribution;
    }

    public function getNextContribution(): ?Contribution
    {
        return $this->nextContribution;
    }
}
