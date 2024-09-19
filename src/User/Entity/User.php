<?php

namespace App\User\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use App\Blog\Entity\BlogComment;
use App\Company\Entity\CompanyBlacklist;
use App\Core\Annotation\ApiThumbnailUrls;
use App\Core\Doctrine\Filter\SearchFilter;
use App\Core\Doctrine\Filter\TimestampFilter;
use App\Core\Entity\Job;
use App\Core\Entity\Location;
use App\Core\Entity\SoftSkill;
use App\Core\Enum\Currency;
use App\Core\Enum\EmploymentTime;
use App\Core\Enum\Gender;
use App\Core\Util\Numbers;
use App\Core\Validator as CoreAssert;
use App\Forum\Entity\ForumPost;
use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\JobPostingSearch;
use App\JobPosting\Enum\Contract;
use App\Messaging\Entity\FeedUser;
use App\Partner\Entity\Partner;
use App\Recruiter\Entity\Recruiter;
use App\User\Contracts\UserInterface;
use App\User\Controller\FreeWork\Lead\Post;
use App\User\Controller\FreeWork\User\Data;
use App\User\Controller\FreeWork\User\Delete;
use App\User\Controller\FreeWork\User\DeleteAvatar;
use App\User\Controller\FreeWork\User\DeleteProfile;
use App\User\Controller\FreeWork\User\NicknameExists;
use App\User\Controller\FreeWork\User\PatchChangePassword;
use App\User\Controller\FreeWork\User\PostAvatar;
use App\User\Controller\FreeWork\User\PostPartner;
use App\User\Controller\FreeWork\User\Stats;
use App\User\Controller\Turnover\User\DeleteItem;
use App\User\Controller\Turnover\User\PatchItem;
use App\User\Controller\Turnover\User\PostItem;
use App\User\Enum\Availability;
use App\User\Enum\CompanyCountryCode;
use App\User\Enum\ExperienceYear;
use App\User\Enum\FreelanceLegalStatus;
use App\User\Enum\NafCode;
use App\User\Enum\UserProfileStep;
use App\User\Repository\UserRepository;
use App\User\Validator as AppAssert;
use App\User\Validator\UserJobSearchPreferencesValidationGroups;
use App\User\Validator\UserStatusValidationGroups;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Gedmo\Loggable()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"email"}),
 *     @ORM\Index(columns={"updated_at"}),
 *     @ORM\Index(columns={"location_value"}),
 *     @ORM\Index(columns={"nickname"}),
 *     @ORM\Index(columns={"confirmation_token"}),
 *     @ORM\Index(columns={"status_updated_at"}),
 *     @ORM\Index(columns={"next_availability_at"}),
 *     @ORM\Index(columns={"last_login_at"}),
 *     @ORM\Index(columns={"created_at"}),
 *     @ORM\Index(columns={"origin"}),
 * })
 * @UniqueEntity(fields={"email"}, groups={"Default", "user:change_email:request"})
 * @UniqueEntity(fields={"nickname"}, groups={"Default", "user:patch:identity", "user:patch:forum_preferences"})
 * @ApiResource(
 *     forceEager = false,
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"user:get"}},
 *          },
 *          "get_me"={
 *              "path"="/users/me",
 *              "method"="GET",
 *              "openapi_context"={
 *                 "parameters"={}
 *              },
 *              "read"=false
 *          },
 *          "freework_get_data"={
 *              "security"="object == user",
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "controller"=Data::class,
 *              "path"="/users/{id}/data",
 *              "deserialize"=false,
 *          },
 *          "freework_get_stats"={
 *              "security"="object == user",
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "controller"=Stats::class,
 *              "path"="/users/{id}/stats",
 *              "deserialize"=false,
 *          },
 *          "freework_patch_profile_personal_info"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:personal_info"}},
 *              "denormalization_context"={"groups"={"user:patch:personal_info"}},
 *              "validation_groups"={"user:patch:personal_info"},
 *              "path"="/users/{id}/personal_info",
 *              "openapi_context"={
 *                  "summary"="Update User personal informations.",
 *                  "description"="Update User personal informations.",
 *              },
 *          },
 *          "freework_patch_profile_job_search_preferences"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:job_search_preferences"}},
 *              "denormalization_context"={"groups"={"user:patch:job_search_preferences"}},
 *              "validation_groups"={UserJobSearchPreferencesValidationGroups::class, "validationGroups"},
 *              "path"="/users/{id}/job_search_preferences",
 *              "openapi_context"={
 *                  "summary"="Update User job's search preferences.",
 *                  "description"="Update User job's search preferences.",
 *              },
 *          },
 *          "freework_patch_profile_skills_and_languages"={
 *              "security"="object == user",
 *              "method"="PUT",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:put:skills_and_languages"}},
 *              "denormalization_context"={"groups"={"user:put:skills_and_languages"}},
 *              "validation_groups"={"user:put:skills_and_languages"},
 *              "path"="/users/{id}/skills_and_languages",
 *              "openapi_context"={
 *                  "summary"="Update User skills and languages.",
 *                  "description"="Update User skills and languages.",
 *              },
 *          },
 *          "freework_patch_profile_education"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:education"}},
 *              "denormalization_context"={"groups"={"user:patch:education"}},
 *              "validation_groups"={"user:patch:education"},
 *              "path"="/users/{id}/education",
 *              "openapi_context"={
 *                  "summary"="Update User education.",
 *                  "description"="Update User education.",
 *              },
 *          },
 *          "freework_patch_profile_about_me"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:about_me"}},
 *              "denormalization_context"={"groups"={"user:patch:about_me"}},
 *              "validation_groups"={"user:patch:about_me"},
 *              "path"="/users/{id}/about_me",
 *              "openapi_context"={
 *                  "summary"="Update User about me.",
 *                  "description"="Update User about me.",
 *              },
 *          },
 *          "freework_patch_identity"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:identity"}},
 *              "denormalization_context"={"groups"={"user:patch:identity"}},
 *              "validation_groups"={"user:patch:identity"},
 *              "path"="/users/{id}/identity",
 *              "openapi_context"={
 *                  "summary"="Update User identity.",
 *                  "description"="Update User identity.",
 *              },
 *          },
 *          "freework_patch_change_password"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:change_password"}},
 *              "controller"=PatchChangePassword::class,
 *              "denormalization_context"={"groups"={"user:patch:change_password"}},
 *              "validation_groups"={"user:patch:change_password"},
 *              "path"="/users/{id}/change_password",
 *              "openapi_context"={
 *                  "summary"="Update User password.",
 *                  "description"="Update User password.",
 *              },
 *          },
 *          "freework_patch_forum_preferences"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:forum_preferences"}},
 *              "denormalization_context"={"groups"={"user:patch:forum_preferences"}},
 *              "validation_groups"={"user:patch:forum_preferences"},
 *              "path"="/users/{id}/forum_preferences",
 *              "openapi_context"={
 *                  "summary"="Update User forum preferences.",
 *                  "description"="Update forum preferences.",
 *              },
 *          },
 *          "freework_patch_status"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:status"}},
 *              "denormalization_context"={"groups"={"user:patch:status"}},
 *              "path"="/users/{id}/status",
 *              "validation_groups"={UserStatusValidationGroups::class, "validationGroups"},
 *              "openapi_context"={
 *                  "summary"="Update User status (ie availability and visible).",
 *                  "description"="Update User status (ie availability and visible).",
 *              },
 *          },
 *          "freework_patch_notifications"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:notifications"}},
 *              "denormalization_context"={"groups"={"user:patch:notifications"}},
 *              "validation_groups"={"user:patch:notifications"},
 *              "path"="/users/{id}/notifications",
 *              "openapi_context"={
 *                  "summary"="Update User notifications.",
 *                  "description"="Update notifications.",
 *              },
 *          },
 *          "freework_patch_terms_of_service"={
 *              "security"="object == user",
 *              "method"="PATCH",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"user:patch:terms_of_service"}},
 *              "denormalization_context"={"groups"={"user:patch:terms_of_service"}},
 *              "validation_groups"={"user:patch:terms_of_service"},
 *              "path"="/users/{id}/terms_of_service",
 *              "openapi_context"={
 *                  "summary"="Update User terms of service.",
 *                  "description"="Update User terms of service.",
 *              },
 *          },
 *          "freework_delete"={
 *              "method"="DELETE",
 *              "security"="object == user",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "controller"=Delete::class,
 *          },
 *          "freework_post_avatar"={
 *              "controller"=PostAvatar::class,
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "path"="/users/{id}/avatar",
 *              "deserialize"=false,
 *              "security"="object == user",
 *              "validation_groups"={"Default", "user:post_avatar"},
 *              "normalization_context"={"groups"={"user:get"}},
 *              "openapi_context"={
 *                  "summary"="Add or edit User avatar.",
 *                  "description"="Add or edit User avatar.",
 *                  "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *          },
 *          "freework_delete_avatar"={
 *              "controller"=DeleteAvatar::class,
 *              "method"="DELETE",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "path"="/users/{id}/avatar",
 *              "deserialize"=false,
 *              "security"="object == user",
 *              "openapi_context"={
 *                  "summary"="Removes the User avatar.",
 *                  "description"="Removes the User avatar.",
 *              }
 *          },
 *          "freework_delete_profile"={
 *              "controller"=DeleteProfile::class,
 *              "method"="DELETE",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "path"="/users/{id}/profile",
 *              "deserialize"=false,
 *              "security"="object == user",
 *              "openapi_context"={
 *                  "summary"="Removes the User profile.",
 *                  "description"="Removes the User profile.",
 *              }
 *          },
 *          "freework_post_partner"={
 *              "controller"=PostPartner::class,
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "path"="/users/{id}/partner",
 *              "deserialize"=false,
 *              "security"="object == user",
 *              "validation_groups"={"Default"},
 *              "normalization_context"={"groups"={"user:get"}},
 *              "openapi_context"={
 *                  "summary"="Add user partner",
 *                  "description"="Add user partner.",
 *             }
 *          },
 *         "freework_post_lead"={
 *              "controller"=Post::class,
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "path"="/users/{id}/lead",
 *              "deserialize"=false,
 *              "security"="object == user",
 *              "validation_groups"={"Default"},
 *              "normalization_context"={"groups"={"user:get"}},
 *              "openapi_context"={
 *                  "summary"="Add user lead",
 *                  "description"="Add user lead.",
 *             }
 *          },
 *          "turnover_patch"={
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and object.getCreatedBy() == user",
 *              "method"="POST",
 *              "normalization_context"={"groups"={"user:turnover_get"}},
 *              "denormalization_context"={"groups"={"user:turnover_write"}},
 *              "controller"=PatchItem::class,
 *              "deserialize"=false,
 *              "validation_groups"={"user:turnover_write"},
 *              "path"="/users/{id}",
 *              "openapi_context"={
 *                  "summary"="Update User Candidate.",
 *                  "description"="Update User Candidate.",
 *              },
 *          },
 *          "turnover_delete"={
 *              "method"="DELETE",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER') and (object.getCreatedBy() == user or object.getCreatedBy().getCompany() == user.getCompany())",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "controller"=DeleteItem::class,
 *          },
 *     },
 *     collectionOperations={
 *          "freework_post"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "security"="is_granted('IS_AUTHENTICATED_REMEMBERED') == false",
 *              "normalization_context"={"groups"={"user:get", "user:get:private"}},
 *              "denormalization_context"={"groups"={"user:post"}},
 *              "validation_groups"={"Default", "user:post"}
 *          },
 *          "freework_get_legacy"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "path"="/legacy/users",
 *              "security"="is_granted('ROLE_LEGACY')",
 *              "normalization_context"={"groups"={"user:legacy"}},
 *              "order"={"updatedAt"="ASC", "createdAt"="ASC", "id"="ASC"},
 *              "fetch_partial" = true,
 *              "pagination_partial" = true,
 *              "cache_headers"={"max_age"=0, "shared_max_age"=0},
 *          },
 *          "freework_get_nickname_exists"={
 *              "security"="is_granted('ROLE_USER')",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "method"="GET",
 *              "path"="/users/exists/{nickname}",
 *              "controller"=NicknameExists::class,
 *              "deserialize"=false,
 *          },
 *          "turnover_post"={
 *              "method"="POST",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "controller"=PostItem::class,
 *              "deserialize"=false,
 *              "denormalization_context"={"groups"={"user:turnover_write"}},
 *              "normalization_context"={"groups"={"user:turnover_get"}},
 *              "validation_groups"={"user:turnover_write"}
 *          },
 *          "turnover_get_company_candidates"={
 *              "method"="GET",
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "normalization_context"={"groups"={"user:get:candidates"}},
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/companies/{slug}/candidates",
 *              "order"={"createdAt"="DESC"},
 *          },
 *          "turnover_get_recruiter_candidates"={
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "method"="GET",
 *              "normalization_context"={"groups"={"user:turnover_get"}},
 *              "security"="is_granted('ROLE_RECRUITER')",
 *              "path"="/recruiters/me/candidates",
 *          },
 *          "turnover_get_last_viewed"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_turnover_pattern_base_url%'",
 *              "path"="/users/last_viewed",
 *              "normalization_context"={"groups"={"user:get_turnover:collection"}},
 *              "openapi_context"={
 *                  "summary"="Retrieves the last resumes viewed",
 *                  "description"="Retrieves the last resumes viewed"
 *              },
 *          },
 *     },
 *     subresourceOperations={
 *          "applications_get_subresource"={
 *              "path"="/users/{id}/applications",
 *          }
 *     }
 * )
 * @Vich\Uploadable()
 * @CoreAssert\CompanyRegistrationNumber(countryCodeProperty="companyCountryCode", registrationNumberProperty="companyRegistrationNumber", parameters={"FR"={"type"="siren"}}, groups={"user:patch:job_search_preferences"})
 * @AppAssert\UserJobSearchPreferences(groups={"user:patch:job_search_preferences"})
 * @AppAssert\UserIdentity(groups={"user:patch:identity"})
 * @ApiFilter(SearchFilter::class, properties={"step"="exact", "state"="exact"})
 * @ApiFilter(TimestampFilter::class, properties={"createdAt", "updatedAt", "deletedAt"})
 * @ApiFilter(BooleanFilter::class, properties={"visible"})
 * @ApiFilter(RangeFilter::class, properties={"applicationsCount"})
 * @ApiFilter(NumericFilter::class, properties={"applicationsCount"})
 * @ApiFilter(OrderFilter::class, properties={"applicationsCount"="DESC", "viewsCount"="DESC"})
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get", "forum_post:get", "forum_topic:get", "forum_category:get", "blog_comment:get", "migration", "feed:get", "application:legacy", "user:legacy", "job_posting_trace:legacy", "user:get:candidates", "folder:get:item"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"user:get:private", "user:post", "migration", "application:legacy", "user:legacy"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"Default", "user:login", "user:forgotten_password:request", "user:change_email:request"})
     * @AppAssert\UserEmail(groups={"Default", "user:login", "user:forgotten_password:request", "user:change_email:request"})
     * @Gedmo\Versioned()
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="30", groups={"user:patch:identity", "user:patch:forum_preferences"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:identity", "user:patch:forum_preferences"})
     * @Assert\Regex(pattern="/[a-zA-Z]+/", match=true, message="user.nickname.regex", groups={"user:patch:identity", "user:patch:forum_preferences"})
     * @Groups({"user:get", "user:patch:identity", "user:patch:forum_preferences", "forum_post:get", "forum_topic:get", "forum_category:get", "blog_comment:get", "migration", "feed:get"})
     * @Gedmo\Versioned()
     */
    private ?string $nickname = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Gedmo\Slug(fields={"nickname"})
     * @Groups({"user:get", "forum_post:get", "forum_topic:get", "forum_category:get", "blog_comment:get", "feed:get"})
     */
    private ?string $nicknameSlug = null;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     * @Groups({"user:get:private", "user:patch:personal_info", "user:legacy"})
     * @Assert\NotBlank(message="generic.phone.valid", groups={"user:patch:personal_info"})
     * @AssertPhoneNumber(message="generic.phone.valid", groups={"user:patch:personal_info"})
     * @Gedmo\Versioned()
     */
    private ?PhoneNumber $phone = null;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:get:private"})
     * @Gedmo\Versioned()
     */
    private array $roles = [];
    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:login"})
     */
    private ?string $password = null;

    /**
     * @Groups({"user:post", "user:forgotten_password:reset"})
     * @CoreAssert\PasswordComplexity(minScore=2, groups={"user:post", "user:forgotten_password:reset"})
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"user:get:private", "user:patch:identity", "feed:get", "user:legacy", "user:get:candidates", "folder:get:item", "user:get_turnover:collection"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", min="2", minMessage="generic.length.min", groups={"user:patch:identity"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:identity"})
     * @Gedmo\Versioned()
     */
    private ?string $firstName = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"user:get:private", "user:patch:identity", "feed:get", "user:legacy", "user:get:candidates", "folder:get:item", "user:get_turnover:collection"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", min="2", minMessage="generic.length.min", groups={"user:patch:identity"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:identity"})
     * @Gedmo\Versioned()
     */
    private ?string $lastName = null;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"user:get:private", "user:patch:identity", "user:legacy"})
     * @EnumAssert(message="generic.enum.message", class=Gender::class, groups={"user:patch:identity"})
     * @ApiProperty(attributes={"openapi_context"={"type"="string", "enum"={"male", "female"}, "example"="male"}})
     * @Gedmo\Versioned()
     */
    private ?string $gender = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $confirmationToken = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"user:patch:forum_preferences"})
     * @Groups({"user:get", "user:patch:forum_preferences", "forum_post:get", "forum_topic:get", "forum_category:get", "user:legacy", "folder:get:item"})
     * @Gedmo\Versioned()
     */
    private ?string $jobTitle = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"user:get", "user:patch:forum_preferences", "forum_post:get", "forum_topic:get", "forum_category:get", "user:legacy"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"user:patch:forum_preferences"})
     * @Assert\Url(message="generic.url", groups={"user:patch:forum_preferences"})
     * @Gedmo\Versioned()
     */
    private ?string $website = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="100", groups={"user:patch:personal_info", "user:turnover_write"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:personal_info", "user:turnover_write"})
     * @Groups({"user:get:private", "user:patch:personal_info", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     * @Gedmo\Versioned()
     */
    private ?string $profileJobTitle = null;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @EnumAssert(message="generic.enum.message", class=ExperienceYear::class, groups={"user:patch:personal_info", "user:turnover_write"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:personal_info", "user:turnover_write"})
     * @Groups({"user:get:private", "user:patch:personal_info", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get", "user:get_turnover:collection"})
     * @Gedmo\Versioned()
     */
    private ?string $experienceYear = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"user:get:private", "user:patch:status", "user:legacy", "user:turnover_write", "user:turnover_get"})
     * @Assert\NotNull(message="generic.not_null", groups={"user:patch:status", "user:turnover_write", "user:turnover_get"})
     * @Gedmo\Versioned()
     */
    private ?bool $visible = true;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"user:get:private", "user:patch:status", "user:legacy", "user:patch:personal_info", "user:get:candidates", "user:turnover_write", "user:turnover_get", "user:get_turnover:collection"})
     * @EnumAssert(message="generic.enum.message", class=Availability::class, groups={"user:patch:personal_info", "user:patch:status", "user:turnover_write"})
     * @Assert\NotNull(message="generic.not_null", groups={"user:patch:status", "user:turnover_write"})
     * @Gedmo\Versioned()
     */
    private ?string $availability = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"user:get:private", "user:patch:status", "user:legacy", "user:get:candidates"})
     * @Assert\NotBlank(message="generic.not_blank", groups={"user:patch:status:date"})
     * @Assert\GreaterThan("today", groups={"user:patch:status:date"}, message="user.next_availability_at.greater_than")
     * @Gedmo\Versioned()
     */
    private ?\DateTime $nextAvailabilityAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:get:private", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private ?\DateTime $statusUpdatedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:get:private"})
     * @Gedmo\Versioned()
     */
    private ?\DateTime $lastLoginAt = null;

    /**
     * @ORM\Column(type="string", length="8", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="8")
     * @Groups({"user:get:private"})
     * @Gedmo\Versioned()
     */
    private ?string $lastLoginProvider = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"user:patch:about_me"})
     * @Assert\Url(message="generic.url", groups={"user:patch:about_me"})
     * @Groups({"user:get:private", "user:patch:about_me", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private ?string $profileWebsite = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"user:patch:about_me"})
     * @Assert\Url(message="generic.url", groups={"user:patch:about_me"})
     * @Groups({"user:get:private", "user:patch:about_me", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private ?string $profileLinkedInProfile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"user:patch:about_me"})
     * @Assert\Url(message="generic.url", groups={"user:patch:about_me"})
     * @Groups({"user:get:private", "user:patch:about_me", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private ?string $profileProjectWebsite = null;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @EnumAssert(message="generic.enum.message", class=FreelanceLegalStatus::class, groups={"user:patch:job_search_preferences:free"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Assert\NotNull(message="generic.not_blank", groups={"user:patch:job_search_preferences:free"})
     * @Gedmo\Versioned()
     */
    private ?string $freelanceLegalStatus = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @EnumAssert(message="generic.enum.message", class=EmploymentTime::class, groups={"user:patch:job_search_preferences"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private ?string $employmentTime = EmploymentTime::FULL_TIME;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @EnumAssert(message="generic.enum.message", class=UserProfileStep::class)
     * @Groups({
     *     "user:get:private",
     *     "user:patch:personal_info",
     *     "user:put:skills_and_languages",
     *     "user:patch:job_search_preferences",
     *     "user:patch:education",
     *     "user:patch:about_me"
     *
     * })
     */
    private ?string $formStep = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="5", groups={"user:patch:job_search_preferences:free", "user:turnover_write"})
     * @EnumAssert(message="generic.enum.message", class=Currency::class, groups={"user:patch:job_search_preferences:free", "user:turnover_write"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     * @Gedmo\Versioned()
     */
    private ?string $freelanceCurrency = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="5", groups={"user:patch:job_search_preferences:worker"})
     * @EnumAssert(message="generic.enum.message", class=Currency::class, groups={"user:patch:job_search_preferences:worker"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private ?string $employeeCurrency = null;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     * @EnumAssert(message="generic.enum.message", class=CompanyCountryCode::class, groups={"user:patch:job_search_preferences"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private ?string $companyCountryCode = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @EnumAssert(message="generic.enum.message", class=Contract::class, multiple=true, multipleMessage="generic.enum.multiple", groups={"user:patch:job_search_preferences", "user:turnover_write"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy", "user:turnover_write", "user:turnover_get", "user:get_turnover:collection"})
     * @Gedmo\Versioned()
     */
    private ?array $contracts = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\IpTraceable(on="create")
     * @Gedmo\Versioned()
     */
    private ?string $ip;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=600, groups={"user:patch:about_me"})
     * @Assert\Length(maxMessage="generic.length.max", max=200, groups={"user:turnover_write"})
     * @Groups({"user:get:private", "user:patch:about_me", "user:legacy", "user:turnover_write", "user:turnover_get"})
     * @Gedmo\Versioned()
     */
    private ?string $introduceYourself = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max="300", groups={"user:patch:forum_preferences"})
     * @AppAssert\ForumSignature(maxLines=5, groups={"user:patch:forum_preferences"})
     * @Groups({"user:get", "user:patch:forum_preferences", "forum_post:get", "forum_topic:get", "forum_category:get"})
     * @Gedmo\Versioned()
     */
    private ?string $signature;

    /**
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatar")
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
     *     mimeTypes={"image/jpeg","image/png","image/gif", "image/jpg"}
     * )
     */
    private ?File $avatarFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"user:get", "forum_post:get", "forum_topic:get", "forum_category:get", "blog_comment:get", "feed:get"})
     * @ApiThumbnailUrls({
     *     { "name"="xSmall", "filter"="user_avatar_x_small" },
     *     { "name"="small", "filter"="user_avatar_small" },
     *     { "name"="medium", "filter"="user_avatar_medium" },
     * })
     * @Gedmo\Versioned()
     */
    private ?string $avatar = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get", "user:patch:forum_preferences", "forum_post:get", "forum_topic:get", "forum_category:get", "blog_comment:get", "feed:get"})
     * @Gedmo\Versioned()
     */
    private bool $displayAvatar = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private"})
     * @Gedmo\Versioned()
     */
    private bool $enabled = false;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned()
     */
    private bool $locked = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:personal_info", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private bool $drivingLicense = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private bool $employee = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private bool $freelance = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private bool $fulltimeTeleworking = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private bool $companyRegistrationNumberBeingAttributed = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private bool $profileCompleted = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"user:get:private", "user:patch:personal_info", "user:legacy", "user:get_turnover:collection"})
     * @Gedmo\Versioned()
     */
    private ?bool $anonymous = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private"})
     * @Gedmo\Versioned()
     */
    public bool $banned = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:post", "user:patch:terms_of_service"})
     * @Assert\IsTrue(message="user.terms_of_service.is_true", groups={"user:post", "user:patch:terms_of_service"})
     * @Gedmo\Versioned()
     */
    public bool $termsOfService = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTime $termsOfServiceAcceptedAt = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(notInRangeMessage="user.gross_annual_salary.not_in_range_message", min=7000, max=10000000, groups={"user:patch:job_search_preferences:worker", "user:turnover_write"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy", "user:turnover_write", "user:turnover_get"})
     * @Assert\Positive(message="generic.positive", groups={"user:patch:job_search_preferences:worker", "user:turnover_write"})
     * @Assert\NotNull(message="generic.not_blank", groups={"user:patch:job_search_preferences:worker", "user:turnover_write"})
     * @Gedmo\Versioned()
     */
    private ?int $grossAnnualSalary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(maxMessage="generic.range.max", max=100000, groups={"user:patch:job_search_preferences:free", "user:turnover_write"})
     * @Assert\Positive(message="generic.positive", groups={"user:patch:job_search_preferences:free", "user:turnover_write"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     * @Assert\NotNull(message="generic.not_blank", groups={"user:patch:job_search_preferences:free", "user:turnover_write"})
     * @Gedmo\Versioned()
     */
    private ?int $averageDailyRate = null;

    /**
     * TODO: delete after FI migration.
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $origin = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private ?string $companyRegistrationNumber = null;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:get", "forum_post:get", "forum_topic:get", "forum_category:get"})
     */
    private int $forumPostUpvotesCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:get", "forum_post:get", "forum_topic:get", "forum_category:get"})
     */
    private int $forumPostsCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private int $unreadMessagesCount = 0;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"user:get:private", "user:patch:personal_info", "user:legacy", "user:get:candidates"})
     * @Assert\Type("\DateTime", groups={"user:patch:personal_info"})
     * @Gedmo\Versioned()
     */
    private ?\DateTime $birthdate = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $passwordRequestedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:get:private"})
     */
    private ?\DateTime $emailRequestedAt;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:get", "forum_post:get", "forum_topic:get", "forum_category:get", "user:get:candidates", "user:get_turnover:collection"})
     */
    protected ?\DateTime $createdAt;

    /**
     * @Gedmo\Timestampable(on="change", field={"firstName", "lastName", "gender", "phone", "password", "email", "marketingNewsletter", "profileJobTitle", "experienceYear", "visible", "availability", "profileWebsite", "profileLinkedInProfile", "profileProjectWebsite", "freelanceLegalStatus", "employmentTime", "freelanceCurrency", "employeeCurrency", "companyCountryCode", "introduceYourself", "drivingLicense", "employee", "freelance", "fulltimeTeleworking", "companyRegistrationNumberBeingAttributed", "anonymous", "grossAnnualSalary", "averageDailyRate", "companyRegistrationNumber", "birthdate", "documents", "locations", "formation", "skills", "languages", "jobs", "softSkill", "umbrellaCompany", "profileCompleted"})
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:get:private", "user:get:candidates", "user:get_turnover:collection"})
     */
    protected ?\DateTime $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:legacy", "forum_topic:get"})
     */
    protected ?\DateTime $deletedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $passwordUpdatedAt = null;

    /**
     * @ORM\Embedded(class="App\Core\Entity\Location")
     * @Groups({"user:get:private", "user:patch:personal_info", "user:legacy", "user:get:candidates", "user:get_turnover:collection"})
     * @CoreAssert\LocationNotNull(message="user.location.not_null", groups={"user:patch:personal_info"})
     */
    private ?Location $location;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private ?User $deletedBy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private ?User $lockedBy;

    /**
     * @ORM\OneToMany(targetEntity=ForumPost::class, mappedBy="author", cascade={"persist", "remove"})
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $forumPosts;

    /**
     * @ORM\OneToMany(targetEntity=BlogComment::class, mappedBy="author", cascade={"persist", "remove"})
     */
    private Collection $blogComments;

    /**
     * @ORM\OneToMany(targetEntity=UserDocument::class, mappedBy="user", cascade={"persist", "remove"})
     * @Groups({"user:get:private", "user:legacy", "user:turnover_write"})
     * @Assert\Valid(groups={"user:turnover_write"})
     * @Assert\Count(min=1, max=1, minMessage="generic.count.max", groups={"user:turnover_write"})
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $documents;

    /**
     * @ORM\OneToMany(targetEntity=UserMobility::class, mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     * @Assert\Count(minMessage="user.jobs_and_mobilities.count", min="1", groups={"user:patch:job_search_preferences", "user:turnover_write"})
     */
    private Collection $locations;

    /**
     * @ORM\OneToMany(targetEntity=JobPostingSearch::class, mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"user:get:private"})
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $jobPostingSearches;

    /**
     * @ORM\OneToOne(targetEntity=UserFormation::class, cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @Assert\Valid(groups={"user:patch:education", "user:turnover_write"})
     * @Assert\NotNull(message="generic.not_null", groups={"user:patch:education", "user:turnover_write"})
     * @Groups({"user:get:private", "user:patch:education", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     * @Gedmo\Versioned()
     */
    private ?UserFormation $formation = null;

    /**
     * @ORM\OneToOne(targetEntity=UserNotification::class, cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @Assert\Valid(groups={"user:patch:notification"})
     * @Assert\NotNull(message="generic.not_null", groups={"user:patch:notification"})
     * @Groups({"user:get:private", "user:post", "user:patch:notifications"})
     * @Gedmo\Versioned()
     */
    private ?UserNotification $notification;

    /**
     * @ORM\OneToMany(targetEntity=UserSkill::class, mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid(groups={"user:put:skills_and_languages"})
     * @Assert\Count(min=3, minMessage="generic.count.min", groups={"user:put:skills_and_languages"})
     * @Assert\Count(min=1, minMessage="generic.count.min", groups={"user:turnover_write"})
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get", "user:get_turnover:collection"})
     * @AppAssert\UserSkills(
     *     max = 5,
     *     groups={"user:put:skills_and_languages"}
     * )
     */
    private Collection $skills;

    /**
     * @ORM\OneToMany(targetEntity=UserLanguage::class, mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid(groups={"user:put:skills_and_languages"})
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     */
    private Collection $languages;

    /**
     * @ORM\OneToMany(targetEntity=UserJob::class, cascade={"persist", "remove"}, mappedBy="user", orphanRemoval=true)
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get", "user:get_turnover:collection"})
     * @Assert\Valid(groups={"user:patch:job_search_preferences"})
     * @Assert\Count(minMessage="user.jobs_and_mobilities.count", min = "1", groups={"user:patch:job_search_preferences", "user:turnover_write"})
     * @AppAssert\UserJobs(
     *     max = 3,
     *     groups={"user:patch:job_search_preferences"}
     * )
     */
    private Collection $jobs;

    /**
     * @ORM\ManyToMany(targetEntity=SoftSkill::class)
     * @Assert\Valid(groups={"user:put:skills_and_languages"})
     * @Assert\Count(min=1, minMessage="generic.count.min", groups={"user:turnover_write"})
     * @Groups({"user:get:private", "user:put:skills_and_languages", "user:legacy", "user:get:candidates", "user:turnover_write", "user:turnover_get"})
     */
    private Collection $softSkills;

    /**
     * @ORM\ManyToOne(targetEntity=UmbrellaCompany::class, cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Valid(groups={"user:patch:job_search_preferences"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences", "user:legacy"})
     * @Gedmo\Versioned()
     */
    private ?UmbrellaCompany $umbrellaCompany = null;

    /**
     * @ORM\OneToMany(targetEntity=Application::class, mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ApiSubresource(maxDepth=1)
     */
    private Collection $applications;

    /**
     * @ORM\OneToMany(targetEntity=UserProvider::class, mappedBy="user", cascade={"persist", "remove"})
     * @Groups({"user:get:private"})
     */
    private Collection $providers;

    /**
     * @ORM\OneToMany(targetEntity=FeedUser::class, mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ApiSubresource(maxDepth=1)
     * @Groups({"user:get:private"})
     */
    private Collection $feedUsers;

    /**
     * @ORM\OneToMany(targetEntity=CompanyBlacklist::class, mappedBy="user", cascade={"persist", "remove"})
     * @Groups({"user:legacy", "user:patch:personal_info", "user:get"})
     */
    private Collection $blacklistedCompanies;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:get:private"})
     */
    private int $activeJobPostingSearchesCount = 0;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"user:legacy"})
     */
    private ?array $oldFreelanceInfoIds = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"user:legacy"})
     */
    private ?array $oldFreelanceInfoProfileIds = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"user:legacy"})
     */
    private ?array $oldCarriereInfoIds = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"user:legacy"})
     */
    private ?array $oldCarriereInfoProfileIds = null;

    /**
     * @ORM\OneToOne(targetEntity=UserData::class, cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private ?UserData $data;

    /**
     * @ORM\ManyToOne(targetEntity=Partner::class, cascade={"persist"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Groups({"user:get:private", "user:patch:job_search_preferences"})
     * @Gedmo\Versioned()
     */
    private ?Partner $partner = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"user:legacy", "user:turnover_write", "user:turnover_get"})
     */
    private ?string $reference = null;

    /**
     * @ORM\Column(type="text", nullable=true, length=200)
     * @Groups({"user:legacy", "user:turnover_write", "user:turnover_get"})
     */
    private ?string $contact = null;

    /**
     * @ORM\ManyToOne(targetEntity=Recruiter::class)
     * @Gedmo\Blameable(on="create")
     * @Groups({"user:legacy", "user:turnover_write", "user:turnover_get"})
     */
    private ?Recruiter $createdBy = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $viewsCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private int $applicationsCount = 0;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:get:private", "user:patch:job_search_preferences"})
     * @Gedmo\Versioned()
     */
    private bool $insurance = false;

    /**
     * @ORM\ManyToOne(targetEntity=InsuranceCompany::class, cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull(message="user.insurance.company", groups={"user:patch:job_search_preferences:insurance"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences"})
     * @Gedmo\Versioned()
     */
    private ?InsuranceCompany $insuranceCompany = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull(message="user.insurance.number", groups={"user:patch:job_search_preferences:insurance"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences"})
     * @Gedmo\Versioned()
     */
    private ?string $insuranceNumber = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotNull(message="user.insurance.expiration", groups={"user:patch:job_search_preferences:insurance"})
     * @Groups({"user:get:private", "user:patch:job_search_preferences"})
     */
    private ?\DateTime $insuranceExpiredAt = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Length(maxMessage="generic.length.max", max=5)
     * @Groups({"user:get:private", "user:patch:job_search_preferences"})
     * @EnumAssert(message="generic.enum.message", class=NafCode::class, groups={"user:patch:job_search_preferences:insurance"})
     */
    private ?string $nafCode = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->forumPosts = new ArrayCollection();
        $this->blogComments = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->softSkills = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->jobPostingSearches = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->feedUsers = new ArrayCollection();
        $this->blacklistedCompanies = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->location = new Location();
        $this->notification = new UserNotification();
        $this->data = new UserData();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        if ('ROLE_USER' !== $role && false === $this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->roles, true);
    }

    /**
     * @Groups({"user:get:private"})
     */
    public function getHasPassword(): bool
    {
        return null !== $this->password;
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

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    public function setPhone(?PhoneNumber $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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

    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

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

    public function isEmailRequestActive(int $ttl): bool
    {
        return $this->getEmailRequestedAt() instanceof \DateTime &&
            $this->getEmailRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getNicknameSlug(): ?string
    {
        return $this->nicknameSlug;
    }

    public function setNicknameSlug(?string $nicknameSlug): self
    {
        $this->nicknameSlug = $nicknameSlug;

        return $this;
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

    public function getForumPostUpvotesCount(): ?int
    {
        return $this->forumPostUpvotesCount;
    }

    public function setForumPostUpvotesCount(int $forumPostUpvotesCount): self
    {
        $this->forumPostUpvotesCount = $forumPostUpvotesCount;

        return $this;
    }

    public function getForumPostsCount(): ?int
    {
        return $this->forumPostsCount;
    }

    public function setForumPostsCount(int $forumPostsCount): self
    {
        $this->forumPostsCount = $forumPostsCount;

        return $this;
    }

    /**
     * @return Collection|ForumPost[]
     */
    public function getForumPosts(): Collection
    {
        return $this->forumPosts;
    }

    public function addForumPost(ForumPost $forumPost): self
    {
        if (!$this->forumPosts->contains($forumPost)) {
            $this->forumPosts[] = $forumPost;
            $forumPost->setAuthor($this);
        }

        return $this;
    }

    public function removeForumPost(ForumPost $forumPost): self
    {
        // set the owning side to null (unless already changed)
        if ($this->forumPosts->removeElement($forumPost) && $forumPost->getAuthor() === $this) {
            $forumPost->setAuthor(null);
        }

        return $this;
    }

    /**
     * @return Collection|BlogComment[]
     */
    public function getBlogComments(): Collection
    {
        return $this->blogComments;
    }

    public function addBlogComment(BlogComment $blogComment): self
    {
        if (!$this->blogComments->contains($blogComment)) {
            $this->blogComments[] = $blogComment;
            $blogComment->setAuthor($this);
        }

        return $this;
    }

    public function removeBlogComment(BlogComment $blogComment): self
    {
        // set the owning side to null (unless already changed)
        if ($this->blogComments->removeElement($blogComment) && $blogComment->getAuthor() === $this) {
            $blogComment->setAuthor(null);
        }

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarFile(?File $avatarFile): self
    {
        $this->avatarFile = $avatarFile;

        if ($avatarFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getDisplayAvatar(): ?bool
    {
        return $this->displayAvatar;
    }

    public function setDisplayAvatar(bool $displayAvatar): self
    {
        $this->displayAvatar = $displayAvatar;

        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return Collection|UserDocument[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(UserDocument $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setUser($this);
        }

        return $this;
    }

    public function removeDocument(UserDocument $document): self
    {
        // set the owning side to null (unless already changed)
        if ($this->documents->removeElement($document) && $document->getUser() === $this) {
            $document->setUser(null);
        }

        return $this;
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

    public function getExperienceYear(): ?string
    {
        return $this->experienceYear;
    }

    public function setExperienceYear(?string $experienceYear): self
    {
        $this->experienceYear = $experienceYear;

        return $this;
    }

    public function getBirthdate(): ?\DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTime $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getDrivingLicense(): ?bool
    {
        return $this->drivingLicense;
    }

    public function setDrivingLicense(bool $drivingLicense): self
    {
        $this->drivingLicense = $drivingLicense;

        return $this;
    }

    public function getIntroduceYourself(): ?string
    {
        return $this->introduceYourself;
    }

    public function setIntroduceYourself(?string $introduceYourself): self
    {
        $this->introduceYourself = $introduceYourself;

        return $this;
    }

    public function getProfileJobTitle(): ?string
    {
        return $this->profileJobTitle;
    }

    public function setProfileJobTitle(?string $profileJobTitle): self
    {
        $this->profileJobTitle = $profileJobTitle;

        return $this;
    }

    public function getProfileWebsite(): ?string
    {
        return $this->profileWebsite;
    }

    public function setProfileWebsite(?string $profileWebsite): self
    {
        $this->profileWebsite = $profileWebsite;

        return $this;
    }

    public function getProfileLinkedInProfile(): ?string
    {
        return $this->profileLinkedInProfile;
    }

    public function setProfileLinkedInProfile(?string $profileLinkedInProfile): self
    {
        $this->profileLinkedInProfile = $profileLinkedInProfile;

        return $this;
    }

    public function getProfileProjectWebsite(): ?string
    {
        return $this->profileProjectWebsite;
    }

    public function setProfileProjectWebsite(?string $profileProjectWebsite): self
    {
        $this->profileProjectWebsite = $profileProjectWebsite;

        return $this;
    }

    public function getFormation(): ?UserFormation
    {
        return $this->formation;
    }

    public function setFormation(?UserFormation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * @return Collection|UserSkill[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(UserSkill $skill): self
    {
        $exists = $this->skills->exists(static function (int $key, UserSkill $element) use ($skill) {
            return $skill->getSkill() === $element->getSkill();
        });

        if (!$exists) {
            $this->skills[] = $skill;
            $skill->setUser($this);
        }

        return $this;
    }

    public function removeSkill(UserSkill $skill): self
    {
        // set the owning side to null (unless already changed)
        if ($this->skills->removeElement($skill) && $skill->getUser() === $this) {
            $skill->setUser(null);
        }

        return $this;
    }

    /**
     * @return Collection|UserLanguage[]
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(UserLanguage $language): self
    {
        $exists = $this->languages->exists(static function (int $key, UserLanguage $element) use ($language) {
            return $language->getLanguage() === $element->getLanguage();
        });

        if (!$exists) {
            $this->languages[] = $language;
            $language->setUser($this);
        }

        return $this;
    }

    public function removeLanguage(UserLanguage $language): self
    {
        // set the owning side to null (unless already changed)
        if ($this->languages->removeElement($language) && $language->getUser() === $this) {
            $language->setUser(null);
        }

        return $this;
    }

    public function getFreelanceLegalStatus(): ?string
    {
        return $this->freelanceLegalStatus;
    }

    public function setFreelanceLegalStatus(?string $freelanceLegalStatus): self
    {
        $this->freelanceLegalStatus = $freelanceLegalStatus;

        return $this;
    }

    public function getEmploymentTime(): ?string
    {
        return $this->employmentTime;
    }

    public function setEmploymentTime(?string $employmentTime): self
    {
        $this->employmentTime = $employmentTime;

        return $this;
    }

    public function getEmployee(): bool
    {
        return $this->employee;
    }

    public function setEmployee(bool $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getFreelance(): bool
    {
        return $this->freelance;
    }

    public function setFreelance(bool $freelance): self
    {
        $this->freelance = $freelance;

        return $this;
    }

    public function getFulltimeTeleworking(): ?bool
    {
        return $this->fulltimeTeleworking;
    }

    public function setFulltimeTeleworking(bool $fulltimeTeleworking): self
    {
        $this->fulltimeTeleworking = $fulltimeTeleworking;

        return $this;
    }

    public function getCompanyRegistrationNumberBeingAttributed(): bool
    {
        return $this->companyRegistrationNumberBeingAttributed;
    }

    public function setCompanyRegistrationNumberBeingAttributed(bool $companyRegistrationNumberBeingAttributed): self
    {
        $this->companyRegistrationNumberBeingAttributed = $companyRegistrationNumberBeingAttributed;

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(UserJob $job): self
    {
        $exists = $this->jobs->exists(static function (int $key, UserJob $element) use ($job) {
            return $job->getJob() === $element->getJob();
        });

        if (!$exists) {
            $this->jobs[] = $job;
            $job->setUser($this);
        }

        return $this;
    }

    public function removeJob(UserJob $job): self
    {
        // set the owning side to null (unless already changed)
        if ($this->jobs->removeElement($job) && $job->getUser() === $this) {
            $job->setUser(null);
        }

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

    public function getUmbrellaCompany(): ?UmbrellaCompany
    {
        return $this->umbrellaCompany;
    }

    public function setUmbrellaCompany(?UmbrellaCompany $umbrellaCompany): self
    {
        $this->umbrellaCompany = $umbrellaCompany;

        return $this;
    }

    public function getFreelanceCurrency(): ?string
    {
        return $this->freelanceCurrency;
    }

    public function setFreelanceCurrency(?string $freelanceCurrency): self
    {
        $this->freelanceCurrency = $freelanceCurrency;

        return $this;
    }

    public function getEmployeeCurrency(): ?string
    {
        return $this->employeeCurrency;
    }

    public function setEmployeeCurrency(?string $employeeCurrency): self
    {
        $this->employeeCurrency = $employeeCurrency;

        return $this;
    }

    public function getGrossAnnualSalary(): ?int
    {
        return $this->grossAnnualSalary;
    }

    public function setGrossAnnualSalary(?int $grossAnnualSalary): self
    {
        $this->grossAnnualSalary = $grossAnnualSalary;

        return $this;
    }

    public function getAverageDailyRate(): ?int
    {
        return $this->averageDailyRate;
    }

    public function setAverageDailyRate(?int $averageDailyRate): self
    {
        $this->averageDailyRate = $averageDailyRate;

        return $this;
    }

    public function getCompanyRegistrationNumber(): ?string
    {
        return $this->companyRegistrationNumber;
    }

    public function setCompanyRegistrationNumber(?string $companyRegistrationNumber): self
    {
        $this->companyRegistrationNumber = $companyRegistrationNumber;

        return $this;
    }

    public function getCompanyCountryCode(): ?string
    {
        return $this->companyCountryCode;
    }

    public function setCompanyCountryCode(?string $companyCountryCode): self
    {
        $this->companyCountryCode = $companyCountryCode;

        return $this;
    }

    /**
     * @return Collection|JobPostingSearch[]
     */
    public function getJobPostingSearches(): Collection
    {
        return $this->jobPostingSearches;
    }

    public function addJobPostingSearch(JobPostingSearch $mobility): self
    {
        if (!$this->jobPostingSearches->contains($mobility)) {
            $this->jobPostingSearches[] = $mobility;
            $mobility->setUser($this);
        }

        return $this;
    }

    public function removeJobPostingSearch(JobPostingSearch $jobPostingSearch): self
    {
        $this->jobPostingSearches->removeElement($jobPostingSearch);

        return $this;
    }

    /**
     * @Groups({"user:turnover_get"})
     */
    public function getDefaultResumeDocuments(): Collection
    {
        return $this->getDocuments()->filter(static function (UserDocument $userDocument) {
            return true === $userDocument->getDefaultResume();
        });
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setUser($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        // set the owning side to null (unless already changed)
        if ($this->applications->removeElement($application) && $application->getUser() === $this) {
            $application->setUser(null);
        }

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @Groups({"user:get", "forum_post:get", "forum_topic:get", "forum_category:get", "blog_comment:get", "feed:get"})
     */
    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
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

    /**
     * @return Collection|UserMobility[]
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(UserMobility $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setUser($this);
        }

        return $this;
    }

    public function removeLocation(UserMobility $location): self
    {
        // set the owning side to null (unless already changed)
        if ($this->locations->removeElement($location) && $location->getUser() === $this) {
            $location->setUser(null);
        }

        return $this;
    }

    public function getFormStep(): ?string
    {
        return $this->formStep;
    }

    public function setFormStep(?string $formStep): self
    {
        $this->formStep = $formStep;

        return $this;
    }

    public function getProfileCompleted(): ?bool
    {
        return $this->profileCompleted;
    }

    public function setProfileCompleted(bool $profileCompleted): self
    {
        $this->profileCompleted = $profileCompleted;

        return $this;
    }

    public function getActiveJobPostingSearchesCount(): ?int
    {
        return $this->activeJobPostingSearchesCount;
    }

    public function setActiveJobPostingSearchesCount(int $activeJobPostingSearchesCount): self
    {
        $this->activeJobPostingSearchesCount = $activeJobPostingSearchesCount;

        return $this;
    }

    /**
     * @return Collection|UserProvider[]
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(UserProvider $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers[] = $provider;
            $provider->setUser($this);
        }

        return $this;
    }

    public function removeProvider(UserProvider $provider): self
    {
        // set the owning side to null (unless already changed)
        if ($this->providers->removeElement($provider) && $provider->getUser() === $this) {
            $provider->setUser(null);
        }

        return $this;
    }

    /**
     * @Groups({"user:get:private", "user:get_turnover:collection"})
     */
    public function getFormattedGrossAnnualSalary(): ?string
    {
        return Numbers::formatCurrency($this->grossAnnualSalary, $this->employeeCurrency ?? 'EUR');
    }

    /**
     * @Groups({"user:get:private", "user:get_turnover:collection"})
     */
    public function getFormattedAverageDailyRate(): ?string
    {
        return Numbers::formatCurrency($this->averageDailyRate, $this->freelanceCurrency ?? 'EUR');
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    public function setAvailability(?string $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getNextAvailabilityAt(): ?\DateTime
    {
        return $this->nextAvailabilityAt;
    }

    public function setNextAvailabilityAt(?\DateTime $nextAvailabilityAt): self
    {
        $this->nextAvailabilityAt = $nextAvailabilityAt;

        return $this;
    }

    public function getStatusUpdatedAt(): ?\DateTime
    {
        return $this->statusUpdatedAt;
    }

    public function setStatusUpdatedAt(?\DateTime $statusUpdatedAt): self
    {
        $this->statusUpdatedAt = $statusUpdatedAt;

        return $this;
    }

    public function getAnonymous(): ?bool
    {
        return $this->anonymous;
    }

    public function setAnonymous(?bool $anonymous): self
    {
        $this->anonymous = $anonymous;

        return $this;
    }

    public function getUnreadMessagesCount(): int
    {
        return $this->unreadMessagesCount;
    }

    public function setUnreadMessagesCount(int $unreadMessagesCount): self
    {
        $this->unreadMessagesCount = $unreadMessagesCount;

        return $this;
    }

    public function getOldFreelanceInfoIds(): ?array
    {
        return $this->oldFreelanceInfoIds;
    }

    public function setOldFreelanceInfoIds(?array $oldFreelanceInfoIds): self
    {
        $this->oldFreelanceInfoIds = $oldFreelanceInfoIds;

        return $this;
    }

    public function getOldFreelanceInfoProfileIds(): ?array
    {
        return $this->oldFreelanceInfoProfileIds;
    }

    public function setOldFreelanceInfoProfileIds(?array $oldFreelanceInfoProfileIds): self
    {
        $this->oldFreelanceInfoProfileIds = $oldFreelanceInfoProfileIds;

        return $this;
    }

    public function getOldCarriereInfoIds(): ?array
    {
        return $this->oldCarriereInfoIds;
    }

    public function setOldCarriereInfoIds(?array $oldCarriereInfoIds): self
    {
        $this->oldCarriereInfoIds = $oldCarriereInfoIds;

        return $this;
    }

    public function getOldCarriereInfoProfileIds(): ?array
    {
        return $this->oldCarriereInfoProfileIds;
    }

    public function setOldCarriereInfoProfileIds(?array $oldCarriereInfoProfileIds): self
    {
        $this->oldCarriereInfoProfileIds = $oldCarriereInfoProfileIds;

        return $this;
    }

    public function getBanned(): ?bool
    {
        return $this->banned;
    }

    public function setBanned(bool $banned): self
    {
        $this->banned = $banned;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

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

    public function getDeletedBy(): ?self
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?self $deletedBy): self
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    public function getLockedBy(): ?self
    {
        return $this->lockedBy;
    }

    public function setLockedBy(?self $lockedBy): self
    {
        $this->lockedBy = $lockedBy;

        return $this;
    }

    /**
     * @return Collection|FeedUser[]
     */
    public function getFeedUsers(): Collection
    {
        return $this->feedUsers;
    }

    public function addFeedUser(FeedUser $feedUser): self
    {
        if (!$this->feedUsers->contains($feedUser)) {
            $this->feedUsers[] = $feedUser;
            $feedUser->setUser($this);
        }

        return $this;
    }

    public function removeFeedUser(FeedUser $feedUser): self
    {
        // set the owning side to null (unless already changed)
        if ($this->feedUsers->removeElement($feedUser) && $feedUser->getUser() === $this) {
            $feedUser->setUser(null);
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @Groups({"user:legacy"})
     */
    public function getCreatedAtTimestamp(): ?int
    {
        return $this->createdAt?->getTimestamp();
    }

    /**
     * @Groups({"user:legacy"})
     */
    public function getUpdatedAtTimestamp(): ?int
    {
        return $this->updatedAt?->getTimestamp();
    }

    /**
     * @Groups({"user:legacy"})
     */
    public function getDeletedAtTimestamp(): ?int
    {
        return $this->deletedAt?->getTimestamp();
    }

    public function getNotification(): ?UserNotification
    {
        return $this->notification;
    }

    public function setNotification(?UserNotification $notification): self
    {
        $this->notification = $notification;

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

    /**
     * @return Collection|CompanyBlacklist[]
     */
    public function getBlacklistedCompanies(): Collection
    {
        return $this->blacklistedCompanies;
    }

    public function addBlacklistedCompany(CompanyBlacklist $blacklistedCompany): self
    {
        if (!$this->blacklistedCompanies->contains($blacklistedCompany)) {
            $this->blacklistedCompanies[] = $blacklistedCompany;
            $blacklistedCompany->setUser($this);
        }

        return $this;
    }

    public function removeBlacklistedCompany(CompanyBlacklist $blacklistedCompany): self
    {
        // set the owning side to null (unless already changed)
        if ($this->blacklistedCompanies->removeElement($blacklistedCompany) && $blacklistedCompany->getUser() === $this) {
            $blacklistedCompany->setUser(null);
        }

        return $this;
    }

    /**
     * @Groups({"user:get", "forum_post:get", "forum_topic:get", "forum_category:get"})
     */
    public function getForumRank(): int
    {
        if ($this->forumPostsCount >= 1000) {
            return 5;
        }

        if ($this->forumPostsCount >= 500) {
            return 4;
        }

        if ($this->forumPostsCount >= 100) {
            return 3;
        }

        if ($this->forumPostsCount >= 50) {
            return 2;
        }

        if ($this->forumPostsCount >= 10) {
            return 1;
        }

        return 0;
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

    public function __toString(): string
    {
        return $this->nickname . ' (' . $this->email . ')';
    }

    public function getLastLoginAt(): ?\DateTime
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTime $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getLastLoginProvider(): ?string
    {
        return $this->lastLoginProvider;
    }

    public function setLastLoginProvider(?string $lastLoginProvider): self
    {
        $this->lastLoginProvider = $lastLoginProvider;

        return $this;
    }

    public function getData(): ?UserData
    {
        return $this->data;
    }

    public function setData(?UserData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getOrigin(): ?int
    {
        return $this->origin;
    }

    public function setOrigin(?int $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
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

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

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

    /**
     * @Groups({"forum_post:get", "forum_post:get:children", "forum_topic:get", "forum_post:get:parent", "forum_post:get:topic"})
     */
    public function isAdmin(): bool
    {
        return \in_array('ROLE_ADMIN', $this->getRoles() ?? [], true);
    }

    public function getViewsCount(): int
    {
        return $this->viewsCount;
    }

    public function setViewsCount(int $viewsCount): self
    {
        $this->viewsCount = $viewsCount;

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

    public function getNafCode(): ?string
    {
        return $this->nafCode;
    }

    public function setNafCode(?string $nafCode): self
    {
        $this->nafCode = $nafCode;

        return $this;
    }

    public function getInsuranceCompany(): ?InsuranceCompany
    {
        return $this->insuranceCompany;
    }

    public function setInsuranceCompany(?InsuranceCompany $insuranceCompany): self
    {
        $this->insuranceCompany = $insuranceCompany;

        return $this;
    }

    public function getInsuranceNumber(): ?string
    {
        return $this->insuranceNumber;
    }

    public function setInsuranceNumber(?string $insuranceNumber): self
    {
        $this->insuranceNumber = $insuranceNumber;

        return $this;
    }

    public function getInsuranceExpiredAt(): ?\DateTime
    {
        return $this->insuranceExpiredAt;
    }

    public function setInsuranceExpiredAt(?\DateTime $insuranceExpiredAt): self
    {
        $this->insuranceExpiredAt = $insuranceExpiredAt;

        return $this;
    }

    public function getInsurance(): bool
    {
        return $this->insurance;
    }

    public function setInsurance(bool $insurance): self
    {
        $this->insurance = $insurance;

        return $this;
    }
}
