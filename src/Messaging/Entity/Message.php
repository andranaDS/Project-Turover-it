<?php

namespace App\Messaging\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Annotation\ApiFileUrl;
use App\Core\Annotation\SensitiveContentEntity;
use App\Core\Annotation\SensitiveContentProperty;
use App\Core\Validator as CoreAssert;
use App\Messaging\Controller\Message\PostMessage;
use App\Messaging\Repository\MessageRepository;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 * @Vich\Uploadable()
 * @ApiResource(
 *      attributes={
 *          "order"={"createdAt"="DESC"},
 *      },
 *      normalizationContext={
 *          "groups"={"message:get"},
 *          "enable_max_depth"=true,
 *      },
 *      collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_USER')",
 *          },
 *          "post_message"={
 *              "method"="POST",
 *              "path"="/feeds/{id}/messages",
 *              "controller"=PostMessage::class,
 *              "security"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"message:get", "message:get:item"}},
 *              "denormalization_context"={"groups"={"message:post"}},
 *              "validation_groups"={"message:post"},
 *              "deserialize"=false,
 *              "openapi_context"={
 *                  "summary"="Add a Message.",
 *                  "description"="Add a Message.",
 *                  "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "documentFile"={
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
 *      itemOperations={
 *          "get"={
 *              "controller"= NotFoundAction::class,
 *              "read"= false,
 *              "output"= false,
 *          },
 *      },
 * )
 * @SensitiveContentEntity()
 */
class Message
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"message:get", "feed:get:collection", "feed:get:item"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldId;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @SensitiveContentProperty()
     * @Groups({"message:get", "feed:get:collection", "feed:get:item"})
     */
    private ?string $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"message:post"})
     * @CoreAssert\ForbiddenContent(groups={"message:post"})
     * @Groups({"message:get", "feed:get:item", "message:post"})
     */
    private ?string $contentHtml;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"message:post"})
     * @Assert\Json(message="generic.json", groups={"message:post"})
     * @Groups({"message:get", "feed:get:item", "message:post"})
     */
    private ?string $contentJson;

    /**
     * @Vich\UploadableField(mapping="message_file", fileNameProperty="document")
     * @Assert\File(
     *     maxSizeMessage="generic.file.max_size",
     *     maxSize="10M",
     *     mimeTypesMessage="message.mime_type",
     *     mimeTypes={"application/pdf", "image/jpeg","image/png", "image/jpg"},
     *     groups={"message:post"}
     * )
     * @Groups({"message:post"})
     */
    private ?File $documentFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"message:get", "feed:get:item", "message:post"})
     * @ApiFileUrl(property="documentFile")
     */
    private ?string $document = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"message:get", "feed:get:item", "message:post"})
     */
    private ?string $documentOriginalName = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     * @Groups({"message:get", "feed:get:collection", "feed:get:item"})
     */
    public ?User $author;

    /**
     * @ORM\ManyToOne(targetEntity=Feed::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull(message="generic.not_null", groups={"message:post"})
     * @Groups({"message:get", "message:post"})
     * @MaxDepth(1)
     */
    public ?Feed $feed;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"message:get", "feed:get:collection", "feed:get:item"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups({"message:get", "feed:get:collection", "feed:get:item"})
     */
    protected $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContentHtml(): ?string
    {
        return $this->contentHtml;
    }

    public function setContentHtml(?string $contentHtml): self
    {
        $this->contentHtml = $contentHtml;

        return $this;
    }

    public function getContentJson(): ?string
    {
        return $this->contentJson;
    }

    public function setContentJson(?string $contentJson): self
    {
        $this->contentJson = $contentJson;

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getFeed(): ?Feed
    {
        return $this->feed;
    }

    public function setFeed(?Feed $feed): self
    {
        $this->feed = $feed;

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

    public function getDocumentOriginalName(): ?string
    {
        return $this->documentOriginalName;
    }

    public function setDocumentOriginalName(?string $documentOriginalName): self
    {
        $this->documentOriginalName = $documentOriginalName;

        return $this;
    }

    public function getOldId(): ?int
    {
        return $this->oldId;
    }

    public function setOldId(?int $oldId): self
    {
        $this->oldId = $oldId;

        return $this;
    }
}
