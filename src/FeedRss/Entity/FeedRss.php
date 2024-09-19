<?php

namespace App\FeedRss\Entity;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Repository\FeedRssRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FeedRssRepository::class)
 * @UniqueEntity(fields={"type", "partner"}, message="feed_rss.type_partner_unique")
 * @UniqueEntity(fields={"name"}, message="feed_rss.name_unique")
 * @UniqueEntity(fields={"slug"}, message="feed_rss.name_unique")
 */
class FeedRss
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=16)
     * @EnumAssert(message="generic.enum.message", class=FeedRssType::class)
     */
    private ?string $type = null;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=32)
     * @EnumAssert(message="generic.enum.message", class=FeedRssPartner::class)
     */
    private ?string $partner = null;

    /**
     * @ORM\Column(type="string", length=2500, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=2500)
     */
    private ?string $gaTag = null;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=128)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private ?string $slug = null;

    /**
     * @ORM\OneToMany(targetEntity=FeedRssBlacklistCompany::class, mappedBy="feedRss", cascade={"persist", "remove"})
     */
    private Collection $blacklistCompanies;

    public function __construct()
    {
        $this->blacklistCompanies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getGaTag(): ?string
    {
        return $this->gaTag;
    }

    public function setGaTag(?string $gaTag): self
    {
        $this->gaTag = $gaTag;

        return $this;
    }

    /**
     * @return Collection|FeedRssBlacklistCompany[]
     */
    public function getBlacklistCompanies(): Collection
    {
        return $this->blacklistCompanies;
    }

    public function addBlacklistCompany(FeedRssBlacklistCompany $blacklistCompany): self
    {
        if (!$this->blacklistCompanies->contains($blacklistCompany)) {
            $this->blacklistCompanies[] = $blacklistCompany;
            $blacklistCompany->setFeedRss($this);
        }

        return $this;
    }

    public function removeBlacklistCompany(FeedRssBlacklistCompany $blacklistCompany): self
    {
        if ($this->blacklistCompanies->removeElement($blacklistCompany)) {
            // set the owning side to null (unless already changed)
            if ($blacklistCompany->getFeedRss() === $this) {
                $blacklistCompany->setFeedRss(null);
            }
        }

        return $this;
    }

    public function getPartner(): ?string
    {
        return $this->partner;
    }

    public function setPartner(?string $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
