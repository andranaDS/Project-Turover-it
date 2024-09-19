<?php

namespace App\JobPosting\Controller\FreeWork\JobPosting;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\JobPosting\ElasticSearch\JobPostingsFilters\JobPostingsFilters;
use App\JobPosting\ElasticSearch\JobPostingsFilters\JobPostingsUserFiltersBuilder;
use App\JobPosting\Entity\JobPosting;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class GetSuggested
{
    public function __invoke(UserInterface $user, JobPostingsUserFiltersBuilder $fb, Request $request, EntityManagerInterface $em, int $appItemsPerPage): PaginatorInterface
    {
        if (!$user instanceof User) {
            throw new AuthenticationException();
        }

        $page = 0 === ($page = (int) $request->query->get('page')) ? 1 : $page;
        $itemsPerPage = 0 === ($itemsPerPage = (int) $request->query->get('itemsPerPage')) ? $appItemsPerPage : $itemsPerPage;

        $filters = $fb->build($user);
        $filters->setPublishedSince(JobPostingsFilters::buildString($request->query->get('publishedSince')));

        return $em->getRepository(JobPosting::class)->getPaginatorSuggested(
            $filters,
            $page,
            $itemsPerPage
        );
    }
}
