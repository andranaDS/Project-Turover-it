<?php

namespace App\Admin\Controller\Core;

use App\Admin\Controller\Blog\BlogPostCrudController;
use App\Blog\Entity\BlogCategory;
use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostImage;
use App\Blog\Entity\BlogTag;
use App\Core\Entity\Alert;
use App\Core\Entity\ForbiddenContent;
use App\Core\Entity\Job;
use App\Core\Entity\JobCategory;
use App\Core\Entity\SensitiveContent;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Entity\FeedRssForbiddenWord;
use App\Forum\Entity\ForumCategory;
use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumPostReport;
use App\Forum\Entity\ForumTopic;
use App\Partner\Entity\Partner;
use App\User\Entity\BanUser;
use App\User\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Gedmo\Loggable\Entity\LogEntry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * @Route("/", name="admin")
     */
    public function index(): Response
    {
        return $this->redirect($this->adminUrlGenerator->setController(BlogPostCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Api')
            ->setTranslationDomain('enums')
            ->disableUrlSignatures()
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Blog');
        yield MenuItem::linkToCrud('Articles', 'fa fa-comment', BlogPost::class);
        yield MenuItem::linkToCrud('Images', 'fa fa-images', BlogPostImage::class);
        yield MenuItem::linkToCrud('Thématiques', 'fa fa-bookmark', BlogCategory::class);
        yield MenuItem::linkToCrud('Tags', 'fa fa-tag', BlogTag::class);

        yield MenuItem::section('Forum');
        yield MenuItem::linkToCrud('Catégories', 'fa fa-bookmark', ForumCategory::class);
        yield MenuItem::linkToCrud('Messages', 'fa fa-comments', ForumPost::class);
        yield MenuItem::linkToCrud('Signalements', 'fa fa-exclamation-circle', ForumPostReport::class);
        yield MenuItem::linkToCrud('Topics', 'fa fa-comment', ForumTopic::class);

        yield MenuItem::section('Job');
        yield MenuItem::linkToCrud('Métiers', 'fa fa-briefcase', Job::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-bookmark', JobCategory::class);

        yield MenuItem::section('Spam');
        yield MenuItem::linkToCrud('Contenus sensibles', 'fa fa-hand-paper-o', SensitiveContent::class);
        yield MenuItem::linkToCrud('Contenus interdits', 'fa fa-hand-paper', ForbiddenContent::class);

        yield MenuItem::section('User');
        yield MenuItem::linkToCrud('Ban', 'fa fa-users-slash', BanUser::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class);

        yield MenuItem::section('Flux RSS');
        yield MenuItem::linkToCrud('Flux', 'fa fa-rss', FeedRss::class);
        yield MenuItem::linkToCrud('Mots interdits', 'fa fa-hand-paper', FeedRssForbiddenWord::class);

        yield MenuItem::section('Partenaire');
        yield MenuItem::linkToCrud('Partenaires', 'fa fa-exclamation-circle', Partner::class);

        yield MenuItem::section('Divers');
        yield MenuItem::linkToCrud('Alertes', 'fa fa-exclamation-circle', Alert::class);
        yield MenuItem::linkToCrud('Logs', 'fa fa-archive', LogEntry::class);
    }
}
