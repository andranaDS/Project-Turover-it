<?php

namespace App\Admin\Form\Type;

use App\User\Entity\UserLanguage;
use App\User\Enum\Language;
use App\User\Enum\LanguageLevel;
use Greg0ire\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserLanguageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('language', EnumType::class, [
                'class' => Language::class,
                'prefix_label_with_class' => true,
            ])
            ->add('languageLevel', EnumType::class, [
                'class' => LanguageLevel::class,
                'prefix_label_with_class' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => UserLanguage::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'locations';
    }
}
