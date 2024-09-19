<?php

namespace App\Admin\Controller\Core;

use App\Core\Entity\Job;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JobCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Job::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => Criteria::ASC])
            ->setEntityLabelInPlural('Métiers')
            ->setEntityLabelInSingular('Métier')
            ->setSearchFields([
                'name',
                'category.name',
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Général'),
            IdField::new('id')
                ->setRequired(true)
                ->hideOnForm(),
            TextField::new('name', 'Nom')
                ->setColumns(6),
            AssociationField::new('category', 'Catégorie')
                ->setRequired(true)
                ->setColumns(6),

            FormField::addPanel('Page baromètre SEO'),
            TextareaField::new('salaryDescription', 'SEO - Description')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            TextareaField::new('salaryFormation', 'SEO - Formation')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            TextareaField::new('salaryStandardMission', 'SEO - Missions types')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            ArrayField::new('salarySkills', 'SEO - Compétences')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            TextField::new('salarySeoMetaTitle', 'SEO - Meta-Title')
                ->setRequired(true)
                ->setColumns(6)
                ->setHelp('Recommandation : le meta-title ne devrait pas dépasser 70 caractères. Pour plus d\'informations, https://seomofo.com')
                ->hideOnIndex(),
            TextareaField::new('salarySeoMetaDescription', 'SEO - Meta-Description')
                ->setRequired(true)
                ->setColumns(6)
                ->setHelp('Recommandation : la meta-description ne devrait pas dépasser 160 caractères. Pour plus d\'informations, https://seomofo.com')
                ->hideOnIndex(),

            FormField::addPanel('Page FAQ'),
            TextareaField::new('faqDescription', 'Description')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            TextareaField::new('faqPrice', 'Q1 - Quel est le tarif d\'un XXXX ?')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            TextareaField::new('faqDefinition', 'Q2 - Qu\'est-ce qu\'un XXXX ?')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            TextareaField::new('faqMissions', 'Q3 - Quelles sont les missions principales d\'un XXXX ?')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            TextareaField::new('faqSkills', 'Q4 - Quelles sont les compétences d\'un XXXX ?')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            TextareaField::new('faqProfile', 'Q5 - XXXX quel profil ?')
                ->setRequired(true)
                ->setColumns(6)
                ->hideOnIndex(),
            FormField::addRow(),
            TextField::new('faqSeoMetaTitle', 'SEO - Meta-Title')
                ->setRequired(true)
                ->setColumns(6)
                ->setHelp('Recommandation : le meta-title ne devrait pas dépasser 70 caractères. Pour plus d\'informations, https://seomofo.com')
                ->setRequired(true)
                ->hideOnIndex(),
            TextareaField::new('faqSeoMetaDescription', 'SEO - Meta-Description')
                ->setRequired(true)
                ->setColumns(6)
                ->setHelp('Recommandation : la meta-description ne devrait pas dépasser 160 caractères. Pour plus d\'informations, https://seomofo.com')
                ->hideOnIndex(),

            FormField::addPanel('Pole Emploi'),
            TextField::new('ROMECode', 'Code ROME')
                ->hideOnIndex()
                ->setColumns(6),
            TextField::new('OGRCode', 'Code OGR')
                ->hideOnIndex()
                ->setColumns(6),
            TextField::new('OGRLabel', 'libellé OGR')
                ->hideOnIndex()
                ->setColumns(6),
        ];
    }
}
