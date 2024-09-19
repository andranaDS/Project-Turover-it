<?php

namespace App\Core\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Enum\ContactService;
use App\Core\Repository\ContactRepository;
use App\Core\Validator as CoreAssert;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 * @ApiResource(
 *      normalizationContext={
 *          "groups"={"contact:get"},
 *      },
 *     itemOperations={
 *          "freework_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "controller"= NotFoundAction::class,
 *              "read"= false,
 *              "output"= false,
 *          }
 *     },
 *      collectionOperations={
 *          "freework_post"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"contact:get"}},
 *              "denormalization_context"={"groups"={"contact:post"}},
 *              "validation_groups"={"contact:post"},
 *          }
 *      },
 * )
 */
class Contact
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"contact:get"})
     */
    private int $id = 0;

    /**
     * @ORM\Column(type="string", length=55)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"contact:post"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"contact:post"})
     * @Groups({"contact:get", "contact:post"})
     */
    private string $fullname;

    /**
     * @ORM\Column(type="string", length=55)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"contact:post"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"contact:post"})
     * @Assert\Email(message="generic.email", groups={"contact:post"})
     * @Groups({"contact:get", "contact:post"})
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=12)
     * @Assert\NotBlank(message="generic.not_blank", groups={"contact:post"})
     * @EnumAssert(message="generic.enum.message", class=ContactService::class, groups={"contact:post"})
     * @Groups({"contact:get", "contact:post"})
     */
    private string $service;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"contact:post"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"contact:post"})
     * @CoreAssert\ForbiddenContent(groups={"contact:post"})
     * @Groups({"contact:get", "contact:post"})
     */
    private string $subject;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"contact:post"})
     * @CoreAssert\ForbiddenContent(groups={"contact:post"})
     * @Groups({"contact:get", "contact:post"})
     */
    private ?string $message;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Blameable(on="create")
     */
    public ?User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
