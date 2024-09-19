<?php

namespace App\Admin\Controller\Forum;

use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumPostReport;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class ForumPostReportCrudController extends AbstractCrudController
{
    private EntityManagerInterface $em;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->em = $em;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return ForumPostReport::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $acceptReportIndex = Action::new('acceptReport', 'Modérer')
            ->linkToCrudAction('acceptReport')
            ->addCssClass('confirm-action text-success')
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-confirm',
            ])
        ;

        $acceptReportDetail = Action::new('acceptReport', 'Modérer')
            ->linkToCrudAction('acceptReport')
            ->displayAsLink()
        ;

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)

            ->remove(Crud::PAGE_DETAIL, Action::EDIT)

            ->add(Crud::PAGE_INDEX, $acceptReportIndex)
            ->add(Crud::PAGE_DETAIL, $acceptReportDetail)
            ->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL)

            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                $action->setLabel('Rien à signaler');

                return $action;
            })
            ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
                $action->setLabel('Rien à signaler');

                return $action;
            })
        ;
    }

    public function configureAssets(Assets $assets): Assets
    {
        $assets->addJsFile('assets/js/confirm-modal.js');

        return parent::configureAssets($assets);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => Criteria::DESC])
            ->setEntityLabelInPlural('Signalement')
            ->setEntityLabelInSingular('Signalement')
            ->setSearchFields([
                'id',
                'content',
                'user.email',
                'user.nickname',
                'post.content',
            ])
        ;
    }

    public function acceptReport(AdminContext $context): Response
    {
        /** @var ForumPostReport $forumPostReport */
        $forumPostReport = $context->getEntity()->getInstance();
        $post = $forumPostReport->getPost();

        if ($post instanceof ForumPost) {
            $post->setModeratedAt(new \DateTime());

            $this->em->remove($forumPostReport);
            $this->em->flush();

            $url = $this->adminUrlGenerator
                ->setAction(Action::EDIT)
                ->setEntityId($post->getId())
                ->setController(ForumPostCrudController::class)
                ->generateUrl()
            ;

            return $this->redirect($url);
        }

        return $this->redirectToRoute('admin', [
            'crudAction' => Crud::PAGE_INDEX,
            'crudControllerFqcn' => ForumPostReportCrudController::class,
        ]);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('user', 'Auteur')->hideOnForm();
        yield AssociationField::new('post', 'Message')->hideOnForm();
        yield TextField::new('content', 'Contenu');
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
    }
}
