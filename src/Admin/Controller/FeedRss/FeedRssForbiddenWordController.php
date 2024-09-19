<?php

namespace App\Admin\Controller\FeedRss;

use App\FeedRss\Entity\FeedRssForbiddenWord;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FeedRssForbiddenWordController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FeedRssForbiddenWord::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => Criteria::ASC])
            ->setEntityLabelInPlural('Mots interdits')
            ->setEntityLabelInSingular('Mot interdit')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'mot interdit')
                ->setHelp('Ne mettre qu\'un seul mot'),
        ];
    }
}
