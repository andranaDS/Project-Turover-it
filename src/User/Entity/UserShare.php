<?php

namespace App\User\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Recruiter\Entity\Recruiter;
use App\User\Repository\UserShareRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserShareRepository::class)
 * @ApiResource(
 *    itemOperations={
 *          "turnover_get_user_share"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getSharedBy() == user"
 *          }
 *     },
 *     collectionOperations={
 *          "turnover_post_user_share"={
 *              "method"="POST",
 *              "path"="/user_share",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "denormalization_context"={"groups"={"user_share:post"}},
 *              "normalization_context"={"groups"={"user_share:get"}}
 *          },
 *     }
 * )
 */
class UserShare
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Assert\Email(message="generic.email")
     * @Assert\NotBlank(message="generic.not_blank")
     * @Groups({"user_share:get", "user_share:post"})
     */
    private ?string $email = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"user_share:get", "user_share:post"})
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     * @Gedmo\Blameable(on="create")
     * @Groups({"user_share:get"})
     */
    private ?Recruiter $sharedBy;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"user_share:get"})
     */
    protected \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSharedBy(): ?Recruiter
    {
        return $this->sharedBy;
    }

    public function setSharedBy(?Recruiter $sharedBy): self
    {
        $this->sharedBy = $sharedBy;

        return $this;
    }
}
