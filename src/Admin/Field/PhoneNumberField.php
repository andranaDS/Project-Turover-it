<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;

final class PhoneNumberField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = 'Téléphone'): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(PhoneNumberType::class)
            ->setTemplatePath('@admin/field/phone_number.html.twig')
            ->setFormTypeOptions([
                'default_region' => 'FR',
                'format' => PhoneNumberFormat::NATIONAL,
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'country_options' => [
                    'label' => 'Pays',
                ],
                'number_options' => [
                    'label' => 'Numéro',
                ],
            ])
        ;
    }
}
