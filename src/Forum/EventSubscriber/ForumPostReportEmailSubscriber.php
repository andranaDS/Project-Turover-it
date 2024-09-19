<?php

namespace App\Forum\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Admin\Controller\Forum\ForumPostReportCrudController;
use App\Core\Mailer\Mailer;
use App\Forum\Email\ForumPostReportEmail;
use App\Forum\Entity\ForumPostReport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ForumPostReportEmailSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;
    private LoggerInterface $logger;
    private string $marketingRecipientMarketing;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(Mailer $mailer, LoggerInterface $logger, string $marketingRecipientMarketing, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->marketingRecipientMarketing = $marketingRecipientMarketing;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', EventPriorities::POST_WRITE],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $forumPostReport = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $route = $event->getRequest()->get('_route');

        if (!$forumPostReport instanceof ForumPostReport || Request::METHOD_POST !== $method || 'api_forum_post_reports_post_collection' !== $route) {
            return;
        }

        try {
            $email = (new ForumPostReportEmail())
                ->to($this->marketingRecipientMarketing)
                ->context([
                    'forumPostReport' => $forumPostReport,
                    'forumPostReportAdminUrl' => $this->adminUrlGenerator
                        ->setAction(Action::DETAIL)
                        ->setEntityId($forumPostReport->getId())
                        ->setController(ForumPostReportCrudController::class)
                        ->generateUrl(),
                ])
            ;
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
