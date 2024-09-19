<?php

namespace App\JobPosting\Controller\FreeWork\JobPosting;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\JobPosting\ElasticSearch\JobPostingsFilters\JobPostingsFilters;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Contract;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class GetSuggestedBanner
{
    public function __invoke(Security $security, Request $request, EntityManagerInterface $em, int $appItemsPerPage): PaginatorInterface
    {
        $user = $security->getUser();
        $page = 0 === ($page = (int) $request->query->get('page')) ? 1 : $page;
        $itemsPerPage = 0 === ($itemsPerPage = (int) $request->query->get('itemsPerPage')) ? $appItemsPerPage : $itemsPerPage;
        $filters = (new JobPostingsFilters())
            ->setOrder(JobPostingsFilters::ORDER_DATE)
        ;

        if ($user instanceof User) {
            $contracts = [];

            if (true === $user->getFreelance()) {
                $contracts[] = Contract::CONTRACTOR;
            }
            $filters->setContracts(array_merge($contracts, $user->getContracts() ?? []));
        }

        return $em->getRepository(JobPosting::class)->getPaginatorSuggested(
            $filters,
            $page,
            $itemsPerPage
        );
    }
}
