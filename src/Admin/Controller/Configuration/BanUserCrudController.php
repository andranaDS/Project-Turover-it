<?php

namespace App\Admin\Controller\Configuration;

use App\User\Entity\BanUser;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BanUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BanUser::class;
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
            ->setDefaultSort(['id' => Criteria::DESC])
            ->setEntityLabelInPlural('Ban utilisateurs')
            ->setEntityLabelInSingular('Ban utilisateur')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('user', 'Utilisateur banni')->autocomplete();
        yield AssociationField::new('author', 'Banni par')->hideOnForm();
        yield TextField::new('reason', 'Raison');
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
    }
}
