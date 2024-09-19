<?php

namespace App\Admin\Controller\Partner;

use App\Admin\Field\EnumField;
use App\Partner\Entity\Partner as PartnerEntity;
use App\Partner\Enum\Partner as PartnerEnum;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PartnerCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly SessionInterface $session,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return PartnerEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => Criteria::ASC])
            ->setEntityLabelInPlural('Partenaires')
            ->setEntityLabelInSingular('Partenaire')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EnumField::new('partner', 'Partenaire')
                ->setColumns(6)
                ->setFormTypeOption('class', PartnerEnum::class),
            NumberField::new('distribution', 'Répartition')
                ->setColumns(6)
                ->setHelp('La répartition doit etre comprise entre 0 et 100'),
            UrlField::new('apiUrl', 'URL de l\'api')
                ->setColumns(12)
                ->setHelp('/!\ Ne pas oublier le <b>https://</b> dans l\'url, par exemple: <b>https://hooks.zapier.com/hooks/catch/13933685/bpxh6r7/</b>')
                ->hideOnIndex(),
        ];
    }

    // @phpstan-ignore-next-line
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var PartnerEntity $entityInstance */
        if (PartnerEnum::NONE === $entityInstance->getPartner()) {
            // @phpstan-ignore-next-line
            $this->session->getFlashBag()->add('warning', 'Vous ne pouvez pas supprimer ce partenaire.');

            return;
        }

        $this->entityManager->remove($entityInstance);
        $this->entityManager->flush();
    }

    // @phpstan-ignore-next-line
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var PartnerEntity $entityInstance */
        if (PartnerEnum::NONE === $entityInstance->getPartner()) {
            // @phpstan-ignore-next-line
            $this->session->getFlashBag()->add('warning', 'Vous ne pouvez pas modifier ce partenaire.');

            return;
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
