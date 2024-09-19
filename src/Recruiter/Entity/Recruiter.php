<?php

namespace App\Recruiter\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Company\Entity\Company;
use App\Company\Entity\Site;
use App\Core\Enum\Gender;
use App\Core\Validator as CoreAssert;
use App\Folder\Entity\Folder;
use App\JobPosting\Entity\JobPostingTemplate;
use App\Recruiter\Controller\Turnover\Recruiter\ChangePassword;
use App\Recruiter\Controller\Turnover\Recruiter\DeleteItem;
use App\Recruiter\Controller\Turnover\Recruiter\DeleteItemSecondary;
use App\Recruiter\Controller\Turnover\Recruiter\PostItem;
use App\Recruiter\Controller\Turnover\Recruiter\PostItemSecondary;
use App\Recruiter\Controller\Turnover\Recruiter\Webinar;
use App\Recruiter\Repository\RecruiterRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RecruiterRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @UniqueEntity(fields={"email"}, groups={"recruiter:post", "recruiter:write:secondary", "recruiter:change_email:request"})
 * @UniqueEntity(fields={"username"}, groups={"recruiter:post", "recruiter:write:secondary"})
 * @ApiResource(
 *     itemOperations={
 *          "turnover_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('RECRUITER_ME', object)",
 *              "normalization_context"={"groups"={"recruiter:get"}},
 *          },
 *          "turnover_patch_webinar"={
 *              "method"="PATCH",
 *              "path"="/recruiters/{id}/webinar",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "controller"=Webinar::class,
 *              "security"="is_granted('RECRUITER_ME', object)",
 *              "normalization_context"={"groups"={"recruiter:get"}},
 *              "deserialize"=false,
 *              "validate"=false,
 *              "openapi_context"={
 *                  "summary"="Update the webinar last viewed date.",
 *                  "description"="Update the webinar last viewed date.",
 *              },
 *          },
 *          "turnover_patch_change_password"={
 *              "method"="PATCH",
 *              "path"="/recruiters/{id}/change_password",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "controller"=ChangePassword::class,
 *              "security"="is_granted('RECRUITER_ME', object)",
 *              "normalization_context"={"groups"={"recruiter:get"}},
 *              "deserialize"=false,
 *              "validate"=false,
 *              "openapi_context"={
 *                  "summary"="Updates Recruiter password.",
 *                  "description"="Updates Recruiter password.",
 *              },
 *          },
 *          "turnover_patch"={
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "normalization_context"={"groups"={"recruiter:get"}},
 *              "denormalization_context"={"groups"={"recruiter:patch"}},
 *              "validation_groups"={"recruiter:patch"},
 *              "security"="is_granted('RECRUITER_ME', object)",
 *          },
 *          "turnover_patch_secondary"={
 *              "method"="PATCH",
 *              "path"="/companies/mine/recruiters/{id}",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "normalization_context"={"groups"={"recruiter:get:secondary"}},
 *              "denormalization_context"={"groups"={"recruiter:write:secondary"}},
 *              "validation_groups"={"recruiter:write:secondary"},
 *              "security"="is_granted('RECRUITER_MINE', object)",
 *              "openapi_context"={
 *                  "summary"="Updates the secondary Recruiter resource.",
 *                  "description"="Updates the secondary Recruiter resource."
 *              },
 *          },
 *          "turnover_delete"={
 *              "method"="DELETE",
 *              "path"="/recruiters/{id}",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('RECRUITER_ME', object)",
 *              "controller"=DeleteItem::class,
 *          },
 *          "turnover_delete_secondary"={
 *              "method"="DELETE",
 *              "path"="/companies/mine/recruiters/{id}",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('RECRUITER_MINE', object)",
 *              "controller"=DeleteItemSecondary::class,
 *              "openapi_context"={
 *                  "summary"="Removes the secondary Recruiter resource.",
 *                  "description"="Removes the secondary Recruiter resource."
 *              },
 *          },
 *          "turnover_patch_notifications"={
 *              "method"="PATCH",
 *              "path"="/recruiters/{id}/notifications",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('RECRUITER_ME', object)",
 *              "normalization_context"={"groups"={"recruiter:patch:notification"}},
 *              "denormalization_context"={"groups"={"recruiter:patch:notification"}},
 *              "validation_groups"={"recruiter:patch:notification"},
 *              "openapi_context"={
 *                  "summary"="Update Recruiter notifications.",
 *                  "description"="Update notifications.",
 *              },
 *          },
 *     },
 *     collectionOperations={
 *          "get"={
 *              "controller"= NotFoundAction::class,
 *          },
 *          "turnover_post"={
 *              "method"="POST",
 *              "path"="/recruiters",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "normalization_context"={"groups"={"recruiter:get"}},
 *              "denormalization_context"={"groups"={"recruiter:post"}},
 *              "validation_groups"={"recruiter:post"},
 *              "controller"=PostItem::class,
 *          },
 *          "turnover_post_secondary"={
 *              "security"="is_granted('RECRUITER_MAIN')",
 *              "method"="POST",
 *              "path"="/companies/mine/recruiters",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "normalization_context"={"groups"={"recruiter:get:secondary"}},
 *              "denormalization_context"={"groups"={"recruiter:write:secondary"}},
 *              "validation_groups"={"recruiter:write:secondary"},
 *              "controller"=PostItemSecondary::class,
 *              "openapi_context"={
 *                  "summary"="Creates a secondary Recruiter resource.",
 *                  "description"="Creates a secondary Recruiter resource."
 *              },
 *          },
 *     },
 *     subresourceOperations={
 *          "api_companies_recruiters_get_subresource"={
 *              "security"="is_granted('COMPANY_MINE')",
 *              "normalization_context"={"groups"={"recruiter:get"}},
 *          },
 *     }
 * )
 * @ApiFilter(BooleanFilter::class, properties={"main"})
 */
