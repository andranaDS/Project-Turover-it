<?php

namespace App\Recruiter\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Recruiter\Repository\RecruiterJobRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RecruiterJobRepository::class)
 * @ApiResource(
 *     order={"name"="ASC"},
 *     normalizationContext={"recruiter_job:get"},
 *     itemOperations={
 *          "get"={
 *              "cache_headers"={"max_age"=300, "shared_max_age"=2592000},
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "cache_headers"={"max_age"=300, "shared_max_age"=2592000},
 *          }
 *     },
 * )
 * @ApiFilter(SearchFilter::class, properties={"name"="partial"})
 */
class RecruiterJob
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"recruiter_job:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"recruiter_job:get"})
     */
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
