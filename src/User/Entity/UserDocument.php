<?php

namespace App\User\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Annotation\ApiFileUrl;
use App\JobPosting\Entity\ApplicationDocument;
use App\User\Controller\FreeWork\User\PostDocument;
use App\User\Repository\UserDocumentRepository;
use App\User\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserDocumentRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"resume"}),
 *     @ORM\Index(columns={"default_resume"}),
 * })
 * @Vich\Uploadable()
 * @ApiResource(
 *      attributes={"order"={"defaultResume"="DESC", "createdAt"="DESC"}},
 *      normalizationContext={
 *          "groups"={"user_document:get"},
 *      },
 *     itemOperations={
 *          "get"={
 *              "security"="object.user == user"
 *          },
 *          "delete"={
 *              "security"="object.user == user",
 *          },
 *          "put"={
 *              "security"="object.user == user",
 *              "denormalization_context"={"groups"={"user_document:put"}},
 *              "validation_groups"={"user_document:put"},
 *          },
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_USER')",
 *          },
 *          "post_document"={
 *              "security"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"location", "user:get", "user:get:private"}},
 *              "controller"=PostDocument::class,
 *              "deserialize"=false,
 *              "method"="POST",
 *              "path"="/user_documents",
 *              "openapi_context"={
 *                  "summary"="Add a User document.",
 *                  "description"="Add a User document.",
 *                  "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "resumeFile"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *          }
 *     },
 *     subresourceOperations={
 *         "api_users_documents_get_subresource"={
 *             "security"="is_granted('ROLE_USER')",
 *         }
 *     },
 * )
 * @AppAssert\UserDocument(groups={"user:turnover_write"})
 */
class UserDocument
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user_document:get", "user:post:document", "user:get:private"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"user_document:get", "user:get:private", "application:get", "user_document:put", "application:legacy", "user:legacy"})
     */
    private ?string $originalName = null;

    /**
     * @Vich\UploadableField(mapping="user_document_file", fileNameProperty="document")
     * @Assert\File(
     *     maxSizeMessage="generic.file.max_size",
     *     mimeTypesMessage="generic.file.mime_type",
     *     maxSize="10M",
     *     mimeTypes={"application/pdf"},
     *     groups={"user:post:document", "user_document:put", "user:turnover_write"}
     * )
     * @Groups({"user:post:document", "user:turnover_write"})
     */
    private ?File $documentFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"user_document:get", "user:post:document", "user:get:private", "application:get", "application:legacy", "user:legacy"})
     * @ApiFileUrl(property="documentFile")
     */
    private ?string $document = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user_document:get", "user:post:document", "user:get:private", "application:get", "application:legacy", "user:legacy"})
     */
    private bool $resume = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user_document:get", "user:post:document", "user:get:private", "application:get", "user_document:put", "user:legacy"})
     */
    private bool $defaultResume = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user:legacy", "user:turnover_write"})
     */
    private ?string $content = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    public ?User $user = null;

    /**
     * @ORM\OneToMany(targetEntity=ApplicationDocument::class, mappedBy="document", cascade={"persist", "remove"})
     */
    private Collection $applicationDocuments;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"user_document:get", "user:post:document", "user:get:private", "application:get"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups({"user_document:get", "user:post:document", "user:get:private", "application:get"})
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->applicationDocuments = new ArrayCollection();
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

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $resume): self
    {
        $this->document = $resume;

        return $this;
    }

    public function getDocumentFile(): ?File
    {
        return $this->documentFile;
    }

    public function setDocumentFile(?File $resumeFile): self
    {
        $this->documentFile = $resumeFile;

        if ($resumeFile) {
            $this->updatedAt = new \DateTime('now');
        }

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

    public function getDefaultResume(): ?bool
    {
        return $this->defaultResume;
    }

    public function setDefaultResume(bool $defaultResume): self
    {
        $this->defaultResume = $defaultResume;

        return $this;
    }

    public function getResume(): ?bool
    {
        return $this->resume;
    }

    public function setResume(bool $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * @return Collection|ApplicationDocument[]
     */
    public function getApplicationDocuments(): Collection
    {
        return $this->applicationDocuments;
    }

    public function addApplicationDocument(ApplicationDocument $applicationDocument): self
    {
        if (!$this->applicationDocuments->contains($applicationDocument)) {
            $this->applicationDocuments[] = $applicationDocument;
            $applicationDocument->setDocument($this);
        }

        return $this;
    }

    public function removeApplicationDocument(ApplicationDocument $applicationDocument): self
    {
        if ($this->applicationDocuments->removeElement($applicationDocument)) {
            // set the owning side to null (unless already changed)
            if ($applicationDocument->getDocument() === $this) {
                $applicationDocument->setDocument(null);
            }
        }

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function __toString(): string
    {
        return $this->originalName ?? '';
    }
}
