<?php

namespace App\Admin\Form\Type;

use App\User\Entity\UserFormation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('diplomaTitle', TextType::class, [
                'label' => 'Diplôme',
            ])
            ->add('diplomaLevel', IntegerType::class, [
                'label' => 'Niveau',
            ])
            ->add('school', TextType::class, [
                'label' => 'École / université',
            ])
            ->add('diplomaYear', IntegerType::class, [
                'label' => 'Année dʼobtention',
            ])
            ->add('beingObtained', CheckboxType::class, [
                'label' => 'En cours dʼattribution',
            ])
            ->add('selfTaught', CheckboxType::class, [
                'label' => 'Autodidacte',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => UserFormation::class,
            'class' => null,
            'query_builder' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'formation';
    }
}
