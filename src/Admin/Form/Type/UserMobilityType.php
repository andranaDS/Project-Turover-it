<?php

namespace App\Admin\Form\Type;

use App\User\Entity\UserMobility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserMobilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', LocationIqType::class, [
                'type' => LocationIqType::MOBILITIES,
                'attr' => [
                    'class' => 'location-iq-container',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => UserMobility::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'locations';
    }
}
