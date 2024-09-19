<?php

namespace App\Core\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Repository\JobCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobCategoryRepository::class)
 * @Gedmo\Loggable()
 * @ApiResource(
 *      normalizationContext={
 *          "groups"="job_category:get",
 *      },
 *     itemOperations={
 *          "get"
 *      },
 *     collectionOperations={
 *          "get"
 *     },
 * )
 */
class JobCategory
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"job_category:get", "job:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"job_category:get", "job:get"})
     * @Gedmo\Versioned()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Gedmo\Slug(fields={"name"})
     * @Groups({"job_category:get", "job:get"})
     * @Gedmo\Versioned()
     */
    private string $slug;

    /**
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="category", cascade={"persist", "remove"})
     * @Groups({"job_category:get"})
     */
    private Collection $jobs;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setCategory($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getCategory() === $this) {
                $job->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
