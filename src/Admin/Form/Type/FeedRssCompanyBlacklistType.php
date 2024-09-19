<?php

namespace App\Admin\Form\Type;

use App\Company\Entity\Company;
use App\FeedRss\Entity\FeedRssBlacklistCompany;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedRssCompanyBlacklistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('company', EntityType::class, [
                'label' => 'société',
                'class' => Company::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')
                        ->orderBy('c.name', Criteria::ASC)
                    ;
                },
                'choice_label' => function (Company $company) {
                    $label = $company->getName();

                    if (null !== $company->getOldId()) {
                        $label .= ' (# ' . $company->getOldId() . ')';
                    }

                    return $label;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => FeedRssBlacklistCompany::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'feed_rss_company_blacklist';
    }
}
