<?php

namespace App\Admin\Form\Type;

use App\Core\Entity\Skill;
use App\User\Entity\UserSkill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('skill', EntityType::class, [
                'label' => 'Skill',
                'class' => Skill::class,
            ])
            ->add('main', CheckboxType::class, [
                'label' => 'principal ?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => UserSkill::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'locations';
    }
}
