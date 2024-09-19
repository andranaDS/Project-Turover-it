<?php

namespace App\Admin\Controller\Core;

use App\Core\Entity\ForbiddenContent;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ForbiddenContentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ForbiddenContent::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['text' => Criteria::ASC])
            ->setEntityLabelInPlural('Contenus interdits')
            ->setEntityLabelInSingular('Contenu interdit')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('text', 'Texte'),
        ];
    }
}
