<?php

namespace App\Recruiter\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Mailer\Mailer;
use App\Core\Util\TokenGenerator;
use App\Recruiter\Email\RegistrationSecondaryEmailConfirmationEmail;
use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Manager\RecruiterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RegistrationSecondaryEmailConfirmSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private Mailer $mailer;
    private RouterInterface $router;
    private RecruiterManager $rm;

    public function __construct(EntityManagerInterface $em, RecruiterManager $rm, Mailer $mailer, RouterInterface $router)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->rm = $rm;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', EventPriorities::POST_WRITE],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $recruiter = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $route = $event->getRequest()->get('_route');

        if (!$recruiter instanceof Recruiter || Request::METHOD_POST !== $method || 'api_recruiters_turnover_post_secondary_collection' !== $route) {
            return;
        }

        $plainPassword = TokenGenerator::generate();
        $this->rm->setPassword($recruiter, $plainPassword);
        $this->em->flush();

        $email = (new RegistrationSecondaryEmailConfirmationEmail())
            ->setVariables([
                'first_name' => $recruiter->getFirstName(),
                'link' => $this->router->generate('turnover_front_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'user_admin' => $recruiter->getCreatedBy()?->getName(),
                'password' => $plainPassword,
            ])
        ;

        $this->mailer->sendRecruiter($email, $recruiter);
    }
}
