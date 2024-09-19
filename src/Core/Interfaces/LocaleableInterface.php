<?php

namespace App\Core\Interfaces;

interface LocaleableInterface
{
    public function getLocales(): ?array;

    public function setLocales(?array $locales): self;

    public function addLocale(?string $locale): self;
}
