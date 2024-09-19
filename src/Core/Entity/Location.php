<?php

namespace App\Core\Entity;

use App\User\Enum\CompanyCountryCode;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable()
 */
class Location
{
    public const LOCATION_KEY_SEPARATOR = '~';

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "location",
     *     "company:get",
     *     "user:get:private",
     *     "user:put",
     *     "application:get",
     *     "job_posting:get",
     *     "job_posting:write",
     *     "job_posting_search:get",
     *     "job_posting_search:post",
     *     "job_posting_search:put",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "job_posting_template:get",
     *     "job_posting_template:write",
     *     "user:get:candidates",
     *     "company:patch:account",
     *     "company:patch:directory",
     *     "recruiter:get",
     *     "job_posting_recruiter_search_filter:get",
     *     "job_posting_recruiter_search_filter:write",
     *     "user:turnover_get",
     *     "user:get_turnover:collection"
     * })
     * @Assert\NotBlank(message="generic.not_blank", groups={"company:patch:account"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"company:patch:account"})
     */
    private ?string $street = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $subLocality = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "location",
     *     "company:get",
     *     "user:get:private",
     *     "user:put",
     *     "application:get",
     *     "job_posting:get",
     *     "job_posting:write",
     *     "job_posting_search:get",
     *     "job_posting_search:post",
     *     "job_posting_search:put",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "job_posting_template:get",
     *     "job_posting_template:write",
     *     "user:get:candidates",
     *     "company:patch:account",
     *     "company:patch:directory",
     *     "recruiter:get",
     *     "job_posting_recruiter_search_filter:get",
     *     "job_posting_recruiter_search_filter:write",
     *	   "user:turnover_write",
     *     "user:turnover_get"
     * })
     * @Assert\NotBlank(message="generic.not_blank", groups={"company:patch:account"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"company:patch:account"})
     */
    private ?string $locality = null;

    /**
     * @Gedmo\Slug(fields={"locality"}, unique=false)
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $localitySlug = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "location",
     *     "company:get",
     *     "user:get:private",
     *     "user:put",
     *     "application:get",
     *     "job_posting:get",
     *     "job_posting:write",
     *     "job_posting_search:get",
     *     "job_posting_search:post",
     *     "job_posting_search:put",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "job_posting_template:get",
     *     "job_posting_template:write",
     *     "user:get:candidates",
     *     "company:patch:account",
     *     "company:patch:directory",
     *     "recruiter:get",
     *     "job_posting_recruiter_search_filter:get",
     *     "job_posting_recruiter_search_filter:write",
     *     "user:turnover_get",
     *	   "user:turnover_write",
     *     "user:get_turnover:collection"
     * })
     * @Assert\NotBlank(message="generic.not_blank", groups={"company:patch:account"})
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"company:patch:account"})
     */
    private ?string $postalCode = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "location",
     *     "user:get:private",
     *     "user:put",
     *     "application:get",
     *     "job_posting:get",
     *     "job_posting:write",
     *     "job_posting_search:get",
     *     "job_posting_search:post",
     *     "job_posting_search:put",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "job_posting_template:get",
     *     "job_posting_template:write",
     *     "user:get:candidates",
     *     "company:patch:directory",
     *     "job_posting_recruiter_search_filter:get",
     *     "job_posting_recruiter_search_filter:write",
     *     "user:turnover_get",
     *     "user:turnover_write"
     * })
     */
    private ?string $adminLevel1 = null;

    /**
     * @Gedmo\Slug(fields={"adminLevel1"}, unique=false)
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $adminLevel1Slug = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "location",
     *     "user:get:private",
     *     "user:put",
     *     "application:get",
     *     "job_posting:get",
     *     "job_posting:write",
     *     "job_posting_search:get",
     *     "job_posting_search:post",
     *     "job_posting_search:put",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "job_posting_template:get",
     *     "job_posting_template:write",
     *     "user:get:candidates",
     *     "company:patch:directory",
     *     "job_posting_recruiter_search_filter:get",
     *     "job_posting_recruiter_search_filter:write",
     *     "user:turnover_get",
     *     "user:turnover_write"
     * })
     */
    private ?string $adminLevel2 = null;

    /**
     * @Gedmo\Slug(fields={"adminLevel2"}, unique=false)
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $adminLevel2Slug = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "location",
     *     "company:get",
     *     "user:get:private",
     *     "user:put",
     *     "application:get",
     *     "job_posting:get",
     *     "job_posting:write",
     *     "job_posting_search:get",
     *     "job_posting_search:post",
     *     "job_posting_search:put",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "job_posting_template:get",
     *     "job_posting_template:write",
     *     "user:get:candidates",
     *     "company:patch:account",
     *     "recruiter:get",
     *     "company:patch:directory",
     *     "job_posting_recruiter_search_filter:get",
     *     "job_posting_recruiter_search_filter:write",
     *     "user:turnover_get",
     *     "user:turnover_write"
     * })
     */
    private ?string $country = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "location",
     *     "user:get:private",
     *     "user:put",
     *     "application:get",
     *     "job_posting:get",
     *     "job_posting:write",
     *     "job_posting_search:get",
     *     "job_posting_search:post",
     *     "job_posting_search:put",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "job_posting_template:get",
     *     "job_posting_template:write",
     *     "user:get:candidates",
     *     "company:patch:account",
     *     "recruiter:post",
     *     "recruiter:get",
     *     "company:patch:directory",
     *     "job_posting_recruiter_search_filter:get",
     *     "job_posting_recruiter_search_filter:write",
     *     "user:turnover_get",
     *     "user:turnover_write"
     * })
     * @Assert\NotBlank(message="generic.not_blank", groups={"company:patch:account"})
     * @EnumAssert(message="generic.enum.message", class=CompanyCountryCode::class, groups={"company:patch:account", "recruiter:post"})
     */
    private ?string $countryCode = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $value = null;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=7, nullable=true)
     * @Groups({
     *     "location",
     *     "user:get:private",
     *     "user:put",
     *     "application:get",
     *     "job_posting:get",
     *     "job_posting:write",
     *     "job_posting_search:get",
     *     "job_posting_search:post",
     *     "job_posting_search:put",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "job_posting_template:get",
     *     "job_posting_template:write",
     *     "user:get:candidates",
     *     "company:patch:directory",
     *     "job_posting_recruiter_search_filter:get",
     *     "job_posting_recruiter_search_filter:write",
     *     "user:turnover_get",
     *     "user:turnover_write"
     * })
     */
    private ?string $latitude = null;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=7, nullable=true)
     * @Groups({
     *     "location",
     *     "user:get:private",
     *     "user:put",
     *     "application:get",
     *     "job_posting:get",
     *     "job_posting:write",
     *     "job_posting_search:get",
     *     "job_posting_search:post",
     *     "job_posting_search:put",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "job_posting_template:get",
     *     "job_posting_template:write",
     *     "user:get:candidates",
     *     "company:patch:directory",
     *     "job_posting_recruiter_search_filter:get",
     *     "job_posting_recruiter_search_filter:write",
     *     "user:turnover_get",
     *     "user:turnover_write"
     * })
     */
    private ?string $longitude = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "company:patch:account",
     *     "recruiter:get"
     * })
     * @Assert\Length(maxMessage="generic.length.max", max="255", groups={"company:patch:account"})
     */
    private ?string $additionalData = null;

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getSubLocality(): ?string
    {
        return $this->subLocality;
    }

    public function setSubLocality(?string $subLocality): self
    {
        $this->subLocality = $subLocality;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(?string $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getAdminLevel1(): ?string
    {
        return $this->adminLevel1;
    }

    public function setAdminLevel1(?string $adminLevel1): self
    {
        $this->adminLevel1 = $adminLevel1;

        return $this;
    }

    public function getAdminLevel2(): ?string
    {
        return $this->adminLevel2;
    }

    public function setAdminLevel2(?string $adminLevel2): self
    {
        $this->adminLevel2 = $adminLevel2;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @Groups({
     *     "location",
     *     "user:get:private",
     *     "job_posting_search:get",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "user:get:candidates",
     *     "company:patch:account",
     *     "company:patch:directory",
     *     "job_posting_recruiter_search_filter:get"
     * })
     */
    public function getKey(): ?string
    {
        $parts = [
            $this->countryCode,
            $this->adminLevel1Slug,
            $this->adminLevel2Slug,
            $this->localitySlug,
        ];

        return empty(array_filter($parts)) ? null : mb_strtolower(implode(self::LOCATION_KEY_SEPARATOR, $parts), 'utf8');
    }

    public static function explodeKey(string $key): array
    {
        $parts = explode(self::LOCATION_KEY_SEPARATOR, $key);

        return array_filter([
            'countryCode' => isset($parts[0]) ? mb_strtoupper($parts[0], 'utf8') : null,
            'adminLevel1Slug' => $parts[1] ?? null,
            'adminLevel2Slug' => $parts[2] ?? null,
            'localitySlug' => $parts[3] ?? null,
        ]);
    }

    /**
     * @Groups({
     *     "location",
     *     "user:get:private",
     *     "job_posting_search:get",
     *     "user:legacy",
     *     "company:get:homepage",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "user:get:candidates",
     *     "company:patch:account",
     *     "company:patch:directory",
     *     "job_posting_recruiter_search_filter:get"
     * })
     */
    public function getLabel(): ?string
    {
        $parts = [];

        if (!empty($this->locality) && 'FR' === $this->countryCode) {
            // city france
            $parts[] = $this->getLocality();
            $parts[] = $this->getAdminLevel1();
        } elseif (!empty($this->locality)) {
            // city world
            $parts[] = $this->getLocality();
            $parts[] = $this->getAdminLevel1();
            $parts[] = $this->getCountry();
        } elseif (!empty($this->adminLevel2)) {
            // admin level 2
            $parts[] = $this->getAdminLevel2();
            $parts[] = $this->getCountry();
        } elseif (!empty($this->adminLevel1)) {
            // admin level 1
            $parts[] = $this->getAdminLevel1();
            $parts[] = $this->getCountry();
        } else {
            // country
            $parts[] = $this->getCountry();
        }

        $parts = array_filter(array_unique($parts));

        return empty($parts) ? null : implode(', ', $parts);
    }

    /**
     * @Groups({
     *     "location",
     *     "user:get:private",
     *     "job_posting_search:get",
     *     "user:legacy",
     *     "user:patch:personal_info",
     *     "user:patch:job_search_preferences",
     *     "user:get:candidates",
     *     "company:patch:account",
     *     "company:patch:directory",
     *     "job_posting_recruiter_search_filter:get"
     * })
     */
    public function getShortLabel(): ?string
    {
        if (!empty($this->locality)) {
            $parts = array_filter([
                $this->locality,
                (null !== $this->postalCode && 'FR' === $this->countryCode) ? sprintf('(%d)', substr($this->postalCode, 0, 2)) : null,
            ]);

            return implode(' ', array_filter($parts));
        }

        if (null !== $this->adminLevel2) {
            return $this->adminLevel2;
        }

        if (null !== $this->adminLevel1) {
            return $this->adminLevel1;
        }

        if (null !== $this->locality) {
            return $this->locality;
        }

        if (null !== $this->country) {
            return $this->country;
        }

        return null;
    }

    public function getLocalitySlug(): ?string
    {
        return $this->localitySlug;
    }

    public function setLocalitySlug(?string $localitySlug): self
    {
        $this->localitySlug = $localitySlug;

        return $this;
    }

    public function getAdminLevel1Slug(): ?string
    {
        return $this->adminLevel1Slug;
    }

    public function setAdminLevel1Slug(?string $adminLevel1Slug): self
    {
        $this->adminLevel1Slug = $adminLevel1Slug;

        return $this;
    }

    public function getAdminLevel2Slug(): ?string
    {
        return $this->adminLevel2Slug;
    }

    public function setAdminLevel2Slug(?string $adminLevel2Slug): self
    {
        $this->adminLevel2Slug = $adminLevel2Slug;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getLabel() ?: '';
    }

    public function getAdditionalData(): ?string
    {
        return $this->additionalData;
    }

    public function setAdditionalData(?string $additionalData): void
    {
        $this->additionalData = $additionalData;
    }
}
