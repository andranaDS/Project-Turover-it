<?php

namespace App\Admin\Controller\Blog;

use App\Admin\Field\VichImageField;
use App\Blog\Entity\BlogPostImage;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class BlogPostImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPostImage::class;
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
            ->setDefaultSort(['id' => Criteria::DESC])
            ->setEntityLabelInPlural('Images')
            ->setEntityLabelInSingular('Image')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $image = ImageField::new('image')->setTemplatePath('@admin/field/image.html.twig');
        $imageFile = VichImageField::new('imageFile', 'Image - Fichier')
            ->setFormTypeOption('allow_delete', false)
            ->setHelp('Poids 5Mo max / Largeur 500px min - 2048px max / Hauteur 500px min - 2048px max')
        ;

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $image];
        }
        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            return [$imageFile];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $image];
        }

        return [];
    }
}
