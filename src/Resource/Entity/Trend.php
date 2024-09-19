<?php

namespace App\Resource\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Resource\Controller\Trend\GetItem;
use App\Resource\Repository\TrendRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TrendRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"date"})})
 * @ApiResource(
 *     normalizationContext={
 *          "groups"={"trend:get"},
 *      },
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/trends/{date}",
 *              "controller"=GetItem::class,
 *              "read"=false,
 *              "cache_headers"={"max_age"=0, "shared_max_age"=0},
 *          },
 *     },
 *     collectionOperations={},
 * )
 */
class Trend
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=false)
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", unique=true)
     * @ApiProperty(identifier=true)
     * @Groups({"trend:get"})
     */
    private ?string $date = null;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"trend:get"})
     */
    private int $resumesCount = 0;

    /**
     * @ORM\Column(type="json")
     * @Groups({"trend:get"})
     */
    private ?array $genderDistribution = null;

    /**
     * @ORM\Column(type="json")
     * @Groups({"trend:get"})
     */
    private ?array $statusDistribution = null;

    /**
     * @ORM\Column(type="json")
     * @Groups({"trend:get"})
     */
    private ?array $remoteDistribution = null;

    /**
     * @ORM\OneToOne(targetEntity=TrendSkillTable::class, cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"trend:get"})
     */
    private ?TrendSkillTable $candidateSkillsTable = null;

    /**
     * @ORM\OneToOne(targetEntity=TrendSkillTable::class, cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"trend:get"})
     */
    private ?TrendSkillTable $recruiterSkillsTable = null;

    /**
     * @ORM\OneToOne(targetEntity=TrendJobTable::class, cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"trend:get"})
     */
    private ?TrendJobTable $candidateJobsTable = null;

    /**
     * @ORM\OneToOne(targetEntity=TrendJobTable::class, cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"trend:get"})
     */
    private ?TrendJobTable $recruiterJobsTable = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getResumesCount(): ?int
    {
        return $this->resumesCount;
    }

    public function setResumesCount(int $resumesCount): self
    {
        $this->resumesCount = $resumesCount;

        return $this;
    }

    public function getGenderDistribution(): ?array
    {
        return $this->genderDistribution;
    }

    public function setGenderDistribution(array $genderDistribution): self
    {
        $this->genderDistribution = $genderDistribution;

        return $this;
    }

    public function getStatusDistribution(): ?array
    {
        return $this->statusDistribution;
    }

    public function setStatusDistribution(array $statusDistribution): self
    {
        $this->statusDistribution = $statusDistribution;

        return $this;
    }

    public function getRemoteDistribution(): ?array
    {
        return $this->remoteDistribution;
    }

    public function setRemoteDistribution(array $remoteDistribution): self
    {
        $this->remoteDistribution = $remoteDistribution;

        return $this;
    }

    public function getCandidateSkillsTable(): ?TrendSkillTable
    {
        return $this->candidateSkillsTable;
    }

    public function setCandidateSkillsTable(TrendSkillTable $candidateSkillsTable): self
    {
        $this->candidateSkillsTable = $candidateSkillsTable;

        return $this;
    }

    public function getRecruiterSkillsTable(): ?TrendSkillTable
    {
        return $this->recruiterSkillsTable;
    }

    public function setRecruiterSkillsTable(TrendSkillTable $recruiterSkillsTable): self
    {
        $this->recruiterSkillsTable = $recruiterSkillsTable;

        return $this;
    }

    public function getCandidateJobsTable(): ?TrendJobTable
    {
        return $this->candidateJobsTable;
    }

    public function setCandidateJobsTable(TrendJobTable $candidateJobsTable): self
    {
        $this->candidateJobsTable = $candidateJobsTable;

        return $this;
    }

    public function getRecruiterJobsTable(): ?TrendJobTable
    {
        return $this->recruiterJobsTable;
    }

    public function setRecruiterJobsTable(TrendJobTable $recruiterJobsTable): self
    {
        $this->recruiterJobsTable = $recruiterJobsTable;

        return $this;
    }
}
