<?php

namespace App\Company\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Company\Controller\FreeWork\Company\Blacklist;
use App\Company\Controller\FreeWork\Company\Favorite as FreeWorkFavorite;
use App\Company\Controller\FreeWork\Company\GetHomepage;
use App\Company\Controller\Turnover\Company\Favorite as TurnoverFavorite;
use App\Company\Controller\Turnover\Company\PostDirectoryMedia;
use App\Company\Enum\CompanySize;
use App\Company\Repository\CompanyRepository;
use App\Company\Validator\CompanyAccountValidationGroups;
use App\Company\Validator\CompanyIntracommunityVat;
use App\Core\Annotation\ApiEnum;
use App\Core\Annotation\ApiFileUrl;
use App\Core\Annotation\ApiThumbnailUrls;
use App\Core\Doctrine\Filter\LocationFilter;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Entity\SoftSkill;
use App\Core\Validator\CompanyRegistrationNumber;
use App\JobPosting\Entity\JobPosting;
use App\Recruiter\Entity\Recruiter;
use App\Sync\Synchronizer\SynchronizableInterface;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"location_value"}),
 *     @ORM\Index(columns={"old_id"})
 * })
 * @Vich\Uploadable()
 * @ApiResource(
 *     attributes={"order"={"createdAt"="DESC"}},
 *     itemOperations={
 *         "get"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"company:get", "company:get:item", "location"}},
 *          },
 *          "freework_get"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"company:get", "company:get:item", "location"}},
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'"
 *          },
 *          "freework_patch_favorite"={
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_USER')",
 *              "path"="/companies/{slug}/favorite",
 *              "controller"=FreeWorkFavorite::class,
 *              "deserialize"=false,
 *          },
 *          "freework_patch_blacklist"={
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_USER')",
 *              "path"="/companies/{slug}/blacklist",
 *              "controller"=Blacklist::class,
 *              "deserialize"=false,
 *          },
 *          "turnover_patch_favorite"={
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "path"="/companies/{slug}/favorite",
 *              "controller"=TurnoverFavorite::class,
 *              "deserialize"=false,
 *          },
 *          "turnover_patch_account"={
 *              "method"="PATCH",
 *              "path"="/companies/{slug}/account",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "normalization_context"={"groups"={"company:patch:account"}},
 *              "denormalization_context"={"groups"={"company:patch:account"}},
 *              "validation_groups"={CompanyAccountValidationGroups::class, "validationGroups"},
 *              "security"="is_granted('COMPANY_MINE')",
 *          },
 *          "turnover_patch_directory"={
 *              "method"="PATCH",
 *              "path"="/companies/{slug}/directory",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "normalization_context"={"groups"={"company:patch:directory"}},
 *              "denormalization_context"={"groups"={"company:patch:directory"}},
 *              "validation_groups"={"company:patch:directory"},
 *              "security"="is_granted('COMPANY_ME')",
 *          },
 *          "turnover_post_directory_media"={
 *              "method"="POST",
 *              "path"="/companies/{slug}/directory_media",
 *              "controller"=PostDirectoryMedia::class,
 *              "deserialize"=false,
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "normalization_context"={"groups"={"company:post:directory_media"}},
 *              "denormalization_context"={"groups"={"company:post:directory_media"}},
 *              "validation_groups"={"company:post:directory_media"},
 *              "security"="is_granted('COMPANY_ME')",
 *          },
 *     },
 *     collectionOperations={
 *         "get"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"company:get", "location"}},
 *          },
 *          "freework_get_favorites"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_USER')",
 *              "path"="/companies/favorites",
 *              "normalization_context"={"groups"={"company:get", "location"}},
 *              "pagination_enabled"=false,
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of favorite Company resources.",
 *                  "description"="Retrieves the collection of favorite Company resources.",
 *              },
 *          },
 *          "freework_get_blacklists"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_USER')",
 *              "path"="/companies/blacklists",
 *              "normalization_context"={"groups"={"company:get", "location"}},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of blacklisted Company resources.",
 *                  "description"="Retrieves the collection of blacklisted Company resources.",
 *              },
 *          },
 *         "freework_get_homepage"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "path"="/companies/homepage",
 *              "normalization_context"={"groups"={"company:get:homepage"}},
 *              "controller"=GetHomepage::class,
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection companies order by rand.",
 *                  "description"="Retrieves the collection companies order by rand.",
 *              },
 *          },
 *          "turnover_get_recruiter_favorites"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "path"="/companies/favorites",
 *              "normalization_context"={"groups"={"company:get", "location"}},
 *              "pagination_enabled"=false,
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of favorite Company resources.",
 *                  "description"="Retrieves the collection of favorite Company resources.",
 *              },
 *          },
 *     },
 *     subresourceOperations={
 *          "sites_get_subresource"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/companies/{slug}/sites",
 *          },
 *          "recruiters_get_subresource"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/companies/{slug}/recruiters",
 *          }
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"size"="exact", "businessActivity"="exact", "name"="partial"})
 * @ApiFilter(LocationFilter::class, properties={"locationKeys"})
 * @ApiFilter(OrderFilter::class, properties={"name", "createdAt", "data.jobPostingsPublishedCount", "data.jobPostingsIntercontractPublishedCount" , "data.usersCount"})
 * @ApiFilter(BooleanFilter::class, properties={"directoryFreeWork"})
 * @ApiFilter(PropertyFilter::class)
 * @CompanyRegistrationNumber(countryCodeProperty="billingAddress.countryCode", registrationNumberProperty="registrationNumber", parameters={"FR"={"type"="siret"}},groups={"recruiter:post", "company:patch:account"})
 * @CompanyIntracommunityVat(groups={"company:patch:account"})
 */
