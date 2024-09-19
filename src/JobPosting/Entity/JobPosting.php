<?php

namespace App\JobPosting\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Company\Entity\Company;
use App\Core\Doctrine\Filter\MultipleFieldsSearchFilter;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Entity\Job;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Entity\SoftSkill;
use App\Core\Util\Numbers;
use App\Core\Validator as CoreAssert;
use App\Core\Validator\Location as LocationAssert;
use App\JobPosting\Contracts\JobPostingInterface;
use App\JobPosting\Controller\FreeWork\JobPosting\Favorite as FreeWorkFavorite;
use App\JobPosting\Controller\FreeWork\JobPosting\GetCollection as FreeWorkGetCollection;
use App\JobPosting\Controller\FreeWork\JobPosting\GetCount as FreeWorkGetCount;
use App\JobPosting\Controller\FreeWork\JobPosting\GetFavorites as FreeWorkGetFavorites;
use App\JobPosting\Controller\FreeWork\JobPosting\GetItemBySlugs;
use App\JobPosting\Controller\FreeWork\JobPosting\GetLegacy;
use App\JobPosting\Controller\FreeWork\JobPosting\GetSuggested;
use App\JobPosting\Controller\FreeWork\JobPosting\GetSuggestedBanner;
use App\JobPosting\Controller\FreeWork\JobPosting\Trace;
use App\JobPosting\Controller\Turnover\JobPosting\Duplicate;
use App\JobPosting\Controller\Turnover\JobPosting\Favorite as TurnoverFavorite;
use App\JobPosting\Controller\Turnover\JobPosting\GetCollection as TurnoverGetCollection;
use App\JobPosting\Controller\Turnover\JobPosting\GetCount as TurnoverGetCount;
use App\JobPosting\Controller\Turnover\JobPosting\GetFavorites as TurnoverGetFavorites;
use App\JobPosting\Controller\Turnover\JobPosting\PostItem;
use App\JobPosting\Controller\Turnover\JobPosting\Trace as RecruiterTrace;
use App\JobPosting\Controller\Turnover\JobPosting\Unpublish;
use App\JobPosting\Enum\ApplicationType;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\DurationPeriod;
use App\JobPosting\Enum\ExperienceLevel;
use App\JobPosting\Enum\RemoteMode;
use App\JobPosting\Enum\Status;
use App\JobPosting\Repository\JobPostingRepository;
use App\JobPosting\Validator\JobPostingValidationGroups;
use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Validator\Teammate;
use App\Sync\Synchronizer\SynchronizableInterface;

