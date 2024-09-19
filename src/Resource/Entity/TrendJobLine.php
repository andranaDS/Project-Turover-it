<?php

namespace App\Resource\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Entity\Job;
use App\Resource\Repository\TrendJobLineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TrendJobLineRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"trend:get"}},
 *     itemOperations={"get"},
 *     collectionOperations={},
 * )
 */
class TrendJobLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=TrendJobTable::class, inversedBy="lines")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull()
     */
    private ?TrendJobTable $table = null;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull()
     * @Groups({"trend:get"})
     */
    private ?Job $job = null;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"trend:get"})
     */
    private ?int $position = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"trend:get"})
     */
    private ?int $evolution = null;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"trend:get"})
     */
    private ?int $count = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getEvolution(): ?int
    {
        return $this->evolution;
    }

    public function setEvolution(?int $evolution): self
    {
        $this->evolution = $evolution;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getTable(): ?TrendJobTable
    {
        return $this->table;
    }

    public function setTable(?TrendJobTable $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }
}
