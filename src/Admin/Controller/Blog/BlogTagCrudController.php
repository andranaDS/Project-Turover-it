<?php

namespace App\Admin\Controller\Blog;

use App\Blog\Entity\BlogTag;
use App\Core\Enum\Locale;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BlogTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogTag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => Criteria::ASC])
            ->setEntityLabelInPlural('Tags')
            ->setEntityLabelInSingular('Tag')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ChoiceField::new('locales', 'Langues')->setChoices(Locale::getBoChoices())->allowMultipleChoices(true)->renderExpanded(true),
            TextField::new('name', 'Nom'),
            TextField::new('metaTitle', 'SEO - Meta-Title')
                ->setHelp('Recommandation : le meta-title ne devrait pas dépasser 70 caractères. Pour plus d\'informations, https://seomofo.com'),
            TextareaField::new('metaDescription', 'SEO - Meta-Description')
                ->setHelp('Recommandation : la meta-description ne devrait pas dépasser 160 caractères. Pour plus d\'informations, https://seomofo.com'),
        ];
    }
}
