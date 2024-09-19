<?php

namespace App\Admin\Controller\FeedRss;

use App\Admin\Field\EnumField;
use App\Admin\Form\Type\FeedRssCompanyBlacklistType;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FeedRssCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FeedRss::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => Criteria::ASC])
            ->setEntityLabelInPlural('Flux Rss')
            ->setEntityLabelInSingular('Flux Rss')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom du flux')
            ->setHelp(' /!\ Une modification du nom entrainera une modification du slug /!\\'),
            TextField::new('slug', 'Slug du flux')
                ->setHelp(' /!\ Cette valeur est utilisé pour généré le nom du fichier: <b>free-work-rss-{slug}.xml</b> /!\\'),
            EnumField::new('type', 'Type')->setFormTypeOption('class', FeedRssType::class),
            EnumField::new('partner', 'Partenaire')->setFormTypeOption('class', FeedRssPartner::class),
            TextField::new('gaTag', 'Google Analytique tags')
                ->hideOnIndex()
                ->setHelp('Par exemple: utm_source=Jobrapido&utm_medium=cpc&utm_campaign=jrp'),
            CollectionField::new('blacklistCompanies', 'Liste des sociétés blacklistées')
                ->setEntryType(FeedRssCompanyBlacklistType::class)
                ->setRequired(false)
                ->hideOnIndex(),
        ];
    }
}
