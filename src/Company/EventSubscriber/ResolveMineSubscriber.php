<?php

namespace App\Company\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Recruiter\Entity\Recruiter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class ResolveMineSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['process', EventPriorities::PRE_READ],
        ];
    }

    public function process(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!\in_array($request->attributes->get('_route'), [
            'api_companies_sites_get_subresource',
            'api_companies_recruiters_get_subresource',
            'api_users_turnover_get_company_candidates_collection',
            'api_companies_turnover_post_directory_media_item',
            'api_companies_turnover_patch_account_item',
            'api_companies_turnover_patch_directory_item',
        ], true)) {
            return;
        }

        if ('mine' !== $request->attributes->get('slug')) {
            return;
        }

        $recruiter = $this->security->getUser();
        if (!$recruiter instanceof Recruiter || null === $recruiter->getId()) {
            throw new AccessDeniedException();
        }

        $request->attributes->set('slug', $recruiter->getCompany()?->getSlug());
    }
}
