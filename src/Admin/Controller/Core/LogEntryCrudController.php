<?php

namespace App\Admin\Controller\Core;

use App\Admin\Field\LogEntryDataField;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Gedmo\Loggable\Entity\LogEntry;

class LogEntryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LogEntry::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Action::INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::DELETE, Action::EDIT)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => Criteria::DESC])
            ->setEntityLabelInPlural('Logs')
            ->setEntityLabelInSingular('Log')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $action = TextField::new('action', 'Action');
        $loggedAt = DateTimeField::new('loggedAt', 'Date');
        $objectClass = TextField::new('objectClass', 'Objet - Class');
        $objectId = TextField::new('objectId', 'Objet - Id');
        $data = LogEntryDataField::new('data', 'Modifications');
        $username = TextField::new('username', 'Utilisateur');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$action, $loggedAt, $objectClass, $objectId, $username];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [$action, $loggedAt, $objectClass, $objectId, $data, $username];
        }

        return [];
    }
}
