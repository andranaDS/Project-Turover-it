<?php

namespace App\Core\Traits;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Core\Enum\Locale;
use Doctrine\ORM\Mapping as ORM;
use Greg0ire\Enum\Bridge\Symfony\Validator\Constraint\Enum as EnumAssert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 */
trait LocaleableTrait
{
    /**
     * @ORM\Column(type="json", nullable=true)
     * @EnumAssert(message="generic.enum.message", Locale::class, multiple=true, multipleMessage="generic.enum.multiple")
     * @Groups({"blog_post:get:item", "blog_category:get:item"})
     */
    private ?array $locales = null;

    public function getLocales(): ?array
    {
        return $this->locales;
    }

    public function setLocales(?array $locales): self
    {
        $this->locales = $locales;

        return $this;
    }

    public function addLocale(?string $locale): self
    {
        if (!\is_array($this->locales)) {
            $this->locales = [];
        }

        if (!\in_array($locale, $this->locales, true)) {
            $this->locales[] = $locale;
        }

        return $this;
    }
}
