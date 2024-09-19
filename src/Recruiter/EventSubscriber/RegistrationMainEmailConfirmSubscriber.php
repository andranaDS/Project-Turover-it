<?php

namespace App\Recruiter\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Mailer\Mailer;
use App\Core\Util\TokenGenerator;
use App\Recruiter\Email\RegistrationMainEmailConfirmationEmail;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RegistrationMainEmailConfirmSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private Mailer $mailer;
    private RouterInterface $router;

    public function __construct(EntityManagerInterface $em, Mailer $mailer, RouterInterface $router)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->router = $router;
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

        if (!$recruiter instanceof Recruiter || Request::METHOD_POST !== $method || 'api_recruiters_turnover_post_collection' !== $route) {
            return;
        }

        $recruiter->setConfirmationToken(TokenGenerator::generate(32));
        $this->em->flush();

        $email = (new RegistrationMainEmailConfirmationEmail())
            ->setVariables([
                'first_name' => $recruiter->getFirstName(),
                'confirmation_link' => $this->router->generate('turnover_front_registration_email_confirm', [
                    'token' => $recruiter->getConfirmationToken(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ])
        ;

        $this->mailer->sendRecruiter($email, $recruiter);
    }
}
