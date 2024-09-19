<?php

namespace App\User\Entity;

use App\User\Repository\HrFlowLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=HrFlowLogRepository::class)
 * @Vich\Uploadable()
 */
class HrFlowLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=UserDocument::class, cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?UserDocument $userDocument = null;

    /**
     * @Vich\UploadableField(mapping="hr_flow_file", fileNameProperty="log")
     */
    private ?File $logFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $log = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserDocument(): ?UserDocument
    {
        return $this->userDocument;
    }

    public function setUserDocument(?UserDocument $userDocument): self
    {
        $this->userDocument = $userDocument;

        return $this;
    }

    public function getLog(): ?string
    {
        return $this->log;
    }

    public function setLog(?string $log): self
    {
        $this->log = $log;

        return $this;
    }

    public function getLogFile(): ?File
    {
        return $this->logFile;
    }

    public function setLogFile(?File $logFile): self
    {
        $this->logFile = $logFile;

        return $this;
    }
}
