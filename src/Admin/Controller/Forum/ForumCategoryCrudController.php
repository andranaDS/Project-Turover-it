<?php

namespace App\Admin\Controller\Forum;

use App\Core\Enum\Locale;
use App\Forum\Entity\ForumCategory;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ForumCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ForumCategory::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)

            ->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['parent' => Criteria::ASC])
            ->setEntityLabelInPlural('Catégories')
            ->setEntityLabelInSingular('Catégorie')
            ->setSearchFields([
                'title',
                'description',
                'parent.title',
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield ChoiceField::new('locales', 'Langues')->setChoices(Locale::getBoChoices())->allowMultipleChoices(true)->renderExpanded(true);
        yield AssociationField::new('parent', 'Catégorie Parent');
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('description', 'Description');
        yield TextField::new('metaTitle', 'SEO - Meta-Title')->setHelp('Recommandation : le meta-title ne devrait pas dépasser 70 caractères. Pour plus d\'informations, https://seomofo.com');
        yield TextareaField::new('metaDescription', 'SEO - Meta-Description')->setHelp('Recommandation : la meta-description ne devrait pas dépasser 160 caractères. Pour plus d\'informations, https://seomofo.com');

        if (Crud::PAGE_NEW !== $pageName) {
            yield IntegerField::new('position', 'Position');
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            yield CollectionField::new('children', 'Catégories enfants');
            yield IntegerField::new('topicsCount', 'Nombre de topics');
            yield IntegerField::new('postsCount', 'Nombre de posts');
            yield DateTimeField::new('createdAt', 'Date de création');
            yield DateTimeField::new('updatedAt', 'Date de mise à jour');
        }
    }
}
