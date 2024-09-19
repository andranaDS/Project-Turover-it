<?php

namespace App\Folder\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Folder\Enum\FolderType;
use App\Folder\Repository\FolderRepository;
use App\Recruiter\Entity\Recruiter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FolderRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ApiResource(
 *     normalizationContext={"groups"={"folder:get"}},
 *     itemOperations={
 *          "turnover_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getRecruiter() == user",
 *              "normalization_context"={"groups"={"folder:get", "folder:get:item"}},
 *          },
 *          "turnover_put"={
 *              "method"="PUT",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getRecruiter() == user",
 *              "denormalization_context"={"groups"={"folder:write"}},
 *              "validation_groups"={"folder:write"},
 *          },
 *          "turnover_delete"={
 *              "method"="DELETE",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getRecruiter() == user",
 *          },
 *     },
 *     collectionOperations={
 *          "turnover_post"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "denormalization_context"={"groups"={"folder:write"}},
 *              "validation_groups"={"folder:write"},
 *          },
 *          "turnover_get"={
 *              "method"="GET",
 *              "condition"="request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *          }
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"name"="partial", "type"="exact"})
 */
class Folder
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"folder:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class, inversedBy="folders")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @Gedmo\Blameable(on="create")
     */
    private ?Recruiter $recruiter = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"folder:get", "folder:write"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"folder:write"})
     * @Assert\Length(max=255, maxMessage="generic.length.max", groups={"folder:write"})
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"folder:get"})
     * @Assert\EqualTo(value=FolderType::PERSONAL, groups={"folder:write"})
     */
    private ?string $type = FolderType::PERSONAL;

    /**
     * @ORM\Column(type="integer", options={"default"= 0})
     * @Groups({"folder:get"})
     */
    private int $usersCount = 0;

    /**
     * @ORM\OneToMany(targetEntity=FolderUser::class, mappedBy="folder", cascade={"persist", "remove"})
     * @Groups({"folder:get"})
     */
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecruiter(): ?Recruiter
    {
        return $this->recruiter;
    }

    public function setRecruiter(?Recruiter $recruiter): self
    {
        $this->recruiter = $recruiter;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUsersCount(): int
    {
        return $this->usersCount;
    }

    public function setUsersCount(int $usersCount): self
    {
        $this->usersCount = $usersCount;

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(FolderUser $folderUser): self
    {
        if (!$this->users->contains($folderUser)) {
            $this->users[] = $folderUser;
            $folderUser->setFolder($this);
        }

        return $this;
    }

    public function removeUser(FolderUser $folderUser): self
    {
        if ($this->users->contains($folderUser)) {
            $this->users->removeElement($folderUser);
            $folderUser->setFolder(null);
        }

        return $this;
    }

    public function isMine(Recruiter $recruiter): bool
    {
        return $this->recruiter === $recruiter;
    }
}