use function count;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobPostingRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"location_value"}),
 *     @ORM\Index(columns={"old_id"}),
 *     @ORM\Index(columns={"published", "published_at"}),
 * })
 * @ApiResource(
 *     attributes={"order"={"publishedAt"="DESC"}},
 *     normalizationContext={"groups"={"job_posting:get", "location"}},
 *     denormalizationContext={"groups"={"job_posting:write"}},
 *     itemOperations={
 *          "freework_get"={
 *              "method"="GET",
 *              "path"="/job_postings/{id}",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"job_posting:get", "job_posting:get:item", "location"}},
 *          },
 *          "turnover_get"={
 *              "method"="GET",
 *              "path"="/job_postings/{id}",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and (object.getCreatedBy() == user or object.getCompany() == user.getCompany())",
 *              "normalization_context"={"groups"={"job_posting:get", "job_posting:get:item", "location", "job_posting:get:item:private"}},
 *          },
 *          "freework_get_by_slugs"={
 *              "method"="GET",
 *              "path"="/job_postings/{jobSlug}/{slug}",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "controller"=GetItemBySlugs::class,
 *              "normalization_context"={"groups"={"job_posting:get", "job_posting:get:item", "location"}},
 *              "read"=false,
 *              "openapi_context"={
 *                  "summary"="Retrieves a JobPosting resource by Job::slug and JobPosting::slug.",
 *                  "description"="Retrieves a JobPosting resource by Job::slug and JobPosting::slug.",
 *              },
 *          },
 *          "turnover_patch"={
 *              "method"="PATCH",
 *              "path"="/job_postings/{id}",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getCreatedBy() == user",
 *              "validation_groups"={JobPostingValidationGroups::class, "validationGroups"},
 *          },
 *          "turnover_post_duplicate"={
 *              "method"="POST",
 *              "path"="/job_postings/{id}/duplicate",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "controller"=Duplicate::class,
 *              "security"="is_granted('ROLE_RECRUITER') and (object.getCreatedBy() == user or object.getCompany() == user.getCompany())",
 *              "deserialize"=false,
 *              "validate"=false,
 *              "write"=false,
 *              "openapi_context"={
 *                  "summary"="Duplicate a JobPosting resource by JobPosting::id.",
 *                  "description"="Duplicate a JobPosting resource by JobPosting::id.",
 *              },
 *          },
 *          "freework_patch_favorite"={
 *              "method"="PATCH",
 *              "path"="/job_postings/{id}/favorite",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_USER')",
 *              "controller"=FreeWorkFavorite::class,
 *              "deserialize"=false,
 *              "openapi_context"={
 *                  "summary"="Update JobPostingUserFavorite resource on the JobPosting.",
 *                  "description"="Update JobPostingUserFavorite resource on the JobPosting.",
 *              },
 *          },
 *          "turnover_patch_favorite"={
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/job_postings/{id}/recruiter/favorite",
 *              "controller"=TurnoverFavorite::class,
 *              "deserialize"=false,
 *              "openapi_context"={
 *                  "summary"="Update JobPostingRecruiterFavorite resource on the JobPosting.",
 *                  "description"="Update JobPostingRecruiterFavorite resource on the JobPosting.",
 *              },
 *          },
 *          "turnover_patch_unpublish"={
 *              "method"="PATCH",
 *              "path"="/job_postings/{id}/unpublish",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getCreatedBy() == user",
 *              "controller"=Unpublish::class,
 *              "deserialize"=false,
 *          },
 *          "turnover_delete"={
 *              "method"="DELETE",
 *              "path"="/job_postings/{id}",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getCreatedBy() == user",
 *              "deserialize"=false,
 *          },
 *          "freework_post_trace"={
 *              "method"="POST",
 *              "path"="/job_postings/{id}/trace",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"job_posting:get"}},
 *              "controller"=Trace::class,
 *              "deserialize"=false,
 *              "openapi_context"={
 *                  "summary"="Create a JobPostingUserTrace resource for the JobPosting resource.",
 *                  "description"="Create a JobPostingUserTrace resource for the JobPosting resource.",
 *              },
 *          },
 *          "turnover_post_trace"={
 *              "method"="POST",
 *              "path"="/job_postings/{id}/trace",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "controller"=RecruiterTrace::class,
 *              "deserialize"=false,
 *              "openapi_context"={
 *                  "summary"="Create a JobPostingRecruiterTrace resource for the JobPosting resource.",
 *                  "description"="Create a JobPostingRecruiterTrace resource for the JobPosting resource.",
 *              },
 *          },
 *          "freework_get_legacy"={
 *              "method"="GET",
 *              "path"="/legacy/job_postings/{oldId}",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_LEGACY')",
 *              "controller"=GetLegacy::class,
 *              "normalization_context"={"groups"={"job_posting:legacy"}},
 *              "read"=false,
 *              "cache_headers"={"max_age"=0, "shared_max_age"=0},
 *              "openapi_context"={
 *                  "summary"="Retrieves a Application resource with the turnover id.",
 *                  "description"="Retrieves a Application resource with the turnover id.",
 *              },
 *          },
 *     },
 *     collectionOperations={
 *          "freework_get"={
 *              "method"="GET",
 *              "path"="/job_postings",
 *              "controller"=FreeWorkGetCollection::class,
 *              "condition"="request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *          },
 *          "freework_get_count"={
 *              "method"="GET",
 *              "path"="/job_postings/count",
 *              "controller"=FreeWorkGetCount::class,
 *              "condition"="request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *          },
 *          "turnover_get"={
 *              "method"="GET",
 *              "path"="/job_postings",
 *              "controller"=TurnoverGetCollection::class,
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "openapi_context"={
 *                  "summary"="Retrieves all job postings for the marketplace.",
 *                  "description"="Retrieves all job postings for the marketplace.",
 *              },
 *          },
 *          "turnover_get_count"={
 *              "method"="GET",
 *              "path"="/job_postings/count",
 *              "controller"=TurnoverGetCount::class,
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *          },
 *          "freework_get_favorites"={
 *              "method"="GET",
 *              "path"="/job_postings/favorites",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_USER')",
 *              "controller"=FreeWorkGetFavorites::class,
 *              "normalization_context"={"groups"={"job_posting:get", "job_posting:get:collection", "location"}},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of favorite JobPosting resources of a User resource.",
 *                  "description"="Retrieves the collection of favorite JobPosting resources of a User resource."
 *              },
 *          },
 *          "freework_get_suggested"={
 *              "method"="GET",
 *              "path"="/job_postings/suggested",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_USER')",
 *              "controller"=GetSuggested::class,
 *              "normalization_context"={"groups"={"job_posting:get", "job_posting:get:collection", "location"}},
 *          },
 *          "freework_get_suggested_banner"={
 *              "method"="GET",
 *              "path"="/job_postings/suggested/banner",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "controller"=GetSuggestedBanner::class,
 *              "normalization_context"={"groups"={"job_posting:get", "job_posting:get:collection", "location"}},
 *          },
 *          "turnover_get_recruiter_favorites"={
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "method"="GET",
 *              "path"="/job_postings/favorites",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "controller"=TurnoverGetFavorites::class,
 *              "normalization_context"={"groups"={"job_posting:get", "job_posting:get:collection", "location"}},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of favorite JobPosting resources of a Recruiter resource.",
 *                  "description"="Retrieves the collection of favorite JobPosting resources of a Recruiter resource."
 *              },
 *          },
 *          "turnover_post"={
 *              "method"="POST",
 *              "path"="/job_postings",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "normalization_context"={"groups"={"job_posting:get"}},
 *              "controller"=PostItem::class,
 *              "validation_groups"={JobPostingValidationGroups::class, "validationGroups"},
 *          },
 *          "freework_get_companies_slug_job_postings"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "path"="/companies/{slug}/job_postings",
 *              "order"={"publishedAt"="DESC"},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of JobPosting resources of the Company resource (only FW contracts).",
 *                  "description"="Retrieves the collection of JobPosting resources of the Company resource (only FW contracts)."
 *              },
 *          },
 *          "turnover_get_companies_mine_job_postings"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/companies/mine/job_postings",
 *              "security"="is_granted('RECRUITER_MAIN')",
 *              "order"={"createdAt"="DESC"},
 *              "normalization_context"={"groups"={"job_posting:get", "location", "job_posting:get:private"}},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of JobPosting resources of the logged Recruiter's Company resource.",
 *                  "description"="Retrieves the collection of JobPosting resources of the logged Recruiter's Company resource."
 *              },
 *          },
 *          "turnover_get_recruiters_me_job_postings"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/recruiters/me/job_postings",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "order"={"createdAt"="DESC"},
 *              "normalization_context"={"groups"={"job_posting:get", "location", "job_posting:get:private"}},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of JobPosting resources of the logged Recruiter resource.",
 *                  "description"="Retrieves the collection of JobPosting resources of the logged Recruiter resource."
 *              },
 *          },
 *          "turnover_get_companies_slug_job_postings"={
 *              "method"="GET",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "normalization_context"={"groups"={"job_posting:get", "location"}},
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/companies/{slug}/job_postings",
 *              "order"={"createdAt"="DESC"},
 *              "openapi_context"={
 *                  "summary"="Retrieves the collection of JobPosting resources of the Company resource (minimum intercontract).",
 *                  "description"="Retrieves the collection of JobPosting resources of the Company resource (minimum intercontract)."
 *              },
 *          }
 *     },
 * )
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(OrderFilter::class, properties={"publishedAt"="DESC", "updatedAt"="DESC", "createdAt"="DESC", "status"="DESC", "viewsCount"="DESC", "applicationsCount"="DESC", "minDailySalary"="ASC"})
 * @ApiFilter(SearchFilter::class, properties={"contracts"="partial", "title"="partial", "reference"="partial", "status"="partial"})
 * @ApiFilter(MultipleFieldsSearchFilter::class, properties={"title", "reference"})
 */
