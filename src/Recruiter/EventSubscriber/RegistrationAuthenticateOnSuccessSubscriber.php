<?php

namespace App\Recruiter\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Security\AccessTokenUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RegistrationAuthenticateOnSuccessSubscriber implements EventSubscriberInterface
{
    private ?Recruiter $recruiter = null;
    private AccessTokenUtils $accessTokenUtils;
    private int $accessTokenTtl;

    public function __construct(AccessTokenUtils $accessTokenUtils, int $accessTokenTtl)
    {
        $this->accessTokenUtils = $accessTokenUtils;
        $this->accessTokenTtl = $accessTokenTtl;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', EventPriorities::POST_WRITE],
            KernelEvents::RESPONSE => ['onPostRespond', EventPriorities::POST_RESPOND],
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

        $this->recruiter = $recruiter;
    }

    public function onPostRespond(ResponseEvent $event): void
    {
        if (null === $this->recruiter) {
            return;
        }

        $response = $event->getResponse();
        if (Response::HTTP_CREATED !== $response->getStatusCode()) {
            return;
        }

        // authenticate the created response for login the recruiter after registration
        $this->accessTokenUtils->authenticateResponse($this->recruiter, $this->accessTokenTtl, $response);
    }
}
