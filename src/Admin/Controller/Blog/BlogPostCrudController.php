<?php

namespace App\Admin\Controller\Blog;

use App\Admin\Field\VichImageField;
use App\Admin\Form\Type\ProsemirrorType;
use App\Blog\Entity\BlogCategory;
use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogTag;
use App\Core\Enum\Locale;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
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
            ->setDefaultSort(['publishedAt' => Criteria::DESC])
            ->setEntityLabelInPlural('Articles')
            ->setEntityLabelInSingular('Article')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $locales = ChoiceField::new('locales', 'Langues')->setChoices(Locale::getBoChoices())->allowMultipleChoices(true)->renderExpanded(true);
        $category = AssociationField::new('category', 'Thématique')
            ->setRequired(true)
            ->setFormTypeOption('choice_label', function (BlogCategory $category) {
                return sprintf('%s (%s)', $category->getName(), implode(', ', $category->getLocales()));
            })
        ;
        $tags = AssociationField::new('tags', 'Tags')
            ->setRequired(true)
            ->setFormTypeOption('choice_label', function (BlogTag $blogTag) {
                return sprintf('%s (%s)', $blogTag->getName(), implode(', ', $blogTag->getLocales()));
            })
        ;
        $title = TextField::new('title', 'Titre');
        $excerpt = TextareaField::new('excerpt', 'Résumé');
        $metaTitle = TextField::new('metaTitle', 'SEO - Meta-Title')
            ->setHelp('Recommandation : le meta-title ne devrait pas dépasser 70 caractères. Pour plus d\'informations, https://seomofo.com')
        ;
        $metaDescription = TextareaField::new('metaDescription', 'SEO - Meta-Description')
            ->setHelp('Recommandation : la meta-description ne devrait pas dépasser 160 caractères. Pour plus d\'informations, https://seomofo.com')
        ;
        $image = ImageField::new('image')->setTemplatePath('@admin/field/image.html.twig');
        $imageFile = VichImageField::new('imageFile', 'Image - Fichier')
            ->setFormTypeOption('allow_delete', false)
            ->setHelp('Poids 5Mo max / Largeur 500px min - 2048px max / Hauteur 500px min - 2048px max')
        ;
        $imageAlt = TextField::new('imageAlt', 'Image - Alt');
        $contentHtml = TextEditorField::new('contentHtml', 'Contenu')
            ->setFormTypeOptions(['attr' => ['data-prose-json' => 'contentJson']])
            ->setFormType(ProsemirrorType::class)
        ;
        $content = TextField::new('content', 'Contenu');
        $contentJson = HiddenField::new('contentJson')->onlyOnForms()->setFormTypeOptions(['attr' => ['class' => 'contentJson']]);
        $published = BooleanField::new('published', 'Publié');
        $publishedAt = DateTimeField::new('publishedAt', 'Date de publication');
        $visible = BooleanField::new('visible', 'Visible')
            ->setCustomOption(BooleanField::OPTION_RENDER_AS_SWITCH, false)
        ;
        $upvotesCount = IntegerField::new('upvotesCount', 'Likes');
        $viewsCount = IntegerField::new('viewsCount', 'Vues');
        $readingTimeMinutes = IntegerField::new('readingTimeMinutes', 'Temps de lecture (m)');
        $createdAt = DateTimeField::new('createdAt', 'Date de création');
        $updatedAt = DateTimeField::new('updatedAt', 'Date de modification');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $title, $excerpt, $image, $upvotesCount, $viewsCount, $published,  $publishedAt, $updatedAt, $visible];
        }
        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            return [$locales, $title, $category, $tags, $excerpt, $metaTitle, $metaDescription, $imageFile, $imageAlt, $contentHtml, $contentJson, $published, $publishedAt, $updatedAt];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            $tags = ArrayField::new('tags', 'Tags');

            return [$id, $locales, $title, $category, $tags, $excerpt, $metaTitle, $metaTitle, $image, $content, $upvotesCount, $viewsCount, $readingTimeMinutes, $published, $publishedAt, $updatedAt, $createdAt];
        }

        return [];
    }
}
