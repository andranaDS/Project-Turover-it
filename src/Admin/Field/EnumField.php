<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Greg0ire\Enum\Bridge\Symfony\Form\Type\EnumType;

final class EnumField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'prefix_label_with_class' => true,
            ])
            ->setTemplatePath('@admin/field/enum.html.twig')
        ;
    }
}
