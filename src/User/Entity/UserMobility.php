<?php

namespace App\User\Entity;

use App\Core\Entity\Location;
use App\User\Repository\UserMobilityRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserMobilityRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"location_value"})})
 */
class UserMobility
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Embedded(class="App\Core\Entity\Location")
     * @Groups({"user:get:private", "location", "user:put", "user:legacy", "user:patch:job_search_preferences", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     */
    private ?Location $location;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="locations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    public ?User $user = null;

    public function __construct()
    {
        $this->location = new Location();
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(): string
    {
        return ($this->location && null !== $this->location->getLabel()) ? $this->location->getLabel() : '';
    }
}
