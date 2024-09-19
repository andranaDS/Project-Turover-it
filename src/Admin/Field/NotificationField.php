<?php

namespace App\Admin\Field;

use App\Admin\Form\Type\UserNotificationType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class NotificationField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = 'Image'): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(UserNotificationType::class)
            ->setCssClass('form-switch')
        ;
    }
}
