<?php

namespace App\Admin\Field;

use App\Admin\Form\Type\LocationIqType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class LocationIqField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_TYPE = 'type';

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel('Ville')
            ->addCssClass('location-iq-container')
            ->addCssFiles('/assets/css/location-iq.css')
            ->setFormType(LocationIqType::class)
            ->setFormTypeOption(self::OPTION_TYPE, LocationIqType::MOBILITIES)
            ->setTemplatePath('@admin/field/location.html.twig')
            ->addJsFiles('https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js')
            ->addJsFiles('/assets/js/location-iq.js')
        ;
    }

    public function useMobilities(): self
    {
        $this->setFormTypeOption(self::OPTION_TYPE, LocationIqType::MOBILITIES);

        return $this;
    }

    public function useCities(): self
    {
        $this->setFormTypeOption(self::OPTION_TYPE, LocationIqType::CITIES);

        return $this;
    }
}
