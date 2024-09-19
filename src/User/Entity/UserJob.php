<?php

namespace App\User\Entity;

use App\Core\Entity\Job;
use App\User\Repository\UserJobRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserJobRepository::class)
 */
class UserJob
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get", "user:get_turnover:collection"})
     */
    private ?Job $job = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="jobs")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    public ?User $user = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     */
    private bool $main = false;

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

    public function getMain(): bool
    {
        return $this->main;
    }

    public function setMain(bool $main): self
    {
        $this->main = $main;

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

    public function __toString(): string
    {
        return (string) $this->getJob()?->getName();
    }
}
