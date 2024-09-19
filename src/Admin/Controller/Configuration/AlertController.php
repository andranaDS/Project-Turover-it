<?php

namespace App\Admin\Controller\Configuration;

use App\Admin\Form\Type\ProsemirrorType;
use App\Core\Entity\Alert;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class AlertController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Alert::class;
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
            ->addFormTheme('@admin/formtypes/prosemirror.html.twig')
            ->setDefaultSort(['id' => Criteria::DESC])
            ->setEntityLabelInPlural('Alertes')
            ->setEntityLabelInSingular('Alerte')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextEditorField::new('contentHtml', 'Contenu')->setFormTypeOptions(['attr' => ['data-prose-json' => 'contentJson']])->setFormType(ProsemirrorType::class);
        yield HiddenField::new('contentJson')->onlyOnForms()->setFormTypeOptions(['attr' => ['class' => 'contentJson']]);
        yield DateTimeField::new('startAt', 'Date de début');
        yield DateTimeField::new('endAt', 'Date de fin');
        yield BooleanField::new('blocking', 'Bloquant ?')
            ->setCustomOption(BooleanField::OPTION_RENDER_AS_SWITCH, false)
        ;
    }
}
