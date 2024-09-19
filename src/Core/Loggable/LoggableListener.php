<?php

namespace App\Core\Loggable;

use App\User\Entity\User;
use Gedmo\Loggable\LoggableListener as GemdoLoggableListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class LoggableListener extends GemdoLoggableListener
{
    private Security $security;
    private RequestStack $requestStack;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        parent::__construct();
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    // @phpstan-ignore-next-line
    public function createLogEntry($action, $object, LoggableAdapter $ea): void
    {
        // check route
        $request = $this->requestStack->getMainRequest();
        if (null === $request || 'admin' !== $request->get('_route')) {
            return;
        }

        // check user
        $user = $this->security->getUser();
        if ($user instanceof User && null !== ($email = $user->getEmail())) {
            $this->username = $email;
        }

        parent::createLogEntry($action, $object, $ea);
    }
}
