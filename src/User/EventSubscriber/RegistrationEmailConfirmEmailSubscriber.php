<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Mailer\Mailer;
use App\Core\Util\TokenGenerator;
use App\User\Email\RegistrationEmailConfirmationEmail;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RegistrationEmailConfirmEmailSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private Mailer $mailer;
    private RouterInterface $router;

    public const UTM_QUERY_PARAMS = [
        'utm_campaign',
        'utm_medium',
        'utm_source',
        'utm_term',
        'utm_content',
    ];

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
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $route = $event->getRequest()->get('_route');

        if (!$user instanceof User || Request::METHOD_POST !== $method || 'api_users_freework_post_collection' !== $route) {
            return;
        }

        $user->setConfirmationToken(TokenGenerator::generate());
        $this->em->flush();

        if (null !== $user->getEmail()) {
            $email = (new RegistrationEmailConfirmationEmail())
                ->to($user->getEmail())
                ->context([
                    'user' => $user,
                    'registrationUrl' => $this->router->generate('candidates_registration_email_confirm', array_merge(
                        ['token' => $user->getConfirmationToken()],
                        $this->getUTMParamFromRequest($event->getRequest())
                    ), UrlGeneratorInterface::ABSOLUTE_URL),
                ])
            ;

            $this->mailer->send($email);
        }
    }

    private function getUTMParamFromRequest(Request $request): array
    {
        $params = [];
        try {
            $content = Json::decode($request->getContent(), Json::FORCE_ARRAY);
            foreach (self::UTM_QUERY_PARAMS as $param) {
                if (isset($content[$param])) {
                    $params[$param] = $content[$param];
                }
            }
        } catch (JsonException $e) {
            // @ignoreException
        } finally {
            return $params;
        }
    }
}