class Company implements SynchronizableInterface
{
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"company:get", "job_posting:get", "application:get", "job_posting:legacy", "user:legacy", "company:get:homepage", "recruiter:get", "recruiter:get:secondary"})
     * @ApiProperty(identifier=false)
     */
    public ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:get", "job_posting:get", "application:get", "application:legacy", "user:legacy", "job_posting:legacy", "company:get:homepage", "user:patch:personal_info", "user:get", "recruiter:post", "recruiter:get", "recruiter:get:secondary", "company:patch:directory"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"recruiter:post", "company:patch:directory"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"company:patch:directory"})
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     * @Assert\Length(maxMessage="generic.length.max", max="255")
     * @Groups({"company:get", "job_posting:get", "application:get", "job_posting:legacy", "company:get:homepage", "user:patch:personal_info", "user:get", "recruiter:get", "recruiter:get:secondary"})
     * @ApiProperty(identifier=true)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"company:get"})
     */
    private ?string $excerpt;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"company:get",  "job_posting:get", "company:patch:directory"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"company:patch:directory"})
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"company:get:item", "company:get", "company:patch:directory"})
     */
    private ?string $annualRevenue = null;

    /**
     * @ORM\ManyToOne(targetEntity=CompanyBusinessActivity::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"company:get", "job_posting:get:item", "company:get:homepage", "recruiter:post", "recruiter:get", "recruiter:get:secondary", "company:patch:account", "company:patch:directory"})
     * @Assert\NotNull(message="generic.not_null", groups={"company:patch:account"})
     */
    private ?CompanyBusinessActivity $businessActivity = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @EnumAssert(message="generic.enum.message", CompanySize::class, groups={"company:patch:directory"})
     * @Groups({"company:get", "job_posting:get:item", "company:get:homepage", "company:patch:directory"})
     * @ApiEnum(class=CompanySize::class, translationDomain="enums")
     */
    private ?string $size = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:get"})
     * @Assert\Url(message="generic.url", groups={"company:patch:directory"})
     */
    private ?string $websiteUrl = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:get", "company:patch:directory"})
     * @Assert\Url(message="generic.url", groups={"company:patch:directory"})
     */
    private ?string $linkedInUrl = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:get", "company:patch:directory"})
     * @Assert\Url(message="generic.url", groups={"company:patch:directory"})
     */
    private ?string $facebookUrl = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:get", "company:patch:directory"})
     * @Assert\Url(message="generic.url", groups={"company:patch:directory"})
     */
    private ?string $twitterUrl = null;

    /**
     * @ORM\Embedded(class="App\Core\Entity\Location")
     * @Groups({"company:get", "job_posting:get:item", "company:get:homepage", "company:patch:directory"})
     */
    private ?Location $location;

    /**
     * @Vich\UploadableField(mapping="company_logo", fileNameProperty="logo")
     * @Assert\Image(
     *     maxSizeMessage="generic.file.max_size",
     *     minWidthMessage="generic.file.image.min_width",
     *     minHeightMessage="generic.file.image.min_height",
     *     maxWidth="generic.file.image.max_width",
     *     maxHeight="generic.file.image.max_height",
     *     mimeTypesMessage="generic.file.mime_type",
     *     maxSize="2M",
     *     minWidth=192,
     *     minHeight=192,
     *     maxWidth=2048,
     *     maxHeight=2048,
     *     mimeTypes={"image/jpeg","image/png","image/gif", "image/jpg"},
     *     groups={"company:post:directory_media"}
     * )
     */
    private ?File $logoFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"company:get", "job_posting:get", "application:get", "company:get:homepage", "company:post:directory_media"})
     * @ApiThumbnailUrls({
     *     { "name"="small", "filter"="company_logo_small" },
     *     { "name"="medium", "filter"="company_logo_medium" },
     * })
     */
    private ?string $logo = null;

    /**
     * @ORM\ManyToMany(targetEntity=Skill::class)
     * @Groups({"company:get", "company:patch:directory"})
     */
    private Collection $skills;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="4", groups={"company:patch:directory"})
     * @Groups({"company:get", "company:patch:directory"})
     */
    private ?int $creationYear = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"company:get", "job_posting:get", "company:patch:directory"})
     */
    private ?bool $directoryFreeWork = false;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=JobPosting::class, mappedBy="company", cascade={"persist"})
     */
    private Collection $jobPostings;

    /**
     * @ORM\OneToMany(targetEntity=CompanyPicture::class, mappedBy="company", orphanRemoval=true, cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"company:get", "company:post:directory_media"})
     * @ORM\OrderBy({"position"="ASC"})
     * @Assert\Valid(groups={"company:post:directory_media"})
     */
    private Collection $pictures;

    /**
     * @Vich\UploadableField(mapping="company_picture_image", fileNameProperty="coverPicture")
     * @Assert\Image(
     *     maxSizeMessage="generic.file.max_size",
     *     minWidthMessage="generic.file.image.min_width",
     *     minHeightMessage="generic.file.image.min_height",
     *     maxWidth="generic.file.image.max_width",
     *     maxHeight="generic.file.image.max_height",
     *     mimeTypesMessage="generic.file.mime_type",
     *     maxSize="30M",
     *     minWidth=500,
     *     minHeight=500,
     *     maxWidth=4096,
     *     maxHeight=4096,
     *     mimeTypes={"image/jpeg","image/png","image/gif", "image/jpg"},
     *     groups={"company:post:directory_media"}
     * )
     */
    private ?File $coverPictureFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"company:get", "job_posting:get", "company:get:homepage", "company:post:directory_media"})
     * @ApiThumbnailUrls({
     *     { "name"="medium", "filter"="company_picture_image_medium" },
     *     { "name"="large", "filter"="company_picture_image_large" },
     * })
     */
    private ?string $coverPicture = null;

    /**
     * @ORM\OneToMany(targetEntity=CompanyUserFavorite::class, mappedBy="company", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $userFavorites;

    /**
     * @ORM\OneToMany(targetEntity=CompanyBlacklist::class, mappedBy="company", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $blacklists;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"application:legacy", "user:legacy", "job_posting:legacy"})
     */
    private ?int $oldId = null;

    /**
     * @ORM\OneToOne(targetEntity=CompanyData::class, cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER", inversedBy="company")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @Groups({"company:get:item", "job_posting:get:item", "company:get"})
     */
    private ?CompanyData $data;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"recruiter:post", "recruiter:get", "recruiter:get:secondary", "company:patch:account"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"company:patch:account"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:account:registration_number"})
     */
    private ?string $registrationNumber;

    /**
     * @ORM\OneToMany(targetEntity=Site::class, mappedBy="company", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $sites;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"company:get", "company:patch:directory"})
     */
    private ?bool $directoryTurnover = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:patch:account", "recruiter:get"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"company:patch:account"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"company:patch:account"})
     */
    private ?string $legalName = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"company:get", "company:patch:directory"})
     * @Assert\Length(maxMessage="generic.length.max", max="100", groups={"company:patch:directory"})
     */
    private ?string $baseline = null;

    /**
     * @ORM\ManyToMany(targetEntity=SoftSkill::class)
     * @Groups({"company:get", "company:patch:directory"})
     */
    private Collection $softSkills;

    /**
     * @Vich\UploadableField(mapping="company_video_file", fileNameProperty="video")
     * @Assert\File(
     *     maxSizeMessage="generic.file.max_size",
     *     mimeTypesMessage="generic.file.mime_type",
     *     maxSize="10M",
     *     mimeTypes={"video/mp4"},
     *     groups={"company:post:directory_media"}
     * )
     */
    private ?File $videoFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"company:post:directory_media"})
     * @ApiFileUrl(property="videoFile")
     */
    private ?string $video = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"company:patch:account", "recruiter:get"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"company:patch:account"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:account:intracommunity_vat"})
     */
    private ?string $intracommunityVat = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Assert\Email()
     */
    private ?string $billingEmail = null;

    /**
     * @ORM\OneToMany(targetEntity=Recruiter::class, mappedBy="company", cascade={"persist", "remove"})
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $recruiters;

    /**
     * @ORM\OneToMany(targetEntity=CompanyRecruiterFavorite::class, mappedBy="company", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $recruiterFavorites;

    /**
     * @ORM\Embedded(class="App\Core\Entity\Location")
     * @Groups({"recruiter:post", "recruiter:get", "company:patch:account"})
     * @Assert\Valid(groups={"company:patch:account", "recruiter:post"})
     */
    private ?Location $billingAddress;

    /**
     * @ORM\OneToOne(targetEntity=CompanyFeaturesUsage::class, cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER", inversedBy="company")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private ?CompanyFeaturesUsage $featuresUsage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $quality;

    public function __construct()
    {
        $this->jobPostings = new ArrayCollection();
        $this->location = new Location();
        $this->skills = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->blacklists = new ArrayCollection();
        $this->data = new CompanyData();
        $this->sites = new ArrayCollection();
        $this->softSkills = new ArrayCollection();
        $this->recruiters = new ArrayCollection();
        $this->userFavorites = new ArrayCollection();
        $this->recruiterFavorites = new ArrayCollection();
        $this->billingAddress = new Location();
        $this->featuresUsage = new CompanyFeaturesUsage();
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAnnualRevenue(): ?string
    {
        return $this->annualRevenue;
    }

    public function setAnnualRevenue(?string $annualRevenue): self
    {
        $this->annualRevenue = $annualRevenue;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(?string $websiteUrl): self
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getCreationYear(): ?int
    {
        return $this->creationYear;
    }

    public function setCreationYear(?int $creationYear): self
    {
        $this->creationYear = $creationYear;

        return $this;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Skill[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        $this->skills->removeElement($skill);

        return $this;
    }

    /**
     * @return Collection|JobPosting[]
     */
    public function getJobPostings(): Collection
    {
        return $this->jobPostings;
    }

    public function addJobPosting(JobPosting $jobPosting): self
    {
        if (!$this->jobPostings->contains($jobPosting)) {
            $this->jobPostings[] = $jobPosting;
            $jobPosting->setCompany($this);
        }

        return $this;
    }

    public function removeJobPosting(JobPosting $jobPosting): self
    {
        // set the owning side to null (unless already changed)
        if ($this->jobPostings->removeElement($jobPosting) && $jobPosting->getCompany() === $this) {
            $jobPosting->setCompany(null);
        }

        return $this;
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function setLogoFile(?File $logoFile): self
    {
        $this->logoFile = $logoFile;

        if ($logoFile) {
            $this->updatedAt = new \DateTime('now');
        }

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
     * @return Collection|CompanyPicture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(CompanyPicture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setCompany($this);
        }

        return $this;
    }

    public function removePicture(CompanyPicture $picture): self
    {
        // set the owning side to null (unless already changed)
        if ($this->pictures->removeElement($picture) && $picture->getCompany() === $this) {
            $picture->setCompany(null);
        }

        return $this;
    }

    /**
     * @return Collection|CompanyUserFavorite[]
     */
    public function getUserFavorites(): Collection
    {
        return $this->userFavorites;
    }

    public function addUserFavorite(CompanyUserFavorite $favorite): self
    {
        if (!$this->userFavorites->contains($favorite)) {
            $this->userFavorites[] = $favorite;
            $favorite->setCompany($this);
        }

        return $this;
    }

    public function removeUserFavorite(CompanyUserFavorite $favorite): self
    {
        // set the owning side to null (unless already changed)
        if ($this->userFavorites->removeElement($favorite) && $favorite->getCompany() === $this) {
            $favorite->setCompany(null);
        }

        return $this;
    }

    /**
     * @return Collection|CompanyBlacklist[]
     */
    public function getBlacklists(): Collection
    {
        return $this->blacklists;
    }

    public function addBlacklist(CompanyBlacklist $blacklist): self
    {
        if (!$this->blacklists->contains($blacklist)) {
            $this->blacklists[] = $blacklist;
            $blacklist->setCompany($this);
        }

        return $this;
    }

    public function removeBlacklist(CompanyBlacklist $blacklist): self
    {
        // set the owning side to null (unless already changed)
        if ($this->blacklists->removeElement($blacklist) && $blacklist->getCompany() === $this) {
            $blacklist->setCompany(null);
        }

        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getBusinessActivity(): ?CompanyBusinessActivity
    {
        return $this->businessActivity;
    }

    public function setBusinessActivity(?CompanyBusinessActivity $businessActivity): self
    {
        $this->businessActivity = $businessActivity;

        return $this;
    }

    public function getLinkedInUrl(): ?string
    {
        return $this->linkedInUrl;
    }

    public function setLinkedInUrl(?string $linkedInUrl): self
    {
        $this->linkedInUrl = $linkedInUrl;

        return $this;
    }

    public function getFacebookUrl(): ?string
    {
        return $this->facebookUrl;
    }

    public function setFacebookUrl(?string $facebookUrl): self
    {
        $this->facebookUrl = $facebookUrl;

        return $this;
    }

    public function getTwitterUrl(): ?string
    {
        return $this->twitterUrl;
    }

    public function setTwitterUrl(?string $twitterUrl): self
    {
        $this->twitterUrl = $twitterUrl;

        return $this;
    }

    public function getDirectoryFreeWork(): ?bool
    {
        return $this->directoryFreeWork;
    }

    public function setDirectoryFreeWork(bool $directoryFreeWork): self
    {
        $this->directoryFreeWork = $directoryFreeWork;

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

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getData(): ?CompanyData
    {
        return $this->data;
    }

    public function setData(?CompanyData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function isDirectory(): ?bool
    {
        return $this->directoryFreeWork;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(?string $registrationNumber): self
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(Site $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->setCompany($this);
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        // set the owning side to null (unless already changed)
        if ($this->sites->removeElement($site) && $site->getCompany() === $this) {
            $site->setCompany(null);
        }

        return $this;
    }

    public function isDirectoryTurnover(): ?bool
    {
        return $this->directoryTurnover;
    }

    public function setDirectoryTurnover(bool $directoryTurnover): self
    {
        $this->directoryTurnover = $directoryTurnover;

        return $this;
    }

    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    public function setLegalName(?string $legalName): self
    {
        $this->legalName = $legalName;

        return $this;
    }

    public function getBaseline(): ?string
    {
        return $this->baseline;
    }

    public function setBaseline(?string $baseline): self
    {
        $this->baseline = $baseline;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getVideoFile(): ?File
    {
        return $this->videoFile;
    }

    public function setVideoFile(?File $videoFile): self
    {
        $this->videoFile = $videoFile;

        if ($videoFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getIntracommunityVat(): ?string
    {
        return $this->intracommunityVat;
    }

    public function setIntracommunityVat(?string $intracommunityVat): self
    {
        $this->intracommunityVat = $intracommunityVat;

        return $this;
    }

    public function getBillingEmail(): ?string
    {
        return $this->billingEmail;
    }

    public function setBillingEmail(?string $billingEmail): self
    {
        $this->billingEmail = $billingEmail;

        return $this;
    }

    /**
     * @return Collection<int, SoftSkill>
     */
    public function getSoftSkills(): Collection
    {
        return $this->softSkills;
    }

    public function addSoftSkill(SoftSkill $softSkill): self
    {
        if (!$this->softSkills->contains($softSkill)) {
            $this->softSkills[] = $softSkill;
        }

        return $this;
    }

    public function removeSoftSkill(SoftSkill $softSkill): self
    {
        $this->softSkills->removeElement($softSkill);

        return $this;
    }

    public function getRecruiters(): Collection
    {
        return $this->recruiters;
    }

    public function addRecruiter(Recruiter $recruiter): self
    {
        if (!$this->recruiters->contains($recruiter)) {
            $this->recruiters->add($recruiter);
            $recruiter->setCompany($this);
        }

        return $this;
    }

    public function removeRecruiter(Recruiter $recruiter): self
    {
        // set the owning side to null (unless already changed)
        if ($this->recruiters->removeElement($recruiter) && $recruiter->getCompany() === $this) {
            $recruiter->setCompany(null);
        }

        return $this;
    }

    public function getRecruiterFavorites(): Collection
    {
        return $this->recruiterFavorites;
    }

    public function addRecruiterFavorite(CompanyRecruiterFavorite $recruiterFavorite): self
    {
        if (!$this->recruiterFavorites->contains($recruiterFavorite)) {
            $this->recruiterFavorites->add($recruiterFavorite);
            $recruiterFavorite->setCompany($this);
        }

        return $this;
    }

    public function removeRecruiterFavorite(CompanyRecruiterFavorite $recruiterFavorite): self
    {
        // set the owning side to null (unless already changed)
        if ($this->recruiterFavorites->removeElement($recruiterFavorite) && $recruiterFavorite->getCompany() === $this) {
            $recruiterFavorite->setCompany(null);
        }

        return $this;
    }

    public function getBillingAddress(): ?Location
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?Location $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function isDirectoryFreeWork(): ?bool
    {
        return $this->directoryFreeWork;
    }

    public function setPictures(Collection $pictures): void
    {
        $this->pictures = $pictures;
    }

    public function getCoverPicture(): ?string
    {
        return $this->coverPicture;
    }

    public function setCoverPicture(?string $coverPicture): self
    {
        $this->coverPicture = $coverPicture;

        return $this;
    }

    public function getCoverPictureFile(): ?File
    {
        return $this->coverPictureFile;
    }

    public function setCoverPictureFile(?File $coverPictureFile): self
    {
        $this->coverPictureFile = $coverPictureFile;

        if ($coverPictureFile) {
            $this->updatedAt = Carbon::now();
        }

        return $this;
    }

    public function getQuality(): ?int
    {
        return $this->quality;
    }

    public function setQuality(?int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function getFeaturesUsage(): ?CompanyFeaturesUsage
    {
        return $this->featuresUsage;
    }

    public function setFeaturesUsage(?CompanyFeaturesUsage $featuresUsage): self
    {
        $this->featuresUsage = $featuresUsage;

        return $this;
    }
}
