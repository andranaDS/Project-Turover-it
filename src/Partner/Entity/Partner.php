<?php

namespace App\Partner\Entity;

use App\Partner\Enum\Partner as PartnerEnum;
use App\Partner\Repository\PartnerRepository;
use App\Partner\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PartnerRepository::class)
 * @UniqueEntity(fields={"partner"}, message="partner.type_partner_unique")
 * @AppAssert\PartnerDistribution()
 */
class Partner
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get:private", "user:patch:job_search_preferences"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Assert\Length(maxMessage="generic.length.max", max=16)
     * @EnumAssert(message="generic.enum.message", class=PartnerEnum::class)
     */
    private ?string $partner = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @Assert\Range(min=0, max=100)
     * @Assert\NotNull(message="generic.not_blank")
     */
    private int $distribution = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=255)
     * @Assert\Url(message="generic.url")
     * @Assert\NotNull(message="generic.not_blank")
     */
    private ?string $apiUrl = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDistribution(): int
    {
        return $this->distribution;
    }

    public function setDistribution(int $distribution): self
    {
        $this->distribution = $distribution;

        return $this;
    }

    public function getApiUrl(): ?string
    {
        return $this->apiUrl;
    }

    public function setApiUrl(?string $apiUrl): self
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }
}
