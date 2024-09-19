<?php

namespace App\Admin\Controller\Core;

use App\Core\Entity\SensitiveContent;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SensitiveContentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SensitiveContent::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['text' => Criteria::ASC])
            ->setEntityLabelInPlural('Contenus sensibles')
            ->setEntityLabelInSingular('Contenu sensible')
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
