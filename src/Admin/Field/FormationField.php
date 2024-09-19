<?php

namespace App\Admin\Field;

use App\Admin\Form\Type\UserFormationType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class FormationField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = 'Image'): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(UserFormationType::class)
            ->setCssClass('form-switch')
        ;
    }
}
