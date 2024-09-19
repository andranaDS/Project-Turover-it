<?php

namespace App\Admin\Controller\Forum;

use App\Admin\Form\Type\ProsemirrorType;
use App\Forum\Entity\ForumPost;
use App\Forum\Manager\ForumPostManager;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\HttpFoundation\Response;

class ForumPostCrudController extends AbstractCrudController
{
    private ForumPostManager $fpm;
    private EntityRepository $er;
    private EntityManagerInterface $entityManager;

    public function __construct(ForumPostManager $fpm, EntityRepository $er, EntityManagerInterface $entityManager)
    {
        $this->fpm = $fpm;
        $this->er = $er;
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return ForumPost::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $deleteCustomIndex = Action::new('deleteCustom', 'Supprimer')
            ->linkToCrudAction('deleteCustom')
            ->displayAsLink()
            ->addCssClass('confirm-action text-danger')
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-confirm',
            ])
        ;

        $deleteCustomDetail = Action::new('deleteCustom', 'Supprimer', 'fa fa-trash-o')
            ->linkToCrudAction('deleteCustom')
            ->displayAsLink()
            ->addCssClass('confirm-action text-danger')
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-confirm',
            ])
        ;

        $deleteCustomEdit = Action::new('deleteCustom', 'Supprimer', 'fa fa-trash-o')
            ->linkToCrudAction('deleteCustom')
            ->displayAsLink()
            ->addCssClass('confirm-action text-danger')
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-confirm',
            ])
        ;

        $deleteCustomBatch = Action::new('deleteCustomBatch', 'Supprimer les messages', 'fa fa-trash-o')
            ->linkToCrudAction('deleteCustomBatch')
            ->displayAsLink()
            ->addCssClass('confirm-action text-danger')
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-confirm',
            ])
        ;

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)

            ->add(Crud::PAGE_INDEX, $deleteCustomIndex)
            ->add(Crud::PAGE_DETAIL, $deleteCustomDetail)
            ->add(Crud::PAGE_EDIT, $deleteCustomEdit)
            ->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL)

            ->addBatchAction($deleteCustomBatch)

            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, 'deleteCustom'])
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
            ->setEntityLabelInPlural('Messages')
            ->setEntityLabelInSingular('Message')
            ->setSearchFields([
                'id',
                'content',
                'author.email',
                'author.nickname',
                'topic.title',
            ])
            ->addFormTheme('@admin/formtypes/prosemirror.html.twig')
        ;
    }

    public function deleteCustom(AdminContext $context): Response
    {
        $forumPost = $context->getEntity()->getInstance();
        $this->fpm->delete($forumPost);

        return $this->redirectToRoute('admin', [
            'crudAction' => 'index',
            'crudControllerFqcn' => ForumPostCrudController::class,
        ]);
    }

    public function deleteCustomBatch(BatchActionDto $batchActionDto): Response
    {
        foreach ($batchActionDto->getEntityIds() as $id) {
            if (null !== $forumPost = $this->entityManager->getRepository(ForumPost::class)->find($id)) {
                $this->fpm->delete($forumPost);
            }
        }

        $this->entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('topic', 'Topic')->hideOnForm();
        yield AssociationField::new('parent', 'Réponse de')->hideOnForm();
        yield TextField::new('content', 'Contenu')->hideOnForm();
        yield TextEditorField::new('contentHtml', 'Contenu')->setFormTypeOptions(['attr' => ['data-prose-json' => 'contentJson']])->setFormType(ProsemirrorType::class)->onlyOnForms();
        yield HiddenField::new('contentJson', 'content')->onlyOnForms()->setFormTypeOptions(['attr' => ['class' => 'contentJson']]);
        yield AssociationField::new('author', 'Auteur')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Date de modification')->onlyOnDetail();
        yield DateTimeField::new('moderatedAt', 'Modéré le')->setHelp('Lorsqu\'une date est inséré le message devient modéré');
        yield TextField::new('ip', 'Ip')->onlyOnDetail();
        yield IntegerField::new('upvotesCount', 'Nombre de like')->onlyOnDetail();
        yield BooleanField::new('hidden', 'Caché ?')->onlyOnDetail();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = $this->er->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response->andWhere('entity.deletedAt IS NULL');

        return $response;
    }
}
