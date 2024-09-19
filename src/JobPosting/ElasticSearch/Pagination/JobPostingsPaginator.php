<?php

namespace App\JobPosting\ElasticSearch\Pagination;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Pagerfanta\Pagerfanta;

class JobPostingsPaginator implements PaginatorInterface, \IteratorAggregate
{
    private ?\ArrayIterator $iterator = null;
    private ?Pagerfanta $pagerfanta = null;
    private EntityManagerInterface $em;
    private PaginatedFinderInterface $paginatedFinder;
    private ?int $page;
    private ?int $itemsPerPage;

    public function __construct(EntityManagerInterface $em, PaginatedFinderInterface $appPaginatedFinder)
    {
        $this->em = $em;
        $this->paginatedFinder = $appPaginatedFinder;
    }

    public function setQuery(Query $query, ?int $page = null, ?int $itemsPerPage = null): self
    {
        $this->iterator = null;
        $this->pagerfanta = $this->paginatedFinder->findPaginated($query);
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    public function getLastPage(): float
    {
        if (null === $this->pagerfanta) {
            throw new \LogicException('No request set. You must call the setQuery() method.');
        }

        return $this->pagerfanta->getNbPages();
    }

    public function getTotalItems(): float
    {
        if (null === $this->pagerfanta) {
            throw new \LogicException('No request set. You must call the setQuery() method.');
        }

        return $this->pagerfanta->getNbResults();
    }

    public function getCurrentPage(): float
    {
        if (null === $this->pagerfanta) {
            throw new \LogicException('No request set. You must call the setQuery() method.');
        }

        return $this->pagerfanta->getCurrentPage();
    }

    public function getItemsPerPage(): float
    {
        if (null === $this->pagerfanta) {
            throw new \LogicException('No request set. You must call the setQuery() method.');
        }

        return $this->pagerfanta->getMaxPerPage();
    }

    public function count(): int
    {
        if (null === $this->pagerfanta) {
            throw new \LogicException('No request set. You must call the setQuery() method.');
        }

        return iterator_count($this->getIterator());
    }

    private function fetchResults(): array
    {
        if (null === $this->pagerfanta) {
            throw new \LogicException('No request set. You must call the setQuery() method.');
        }

        if (empty($this->getTotalItems())) {
            return [];
        }

        $totalItems = (int) $this->getTotalItems();
        if (0 === $totalItems) {
            return [];
        }

        if (null === $this->page || null === $this->itemsPerPage) {
            $currentPage = 1;
            $maxPerPage = max(1, $totalItems);
        } else {
            $maxPerPage = max(1, $this->itemsPerPage);
            $nbPages = (int) ceil($totalItems / $maxPerPage);
            $currentPage = (int) max(1, min($this->page, $nbPages));
        }

        $this->pagerfanta->setMaxPerPage($maxPerPage);
        $this->pagerfanta->setCurrentPage($currentPage);

        $jobPostingIds = Arrays::map($this->pagerfanta->getCurrentPageResults(), static function (JobPosting $jobPosting) {
            return $jobPosting->getId();
        });

        return $this->em->getRepository(JobPosting::class)->findDataByIds($jobPostingIds);
    }

    public function getIterator(): \ArrayIterator
    {
        if (null === $this->pagerfanta) {
            throw new \LogicException('No request set. You must call the setQuery() method.');
        }

        if (null === $this->iterator) {
            $this->iterator = new \ArrayIterator($this->fetchResults());
        }

        return $this->iterator;
    }
}
