<?php

namespace App\Resource\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Resource\Repository\TrendSkillTableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TrendSkillTableRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"trend:get"}},
 *     itemOperations={"get"},
 *     collectionOperations={},
 * )
 */
class TrendSkillTable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\OneToMany(targetEntity=TrendSkillLine::class, mappedBy="table", cascade={"persist", "remove"})
     * @Groups({"trend:get"})
     */
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|TrendSkillLine[]
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(TrendSkillLine $line): self
    {
        if (!$this->lines->contains($line)) {
            $this->lines[] = $line;
            $line->setTable($this);
        }

        return $this;
    }

    public function removeLine(TrendSkillLine $line): self
    {
        // set the owning side to null (unless already changed)
        if ($this->lines->removeElement($line) && $line->getTable() === $this) {
            $line->setTable(null);
        }

        return $this;
    }
}
