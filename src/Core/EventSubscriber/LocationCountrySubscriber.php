<?php

namespace App\Core\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Company\Entity\Company;
use App\Recruiter\Entity\Recruiter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocationCountrySubscriber implements EventSubscriberInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['process', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function process(ViewEvent $event): void
    {
        $company = null;
        $object = $event->getControllerResult();
        $route = $event->getRequest()->attributes->get('_route');
        $allowedRoutes = [
            'api_recruiters_turnover_post_collection',
            'api_companies_turnover_patch_account_item',
        ];

        if ($object instanceof Recruiter) {
            $company = $object->getCompany();
        }

        if (!$company instanceof Company || !\in_array($route, $allowedRoutes, true) || (null === $location = $company->getBillingAddress()) || null === $countryCode = $location->getCountryCode()) {
            return;
        }

        $location->setCountry($this->translator->trans('app_user_enum_company_country_code_' . strtolower($countryCode), [], 'enums'));
    }
}
