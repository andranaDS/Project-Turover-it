<?php

namespace App\FeedRss\Entity;

use App\Company\Entity\Company;
use App\FeedRss\Repository\FeedRssBlacklistCompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FeedRssBlacklistCompanyRepository::class)
 * @UniqueEntity(
 *     fields={"company", "feedRss"},
 *     message="There cannot be 2  same blacklisted companies for the same feed."
 * )
 */
class FeedRssBlacklistCompany
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class)
     * @Assert\NotNull(message="generic.not_null")
     */
    private ?Company $company = null;

    /**
     * @ORM\ManyToOne(targetEntity=FeedRss::class, inversedBy="blacklistCompanies")
     * @Assert\NotNull(message="generic.not_null")
     */
    private ?FeedRss $feedRss = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getFeedRss(): ?FeedRss
    {
        return $this->feedRss;
    }

    public function setFeedRss(?FeedRss $feedRss): self
    {
        $this->feedRss = $feedRss;

        return $this;
    }

    public function __toString(): string
    {
        return null !== $this->getCompany()
            ? $this->getCompany()->getName() ?? ''
            : ''
        ;
    }
}