class JobPosting implements JobPostingInterface, SynchronizableInterface
{
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"job_posting:get", "application:get", "job_posting:legacy", "notification:get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"job_posting:get", "application:get", "job_posting:legacy", "job_posting:write", "notification:get"})
     * @Assert\Length(minMessage="generic.length.min", min="5", groups={"job_posting:post:status-published"})
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"title"})
     * @Groups({"job_posting:get", "application:get", "job_posting:legacy", "notification:get"})
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="text")
     * @Groups({"job_posting:get", "job_posting:write"})
     * @Assert\NotBlank(groups={"job_posting:write"})
     * @Assert\Length(minMessage="generic.length.min", min="150", groups={"job_posting:post:status-published"})
     */
    private ?string $description = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"job_posting:get", "job_posting:write"})
     * @Assert\NotBlank(groups={"job_posting:write"})
     */
    private ?string $candidateProfile = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"job_posting:get", "job_posting:write"})
     * @Assert\NotBlank(groups={"job_posting:write"})
     */
    private ?string $companyDescription = null;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", length=12, nullable=true)
     * @Groups({"job_posting:get", "job_posting:write"})
     * @EnumAssert(message="generic.enum.message", class=ExperienceLevel::class, groups={"job_posting:post:status-published"})
     * @Assert\NotNull(groups={"job_posting:post:status-published"})
     */
    private ?string $experienceLevel = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @Assert\NotNull(message="generic.not_null", groups={"job_posting:post:status-published:contract-work"})
     * @Assert\GreaterThan(0, groups={"job_posting:post:status-published:contract-work"})
     * @Assert\LessThan(propertyPath="maxAnnualSalary", groups={"job_posting:post:status-published:contract-work"})
     */
    private ?int $minAnnualSalary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @Assert\NotNull(message="generic.not_null", groups={"job_posting:post:status-published:contract-work"})
     * @Assert\GreaterThan(0, groups={"job_posting:post:status-published:contract-work"})
     */
    private ?int $maxAnnualSalary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @Assert\NotNull(message="generic.not_null", groups={"job_posting:post:status-published:contract-free"})
     * @Assert\GreaterThan(0, groups={"job_posting:post:status-published:contract-free"})
     * @Assert\LessThan(propertyPath="maxDailySalary", groups={"job_posting:post:status-published:contract-free"})
     */
    private ?int $minDailySalary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @Assert\NotNull(message="generic.not_null", groups={"job_posting:post:status-published:contract-free"})
     * @Assert\GreaterThan(0, groups={"job_posting:post:status-published:contract-free"})
     */
    private ?int $maxDailySalary = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     */
    private ?string $currency = null;

    /**
     * @var ?array
     *
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @Assert\Count(minMessage="generic.count.min", min="1", groups={"job_posting:post:status-published"})
     * @EnumAssert(message="generic.enum.message", class=Contract::class, multiple=true, multipleMessage="generic.enum.multiple", groups={"job_posting:post:status-published"})
     * @CoreAssert\ChoiceAtLeast(groups={"job_posting_share:post"}, message="job_posting.contracts.choice_at_least", min=1, choices={"intercontract"})
     */
    private ?array $contracts = null;

    /**
     * @var ?int
     *
     * @ORM\Column(type="integer", length=10, nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     */
    private ?int $duration = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting:get", "job_posting:write"})
     * @Assert\NotNull(message="generic.not_null", groups={"job_posting:post:status-published:contract-temporary"})
     * @Assert\GreaterThan(0, groups={"job_posting:post:status-published:contract-temporary"})
     */
    private ?int $durationValue;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"job_posting:get", "job_posting:write"})
     * @EnumAssert(message="generic.enum.message", class=DurationPeriod::class, groups={"job_posting:write"})
     * @Assert\NotNull(message="generic.not_null", groups={"job_posting:post:status-published:contract-temporary"})
     */
    private ?string $durationPeriod;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @Assert\NotNull(message="generic.not_null", groups={"job_posting:post:status-published:contract-temporary"})
     */
    private ?bool $renewable = false;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @EnumAssert(message="generic.enum.message", class=RemoteMode::class, groups={"job_posting:post:status-published"})
     */
    private ?string $remoteMode = null;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @EnumAssert(message="generic.enum.message", class=ApplicationType::class, groups={"job_posting:post:status-published"})
     * @Assert\NotBlank(groups={"job_posting:post:status-published"})
     */
    private ?string $applicationType = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @Assert\NotBlank(groups={"job_posting:post:status-published:type-contact"})
     * @Assert\Length(minMessage="generic.length.min", min="5", groups={"job_posting:post:status-published:type-contact"}, allowEmptyString=true)
     */
    private ?string $applicationContact = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @Assert\Url(message="generic.url", groups={"job_posting:post:status-published:type-url"})
     * @Assert\NotBlank(groups={"job_posting:post:status-published:type-url"})
     */
    private ?string $applicationUrl = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"job_posting:get", "job_posting:write"})
     * @Assert\Email(message="generic.email", groups={"job_posting:post:status-published:type-turnover"})
     * @Assert\NotBlank(groups={"job_posting:post:status-published:type-turnover"})
     */
    private ?string $applicationEmail;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"job_posting:get", "application:get"})
     */
    private int $applicationsCount = 0;

    /**
     * @ORM\Embedded(class="App\Core\Entity\Location")
     * @Groups({"location", "application:get", "job_posting:get", "job_posting:write"})
     * @Assert\NotBlank(groups={"job_posting:post:status-published"})
     * @LocationAssert(message="core.location.invalid", groups={"job_posting:post:status-published"})
     */
    private Location $location;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     * @Assert\GreaterThan("today", groups={"job_posting:post:status-published"})
     */
    private ?\DateTimeInterface $startsAt = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     */
    private ?string $reference = null;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="jobPostings")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Groups({"job_posting:get", "application:get", "job_posting:legacy", "job_posting:write"})
     */
    private ?Company $company = null;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"job_posting:get", "application:get", "job_posting:legacy", "job_posting:write"})
     */
    private ?Job $job = null;

    /**
     * @ORM\ManyToMany(targetEntity=Skill::class, inversedBy="jobPostings")
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     */
    private Collection $skills;

    /**
     * @ORM\OneToMany(targetEntity=JobPostingUserFavorite::class, mappedBy="jobPosting", cascade={"persist", "remove"})
     */
    private Collection $userFavorites;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"job_posting:get"})
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"job_posting:get"})
     */
    private \DateTimeInterface $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"job_posting:get"})
     */
    private ?\DateTime $publishedAt = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"job_posting:get", "job_posting:legacy", "job_posting:write"})
     */
    private ?bool $published = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"application:legacy", "job_posting_trace:legacy"})
     */
    private ?int $oldId = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"job_posting:write"})
     * @Assert\NotNull(message="generic.not_null", groups={"job_posting:post:status-published"})
     */
    private ?bool $multicast = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"job_posting:get", "job_posting:write"})
     * @EnumAssert(message="generic.enum.message", class=Status::class, groups={"job_posting:write"})
     * @Assert\EqualTo(value=Status::PUBLISHED, groups={"job_posting_share:post"}, message="job_posting.status.equal_to_published")
     */
    private ?string $status;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $supplyEntryChannel;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"job_posting:get:private"})
     */
    private ?int $viewsCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $daysOnlineCount = 0;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"job_posting:write"})
     */
    private ?bool $pushToTop;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $pushedToTopCount = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $pushedToTopAt = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"job_posting:write"})
     */
    private ?int $quality = null;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     * @Groups({"job_posting:write"})
     * @Gedmo\Blameable(on="create")
     */
    private ?Recruiter $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     * @Groups({"job_posting:write"})
     * @Teammate(message="recruiter.teammate.invalid", groups={"job_posting:post:status-published"})
     * @Assert\NotBlank(groups={"job_posting:post:status-published"})
     */
    private ?Recruiter $assignedTo;

    /**
     * @ORM\ManyToMany(targetEntity=SoftSkill::class)
     * @Groups({"job_posting:get", "application:get", "job_posting:write"})
     */
    private Collection $softSkills;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"job_posting:write"})
     */
    private ?bool $received;

    /**
     * @ORM\OneToMany(targetEntity=JobPostingRecruiterFavorite::class, mappedBy="jobPosting", cascade={"persist", "remove"})
     */
    private Collection $recruiterFavorites;

    /**
     * @Gedmo\Timestampable(on="change", field={"status"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $statusUpdatedAt = null;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->userFavorites = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->location = new Location();
        $this->softSkills = new ArrayCollection();
        $this->recruiterFavorites = new ArrayCollection();
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getExperienceLevel(): ?string
    {
        return $this->experienceLevel;
    }

    public function setExperienceLevel(?string $experienceLevel): self
    {
        $this->experienceLevel = $experienceLevel;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRenewable(): ?bool
    {
        return $this->renewable;
    }

    public function setRenewable(?bool $renewable): self
    {
        $this->renewable = $renewable;

        return $this;
    }

    public function getRemoteMode(): ?string
    {
        return $this->remoteMode;
    }

    public function setRemoteMode(?string $remoteMode): self
    {
        $this->remoteMode = $remoteMode;

        return $this;
    }

    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(?\DateTimeInterface $startsAt): self
    {
        $this->startsAt = $startsAt;

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

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

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

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getUserFavorites(): Collection
    {
        return $this->userFavorites;
    }

    public function addUserFavorite(JobPostingUserFavorite $favorite): self
    {
        if (!$this->userFavorites->contains($favorite)) {
            $this->userFavorites[] = $favorite;
            $favorite->setJobPosting($this);
        }

        return $this;
    }

    public function removeUserFavorite(JobPostingUserFavorite $favorite): self
    {
        // set the owning side to null (unless already changed)
        if ($this->userFavorites->removeElement($favorite) && $favorite->getJobPosting() === $this) {
            $favorite->setJobPosting(null);
        }

        return $this;
    }

    public function getApplicationsCount(): int
    {
        return $this->applicationsCount;
    }

    public function setApplicationsCount(int $applicationsCount): self
    {
        $this->applicationsCount = $applicationsCount;

        return $this;
    }

    public function getContracts(): ?array
    {
        return $this->contracts;
    }

    public function setContracts(?array $contracts): self
    {
        $this->contracts = $contracts;

        return $this;
    }

    public function getMinAnnualSalary(): ?int
    {
        return $this->minAnnualSalary;
    }

    public function setMinAnnualSalary(?int $minAnnualSalary): self
    {
        $this->minAnnualSalary = $minAnnualSalary;

        if ($this->getMaxAnnualSalary() < $this->minAnnualSalary) {
            $this->setMaxAnnualSalary($minAnnualSalary);
        }

        return $this;
    }

    public function getMaxAnnualSalary(): ?int
    {
        return $this->maxAnnualSalary;
    }

    public function setMaxAnnualSalary(?int $maxAnnualSalary): self
    {
        $this->maxAnnualSalary = $maxAnnualSalary;

        if ($this->maxAnnualSalary < $this->minAnnualSalary) {
            $this->maxAnnualSalary = $this->minAnnualSalary;
        }

        return $this;
    }

    public function getMinDailySalary(): ?int
    {
        return $this->minDailySalary;
    }

    public function setMinDailySalary(?int $minDailySalary): self
    {
        $this->minDailySalary = $minDailySalary;

        if ($this->getMaxDailySalary() < $this->minDailySalary) {
            $this->setMaxDailySalary($minDailySalary);
        }

        return $this;
    }

    public function getMaxDailySalary(): ?int
    {
        return $this->maxDailySalary;
    }

    public function setMaxDailySalary(?int $maxDailySalary): self
    {
        $this->maxDailySalary = $maxDailySalary;

        if ($this->maxDailySalary < $this->minDailySalary) {
            $this->maxDailySalary = $this->minDailySalary;
        }

        return $this;
    }

    /**
     * @Groups({"job_posting:get", "application:get"})
     */
    public function getAnnualSalary(): ?string
    {
        return Numbers::formatRangeCurrency($this->minAnnualSalary, $this->maxAnnualSalary, $this->currency ?? 'EUR');
    }

    /**
     * @Groups({"job_posting:get", "application:get"})
     */
    public function getDailySalary(): ?string
    {
        return Numbers::formatRangeCurrency($this->minDailySalary, $this->maxDailySalary, $this->currency ?? 'EUR');
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function hasFreeContract(): bool
    {
        return \count(array_intersect($this->contracts ?? [], [Contract::CONTRACTOR, Contract::INTERCONTRACT])) > 0;
    }

    public function hasWorkContract(): bool
    {
        return \count(array_intersect($this->contracts ?? [], [Contract::APPRENTICESHIP, Contract::FIXED_TERM, Contract::PERMANENT, Contract::INTERNSHIP])) > 0;
    }

    public function hasTemporaryContract(): bool
    {
        return \count(array_intersect($this->contracts ?? [], [Contract::APPRENTICESHIP, Contract::FIXED_TERM, Contract::INTERNSHIP, Contract::CONTRACTOR, Contract::INTERCONTRACT])) > 0;
    }

    public function hasPermanentContract(): bool
    {
        return \count(array_intersect($this->contracts ?? [], [Contract::PERMANENT])) > 0;
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

    public function getCandidateProfile(): ?string
    {
        return $this->candidateProfile;
    }

    public function setCandidateProfile(?string $candidateProfile): self
    {
        $this->candidateProfile = $candidateProfile;

        return $this;
    }

    public function getCompanyDescription(): ?string
    {
        return $this->companyDescription;
    }

    public function setCompanyDescription(?string $companyDescription): self
    {
        $this->companyDescription = $companyDescription;

        return $this;
    }

    public function getApplicationType(): ?string
    {
        return $this->applicationType;
    }

    public function setApplicationType(?string $applicationType): self
    {
        $this->applicationType = $applicationType;

        return $this;
    }

    public function getApplicationContact(): ?string
    {
        return $this->applicationContact;
    }

    public function setApplicationContact(?string $applicationContact): self
    {
        $this->applicationContact = $applicationContact;

        return $this;
    }

    public function getApplicationUrl(): ?string
    {
        return $this->applicationUrl;
    }

    public function setApplicationUrl(?string $applicationUrl): self
    {
        $this->applicationUrl = $applicationUrl;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

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

    public function getDurationValue(): ?int
    {
        return $this->durationValue;
    }

    public function setDurationValue(?int $durationValue): self
    {
        $this->durationValue = $durationValue;

        return $this;
    }

    public function getDurationPeriod(): ?string
    {
        return $this->durationPeriod;
    }

    public function setDurationPeriod(?string $durationPeriod): self
    {
        $this->durationPeriod = $durationPeriod;

        return $this;
    }

    public function isMulticast(): ?bool
    {
        return $this->multicast;
    }

    public function setMulticast(bool $multicast): self
    {
        $this->multicast = $multicast;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSupplyEntryChannel(): ?string
    {
        return $this->supplyEntryChannel;
    }

    public function setSupplyEntryChannel(?string $supplyEntryChannel): self
    {
        $this->supplyEntryChannel = $supplyEntryChannel;

        return $this;
    }

    public function getCreatedBy(): ?Recruiter
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Recruiter $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getApplicationEmail(): ?string
    {
        return $this->applicationEmail;
    }

    public function setApplicationEmail(?string $applicationEmail): self
    {
        $this->applicationEmail = $applicationEmail;

        return $this;
    }

    /**
     * @return Collection|SoftSkill[]
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

    public function isRenewable(): ?bool
    {
        return $this->renewable;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function getViewsCount(): ?int
    {
        return $this->viewsCount;
    }

    public function setViewsCount(int $viewsCount): self
    {
        $this->viewsCount = $viewsCount;

        return $this;
    }

    public function getDaysOnlineCount(): ?int
    {
        return $this->daysOnlineCount;
    }

    public function setDaysOnlineCount(int $daysOnlineCount): self
    {
        $this->daysOnlineCount = $daysOnlineCount;

        return $this;
    }

    public function getQuality(): ?int
    {
        return $this->quality;
    }

    public function setQuality(int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function isReceived(): ?bool
    {
        return $this->received;
    }

    public function setReceived(?bool $received): self
    {
        $this->received = $received;

        return $this;
    }

    public function isPushToTop(): ?bool
    {
        return $this->pushToTop;
    }

    public function setPushToTop(?bool $pushToTop): self
    {
        $this->pushToTop = $pushToTop;

        return $this;
    }

    public function getPushedToTopCount(): ?int
    {
        return $this->pushedToTopCount;
    }

    public function setPushedToTopCount(int $pushedToTopCount): self
    {
        $this->pushedToTopCount = $pushedToTopCount;

        return $this;
    }

    public function getPushedToTopAt(): ?\DateTimeInterface
    {
        return $this->pushedToTopAt;
    }

    public function setPushedToTopAt(?\DateTimeInterface $pushedToTopAt): self
    {
        $this->pushedToTopAt = $pushedToTopAt;

        return $this;
    }

    public function getAssignedTo(): ?Recruiter
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?Recruiter $assignedTo): self
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * @Groups({"job_posting:get"})
     */
    public function getExpiredAt(): ?\DateTime
    {
        return $this->publishedAt ? (clone ($this->publishedAt)->modify('+45 days')) : null;
    }

    public function getRecruiterFavorites(): Collection
    {
        return $this->recruiterFavorites;
    }

    public function addRecruiterFavorite(JobPostingRecruiterFavorite $recruiterFavorite): self
    {
        if (!$this->recruiterFavorites->contains($recruiterFavorite)) {
            $this->recruiterFavorites[] = $recruiterFavorite;
            $recruiterFavorite->setJobPosting($this);
        }

        return $this;
    }

    public function removeRecruiterFavorite(JobPostingRecruiterFavorite $recruiterFavorite): self
    {
        // set the owning side to null (unless already changed)
        if ($this->recruiterFavorites->removeElement($recruiterFavorite) && $recruiterFavorite->getJobPosting() === $this) {
            $recruiterFavorite->setJobPosting(null);
        }

        return $this;
    }

    public function getStatusUpdatedAt(): ?\DateTimeInterface
    {
        return $this->statusUpdatedAt;
    }

    public function setStatusUpdatedAt(?\DateTimeInterface $statusUpdatedAt): self
    {
        $this->statusUpdatedAt = $statusUpdatedAt;

        return $this;
    }
}
