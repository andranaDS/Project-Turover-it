<?php

namespace App\Admin\Form\Type;

use App\Core\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LocationIqType extends AbstractType
{
    public const MOBILITIES = 'mobilities';
    public const CITIES = 'cities';

    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street', HiddenType::class)
            ->add('locality', HiddenType::class)
            ->add('postalCode', HiddenType::class)
            ->add('adminLevel1', HiddenType::class)
            ->add('adminLevel2', HiddenType::class)
            ->add('country', HiddenType::class)
            ->add('countryCode', HiddenType::class)
            ->add('value', HiddenType::class)
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            /** @var Location|null $entity */
            $entity = $event->getData();
            $form = $event->getForm();
            $form
                ->add('locationIqInput', TextType::class, [
                    'label' => 'Ville',
                    'mapped' => false,
                    'attr' => [
                        'api-autocomplete' => LocationIqType::MOBILITIES === $options['type'] ? $this->router->generate('api_core_locations_mobilities') : $this->router->generate('api_core_locations_cities'),
                        'value' => $entity ? $entity->getLabel() : null,
                        'class' => 'location-iq',
                    ],
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => Location::class,
            'type' => LocationIqType::MOBILITIES,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'locationiq';
    }
}
