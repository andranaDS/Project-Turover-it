<?php

namespace App\Company\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Company\Repository\CompanyRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class CompanyExistsSubscriber implements EventSubscriberInterface
{
    private CompanyRepository $repository;

    public function __construct(CompanyRepository $repository)
    {
        $this->repository = $repository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['process', EventPriorities::PRE_READ],
            ],
        ];
    }

    public function process(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!\in_array($request->attributes->get('_route'), [
            'api_companies_job_postings_get_subresource',
            'api_companies_recruiters_get_subresource',
            'api_companies_sites_get_subresource',
            'api_job_postings_freework_get_companies_slug_job_postings_collection',
        ], true)) {
            return;
        }

        $slug = $request->attributes->get('slug');
        if (!$this->repository->exists($slug)) {
            throw new NotFoundHttpException("The company \"$slug\" does not exist");
        }
    }
}