class Recruiter implements UserInterface, PasswordAuthenticatedUserInterface
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"recruiter:get", "recruiter:get:secondary", "job_posting_template:get", "folder:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"recruiter:get", "recruiter:get:secondary", "recruiter:post", "recruiter:write:secondary", "job_posting_template:get", "folder:get"})
     * @Assert\Email(groups={"recruiter:post", "recruiter:write:secondary", "recruiter:change_email:request", "recruiter:forgotten_password:reset"})
     * @Assert\NotBlank(groups={"recruiter:post", "recruiter:write:secondary", "recruiter:change_email:request", "recruiter:forgotten_password:reset"})
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"recruiter:get", "folder:get"})
     * @Assert\NotBlank(groups={"recruiter:post", "recruiter:write:secondary"})
     */
    private ?string $username = null;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"recruiter:get", "recruiter:patch"})
     * @EnumAssert(message="generic.enum.message", class=Gender::class, groups={"recruiter:patch"})
     */
    private ?string $gender = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"recruiter:get", "recruiter:get:secondary", "recruiter:post", "recruiter:patch", "recruiter:write:secondary", "job_posting_template:get", "user:get_turnover:collection"})
     * @Assert\NotBlank(groups={"recruiter:post", "recruiter:patch", "recruiter:write:secondary"})
     */
    private ?string $firstName = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"recruiter:get", "recruiter:get:secondary", "recruiter:post", "recruiter:patch", "recruiter:write:secondary", "job_posting_template:get", "user:get_turnover:collection"})
     * @Assert\NotBlank(groups={"recruiter:post", "recruiter:patch", "recruiter:write:secondary"})
     */
    private ?string $lastName = null;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     * @Groups({"recruiter:get", "recruiter:post", "recruiter:patch"})
     * @AssertPhoneNumber(message="generic.phone.valid", groups={"recruiter:post", "recruiter:patch"})
     * @Assert\NotBlank(groups={"recruiter:post", "recruiter:patch"})
     */
    private ?PhoneNumber $phoneNumber = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get"})
     */
    private bool $enabled = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $confirmationToken = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:get:private"})
     */
    private ?\DateTime $emailRequestedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $passwordRequestedAt;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @Groups({"recruiter:post"})
     * @CoreAssert\PasswordComplexity(minScore=2, groups={"recruiter:post", "recruiter:forgotten_password:reset"})
     * @Assert\NotBlank()
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get"})
     */
    private bool $passwordUpdateRequired = false;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, cascade={"persist"}, inversedBy="recruiters")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"recruiter:get", "recruiter:get:secondary", "recruiter:post", "job_posting_template:get"})
     * @Assert\NotNull(groups={"recruiter:post"})
     * @Assert\Valid(groups={"recruiter:post"})
     */
    public ?Company $company;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class)
     * @Groups({"recruiter:get", "recruiter:get:secondary", "recruiter:write:secondary", "recruiter:write:secondary"})
     * @ORM\JoinColumn(nullable=true)
     */
    public ?Site $site;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:get:secondary"})
     */
    public bool $main = false;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     * @Gedmo\Blameable(on="create")
     */
    private ?Recruiter $createdBy = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"recruiter:get", "recruiter:post"})
     */
    private ?string $job = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:post"})
     * @Assert\IsTrue(message="user.terms_of_service.is_true", groups={"recruiter:post"})
     */
    public bool $termsOfService = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTime $termsOfServiceAcceptedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTime $webinarViewedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTime $loggedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTime $passwordUpdatedAt = null;

    /**
     * @ORM\OneToMany(targetEntity=JobPostingTemplate::class, mappedBy="createdBy")
     */
    private Collection $jobPostingTemplates;

    /**
     * @ORM\OneToOne(targetEntity=RecruiterNotification::class, cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @Assert\Valid(groups={"recruiter:patch:notification"})
     * @Assert\NotNull(message="generic.not_null", groups={"recruiter:patch:notification"})
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private ?RecruiterNotification $notification;

    /**
     * @ORM\OneToMany(targetEntity=Folder::class, mappedBy="recruiter")
     */
    private Collection $folders;

    public function __construct()
    {
        $this->jobPostingTemplates = new ArrayCollection();
        $this->notification = new RecruiterNotification();
        $this->folders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_RECRUITER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function isMain(): ?bool
    {
        return $this->main;
    }

    public function isSecondary(): ?bool
    {
        return !$this->isMain();
    }

    public function setMain(bool $main): self
    {
        $this->main = $main;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getName(): string
    {
        return implode(' ', array_filter([$this->firstName, $this->lastName]));
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumber $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getCreatedBy(): ?self
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?self $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getTermsOfService(): ?bool
    {
        return $this->termsOfService;
    }

    public function setTermsOfService(bool $termsOfService): self
    {
        $this->termsOfService = $termsOfService;
        $this->termsOfServiceAcceptedAt = true === $termsOfService ? Carbon::now() : null;

        return $this;
    }

    public function getTermsOfServiceAcceptedAt(): ?\DateTime
    {
        return $this->termsOfServiceAcceptedAt;
    }

    public function setTermsOfServiceAcceptedAt(?\DateTime $termsOfServiceAcceptedAt): self
    {
        $this->termsOfServiceAcceptedAt = $termsOfServiceAcceptedAt;

        return $this;
    }

    public function isEmailConfirmActive(int $ttl): bool
    {
        return $this->getCreatedAt() instanceof \DateTime &&
            $this->getCreatedAt()->getTimestamp() + $ttl > time();
    }

    public function isPasswordRequestActive(int $ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function isTermsOfService(): ?bool
    {
        return $this->termsOfService;
    }

    public function getWebinarViewedAt(): ?\DateTime
    {
        return $this->webinarViewedAt;
    }

    public function setWebinarViewedAt(?\DateTime $webinarViewedAt): self
    {
        $this->webinarViewedAt = $webinarViewedAt;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getEmailRequestedAt(): ?\DateTime
    {
        return $this->emailRequestedAt;
    }

    public function setEmailRequestedAt(?\DateTime $emailRequestedAt): self
    {
        $this->emailRequestedAt = $emailRequestedAt;

        return $this;
    }

    public function isEmailRequestActive(int $ttl): bool
    {
        return $this->getEmailRequestedAt() instanceof \DateTime &&
            $this->getEmailRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    public function isPasswordUpdateRequired(): bool
    {
        return $this->passwordUpdateRequired;
    }

    public function setPasswordUpdateRequired(bool $passwordUpdateRequired): self
    {
        $this->passwordUpdateRequired = $passwordUpdateRequired;

        return $this;
    }

    public function getLoggedAt(): ?\DateTime
    {
        return $this->loggedAt;
    }

    public function setLoggedAt(?\DateTime $loggedAt): self
    {
        $this->loggedAt = $loggedAt;

        return $this;
    }

    public function getPasswordUpdatedAt(): ?\DateTime
    {
        return $this->passwordUpdatedAt;
    }

    public function setPasswordUpdatedAt(?\DateTime $passwordUpdatedAt): self
    {
        $this->passwordUpdatedAt = $passwordUpdatedAt;

        return $this;
    }

    public function getJobPostingTemplates(): ?Collection
    {
        return $this->jobPostingTemplates;
    }

    public function addJobPostingTemplate(JobPostingTemplate $jobPostingTemplate): self
    {
        if (!$this->jobPostingTemplates->contains($jobPostingTemplate)) {
            $this->jobPostingTemplates[] = $jobPostingTemplate;
            $jobPostingTemplate->setCreatedBy($this);
        }

        return $this;
    }

    public function removeJobPostingTemplate(JobPostingTemplate $jobPostingTemplate): self
    {
        // set the owning side to null (unless already changed)
        if ($this->jobPostingTemplates->removeElement($jobPostingTemplate) && $jobPostingTemplate->getCreatedBy() === $this) {
            $jobPostingTemplate->setCreatedBy(null);
        }

        return $this;
    }

    public function getNotification(): ?RecruiterNotification
    {
        return $this->notification;
    }

    public function setNotification(?RecruiterNotification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    public function getFolders(): Collection
    {
        return $this->folders;
    }

    public function addFolder(Folder $folder): self
    {
        if (!$this->folders->contains($folder)) {
            $this->folders->add($folder);
            $folder->setRecruiter($this);
        }

        return $this;
    }

    public function removeFolder(Folder $folder): self
    {
        if ($this->folders->removeElement($folder) && $folder->getRecruiter() === $this) {
            $folder->setRecruiter(null);
        }

        return $this;
    }
}
