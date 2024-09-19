<?php

namespace App\JobPosting\Entity;

use App\JobPosting\Repository\ApplicationDocumentRepository;
use App\User\Entity\UserDocument;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ApplicationDocumentRepository::class)
 * @Vich\Uploadable()
 */
class ApplicationDocument
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=UserDocument::class, inversedBy="applicationDocuments")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"application:get", "application:post", "application:legacy"})
     */
    public ?UserDocument $document = null;

    /**
     * @ORM\ManyToOne(targetEntity=Application::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull(message="generic.not_null", groups={"application:post"})
     */
    public ?Application $application = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getDocument(): ?UserDocument
    {
        return $this->document;
    }

    public function setDocument(?UserDocument $document): self
    {
        $this->document = $document;

        return $this;
    }

    /**
     * @Groups({"application:legacy"})
     */
    public function getCreatedAtTimestamp(): ?int
    {
        return null === $this->createdAt ? null : $this->createdAt->getTimestamp();
    }
}
