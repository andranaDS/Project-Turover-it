<?php

namespace App\User\Security\Guard;

use App\User\Entity\User;
use App\User\Entity\UserProvider;
use App\User\Event\UserEvents;
use App\User\Response\ProviderAuthenticationFailureResponse;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\LinkedInResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractProviderAuthenticator extends OAuth2Authenticator implements ProviderAuthenticatorInterface
{
    private bool $isCreated;
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $em;
    private AuthenticationSuccessHandler $authenticationSuccessHandler;
    private EventDispatcherInterface $dispatcher;
    private TranslatorInterface $translator;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, AuthenticationSuccessHandler $authenticationSuccessHandler, EventDispatcherInterface $dispatcher, TranslatorInterface $translator)
    {
        $this->isCreated = false;
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
    }

    public function supports(Request $request): bool
    {
        return 'api_user_freework_security_login_provider' === $request->attributes->get('_route') && $this->getProvider() === ($request->attributes->get('_route_params')['provider'] ?? null);
    }

    public function authenticate(Request $request): Passport
    {
        $options = [];

        if (null !== $redirectUri = $request->get('redirect_uri')) {
            $options['redirect_uri'] = $redirectUri;
        }

        $accessToken = $this->fetchAccessToken($this->getClient(), $options);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken) {
                /** @var AccessToken $accessToken */

                // 1. have they logged in with this provider before?
                /** @var GoogleUser|LinkedInResourceOwner $providerResponse */
                $providerResponse = $this->getClient()
                    ->fetchUserFromToken($accessToken)
                ;

                $existingUserProvider = $this->em->getRepository(UserProvider::class)
                    ->findOneByProvider($this->getProvider(), $providerResponse->getId())
                ;

                if ($existingUserProvider) {
                    // update access token
                    $this->updateUserProvider($existingUserProvider, $accessToken->getToken());
                    $this->em->flush();

                    return $existingUserProvider->getUser();
                }

                // 2. do we have a matching user by email?
                $email = $providerResponse->getEmail();

                if (null === $email) {
                    return null;
                }

                $user = $this->em->getRepository(User::class)->findOneByEmail($email);

                $providerData = $providerResponse->toArray();

                // 3. if not, create a user account
                if (null === $user) {
                    $user = (new User())
                        ->setEmail($email)
                    ;
                    $this->hydrateUser($user, $providerData);
                    $this->em->persist($user);
                    $this->isCreated = true;
                }

                // 4. force enable
                $user->setEnabled(true);

                // 5. create oauth connect to this user
                $userOAuthConnect = $this->createUserProvider($user, $email, $providerResponse->getId(), $accessToken->getToken());
                $this->em->persist($userOAuthConnect);

                // 6. create and update user data
                $this->em->flush();

                if (true === $this->isCreated) {
                    $this->dispatcher->dispatch(new GenericEvent($user), UserEvents::PROVIDER_USER_CREATED);
                }

                return $user;
            })
        );
    }

    public function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry
            ->getClient($this->getProvider())
        ;
    }

    public function createUserProvider(User $user, string $email, string $providerUserId, string $providerAccessToken): UserProvider
    {
        return (new UserProvider())
            ->setUser($user)
            ->setEmail($email)
            ->setProvider($this->getProvider())
            ->setProviderUserId($providerUserId)
            ->setAccessToken($providerAccessToken)
        ;
    }

    public function updateUserProvider(UserProvider $userOAuthConnect, string $providerAccessToken): UserProvider
    {
        return $userOAuthConnect
            ->setAccessToken($providerAccessToken)
        ;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $response = null;

        if (null !== $user = $token->getUser()) {
            $response = $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
            $response->setStatusCode(true === $this->isCreated ? Response::HTTP_CREATED : Response::HTTP_NO_CONTENT);

            $this->isCreated = false;
        }

        return $response;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ProviderAuthenticationFailureResponse
    {
        return new ProviderAuthenticationFailureResponse($this->translator->trans('user.authentication.error.provider'));
    }
}
