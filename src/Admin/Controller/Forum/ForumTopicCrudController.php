<?php

namespace App\Admin\Controller\Forum;

use App\Forum\Entity\ForumTopic;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ForumTopicCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ForumTopic::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => Criteria::DESC])
            ->setEntityLabelInPlural('Topics')
            ->setEntityLabelInSingular('Topic')
            ->setSearchFields([
                'id',
                'title',
                'author.email',
                'author.nickname',
                'category.title',
            ])
            ->addFormTheme('@admin/formtypes/prosemirror.html.twig')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('category', 'Catégorie')
            ->setQueryBuilder(
                fn (QueryBuilder $qb) => $qb->addCriteria(Criteria::create()
                    ->andWhere(Criteria::expr()->neq('parent', null)))
            )
        ;

        yield AssociationField::new('initialPost', 'Message initial')->hideOnForm();
        yield TextField::new('title', 'Titre');
        yield TextField::new('metaTitle', 'SEO - Meta-Title')->setHelp('Recommandation : le meta-title ne devrait pas dépasser 70 caractères. Pour plus d\'informations, https://seomofo.com');
        yield TextareaField::new('metaDescription', 'SEO - Meta-Description')->setHelp('Recommandation : la meta-description ne devrait pas dépasser 160 caractères. Pour plus d\'informations, https://seomofo.com');
        yield AssociationField::new('author', 'Auteur')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Date de modification')->hideOnForm();
        yield IntegerField::new('postsCount', 'Nombre de messages')->onlyOnDetail();
        yield IntegerField::new('repliesCount', 'Nombre de réponses')->onlyOnDetail();
        yield IntegerField::new('viewsCount', 'Nombre de vues')->onlyOnDetail();
        yield IntegerField::new('upvotesCount', 'Nombre de like')->onlyOnDetail();
    }
}
