<?php

namespace App\Admin\Form\Type;

use App\User\Entity\UserDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('documentFile', VichFileType::class, [
                'label' => 'Fichier',
                'required' => true,
                'allow_delete' => false,
                'asset_helper' => true,
            ])
            ->add('resume', CheckboxType::class, [
                'label' => 'CV ?',
            ])
            ->add('defaultResume', CheckboxType::class, [
                'label' => 'CV par défaut ?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => UserDocument::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'locations';
    }
}
