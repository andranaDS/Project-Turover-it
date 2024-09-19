<?php

namespace App\Admin\Form\Type;

use App\User\Entity\UserNotification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserNotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marketingNewsletter', CheckboxType::class, [
                'label' => 'Accepte de recevoir par mail les actualité tech de Free-Work',
            ])
            ->add('forumTopicReply', CheckboxType::class, [
                'label' => 'Recevoir un e-mail à chaque réponse d’un sujet créé',
            ])
            ->add('forumTopicFavorite', CheckboxType::class, [
                'label' => 'Recevoir un e-mail à chaque réponse d’un sujet créé mis en favoris',
            ])
            ->add('forumPostReply', CheckboxType::class, [
                'label' => 'Recevoir un e-mail à chaque commentaire lié un post publié',
            ])
            ->add('forumPostLike', CheckboxType::class, [
                'label' => 'Recevoir un e-mail à chaque like lié un post publié',
            ])
            ->add('messagingNewMessage', CheckboxType::class, [
                'label' => 'Recevoir un e-mail à chaque nouveau message reçu dans la messagerie',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => UserNotification::class,
            'class' => null,
            'query_builder' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'formation';
    }
}
