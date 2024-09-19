<?php

namespace App\Core\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Repository\LocationKeyLabelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LocationKeyLabelRepository::class)
 * @ApiResource(
 *     itemOperations={"get"},
 *     collectionOperations={"get"},
 *     normalizationContext={"groups"={"location_key_label:get"}}
 * )
 * @ApiFilter(SearchFilter::class, properties={"key"="exact"})
 */
class LocationKeyLabel
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="`key`", type="string", length=255)
     * @Groups({"location_key_label:get"})
     */
    private string $key;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"location_key_label:get"})
     */
    private string $label;

    /**
     * @var ?array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $data = null;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
