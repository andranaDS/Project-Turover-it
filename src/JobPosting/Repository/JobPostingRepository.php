<?php

namespace App\JobPosting\Repository;

use App\Company\Entity\Company;
use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Entity\FeedRssBlacklistCompany;
use App\FeedRss\Enum\FeedRssType;
use App\JobPosting\ElasticSearch\JobPostingsFilters\JobPostingsFilters;
use App\JobPosting\ElasticSearch\JobPostingsFilters\JobPostingsUserFiltersBuilder;
use App\JobPosting\ElasticSearch\JobPostingsQueryBuilder\JobPostingsQueryBuilder;
use App\JobPosting\ElasticSearch\Pagination\JobPostingsPaginator;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingUserTrace;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\Status;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method JobPosting|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPosting|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPosting[]    findAll()
 * @method JobPosting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingRepository extends ServiceEntityRepository
{
    private JobPostingsQueryBuilder $jobPostingsQueryBuilder;
    private JobPostingsUserFiltersBuilder $jobPostingsSuggestedFiltersBuilder;
    private int $appItemsPerPage;

    public function __construct(ManagerRegistry $registry, JobPostingsQueryBuilder $jobPostingsQueryBuilder, JobPostingsUserFiltersBuilder $jobPostingsSuggestedFiltersBuilder, int $appItemsPerPage)
    {
        parent::__construct($registry, JobPosting::class);
        $this->jobPostingsQueryBuilder = $jobPostingsQueryBuilder;
        $this->jobPostingsSuggestedFiltersBuilder = $jobPostingsSuggestedFiltersBuilder;
        $this->appItemsPerPage = $appItemsPerPage;
    }

    public function findDataByIds(array $ids): array
    {
        return $this->createQueryBuilder('jp')
            ->select('jp, jp_c, jp_s, jp_j')
            ->leftJoin('jp.company', 'jp_c')
            ->leftJoin('jp.skills', 'jp_s')
            ->leftJoin('jp.job', 'jp_j')
            ->where('jp.id IN (:ids)')
            ->orderBy('FIELD(jp.id, :ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getUserFavorites(UserInterface $user): array
    {
        return $this->createQueryBuilder('j')
            ->join('j.userFavorites', 'f')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', Criteria::DESC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getRecruiterFavorites(UserInterface $recruiter): array
    {
        return $this->createQueryBuilder('j')
            ->join('j.recruiterFavorites', 'f')
            ->where('f.recruiter = :recruiter')
            ->setParameter('recruiter', $recruiter)
            ->orderBy('f.createdAt', Criteria::DESC)
            ->getQuery()
            ->getResult()
        ;
    }

    private function countForHomeStats(array $contracts, ?\DateTime $after = null): int
    {
        if (empty($contracts)) {
            return 0;
        }

        $qb = $this->createQueryBuilder('j');
        $qb->select('COUNT(j) as count')
            ->andWhere('j.published = true')
            ->andWhere('j.publishedAt <= :end')
            ->setParameter('end', Carbon::now())
        ;

        if (null !== $after) {
            $qb->andWhere('j.publishedAt >= :start')
                ->setParameter('start', $after)
            ;
        }

        $expr = $qb->expr()->orX();
        foreach ($contracts as $contract) {
            $expr->add($qb->expr()->like('j.contracts', $qb->expr()->literal('%' . $contract . '%')));
        }

        $qb->andWhere($expr);

        return (int) $qb->getQuery()
            ->enableResultCache(300)
            ->getSingleScalarResult()
        ;
    }

    public function countFreeForHomeStats(?\DateTime $after = null): int
    {
        return $this->countForHomeStats([Contract::CONTRACTOR], $after);
    }

    public function countWorkForHomeStats(?\DateTime $after = null): int
    {
        return $this->countForHomeStats(Contract::getWorkValues(), $after);
    }

    public function countByCompanyGroupByContract(Company $company): array
    {
        $data = $this->createQueryBuilder('j')
            ->select('j.contracts, j.published, COUNT(j) as count')
            ->where('j.company = :company')
            ->andWhere('j.contracts IS NOT NULL')
            ->andWhere('j.publishedAt IS NULL OR j.publishedAt <= :now')
            ->setParameter('company', $company)
            ->setParameters([
                'company' => $company,
                'now' => Carbon::now(),
            ])
            ->groupBy('j.contracts')
            ->addGroupBy('j.published')
            ->getQuery()
            ->getResult()
        ;

        $counts = [];

        foreach ($data as $d) {
            foreach ($d['contracts'] as $contract) {
                $publishedStatus = true === $d['published'] ? 'published' : 'not_published';
                $counts[$publishedStatus][$contract] = !isset($counts[$publishedStatus][$contract]) ? $d['count'] : ($counts[$publishedStatus][$contract] + $d['count']);
                $counts['total'][$contract] = !isset($counts['total'][$contract]) ? $d['count'] : ($counts['total'][$contract] + $d['count']);
            }
        }

        return $counts;
    }

    public function findLastJobPostingByCompany(Company $company): ?JobPosting
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.company = :company')
            ->setParameter('company', $company)
            ->orderBy('j.publishedAt', Criteria::DESC)
            ->addOrderBy('j.id', Criteria::DESC)
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findFixLocationNeeded(): array
    {
        return $this->createQueryBuilder('jp')
            ->where('jp.location.value IS NOT NULL')
            ->andWhere('jp.location.countryCode IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findToUnpublish(array $oldIdNotDeleted): array
    {
        if (empty($oldIdNotDeleted)) {
            return [];
        }

        $qb = $this->createQueryBuilder('jp');

        return $qb->andWhere($qb->expr()->notIn('jp.oldId', $oldIdNotDeleted))
            ->andWhere('jp.published = true')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneDataById(int $id): ?JobPosting
    {
        return $this->createQueryBuilder('j')
            ->select('j, c, cp, s')
            ->join('j.company', 'c')
            ->leftJoin('c.pictures', 'cp')
            ->leftJoin('j.skills', 's')
            ->where('j.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByOldId(int $oldId): ?JobPosting
    {
        return $this->createQueryBuilder('j')
            ->select('j, c')
            ->join('j.company', 'c')
            ->where('j.oldId = :oldId')
            ->setParameter('oldId', $oldId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getQueryBuilderToPopulate(): QueryBuilder
    {
        return $this->createQueryBuilder('jp')
            ->select('jp, jp_s')
            ->leftJoin('jp.skills', 'jp_s')
            ->where('jp.published = true')
            ->andWhere('jp.publishedAt IS NOT NULL')
        ;
    }

    public function findUserSuggested(User $user, ?int $page = null, ?int $itemsPerPage = null): \ArrayIterator
    {
        $userFilters = $this->jobPostingsSuggestedFiltersBuilder->build($user);

        return $this->findSuggested($userFilters, $page, $itemsPerPage);
    }

    public function findUserSuggestedAfterNextAvailability(User $user, ?int $page = null, ?int $itemsPerPage = null): \ArrayIterator
    {
        if (null === $user->getNextAvailabilityAt()) {
            return new \ArrayIterator([]);
        }

        $userFilters = $this->jobPostingsSuggestedFiltersBuilder
            ->build($user)
            ->setStartsAfter($user->getNextAvailabilityAt())
        ;

        return $this->findSuggested($userFilters, $page, $itemsPerPage);
    }

    public function countUserSuggested(User $user): int
    {
        $filters = $this->jobPostingsSuggestedFiltersBuilder->build($user);

        return $this->countSuggested($filters);
    }

    public function getPaginatorSuggested(JobPostingsFilters $filters, ?int $page = null, ?int $itemsPerPage = null): JobPostingsPaginator
    {
        $qb = $this->jobPostingsQueryBuilder->createQueryBuilder();

        // where
        $qb
            ->addPublishedFilter()
            ->addContractFilter(Contract::getFreeWorkValues())
            ->addContractFilter(array_intersect($filters->getContracts(), Contract::getFreeWorkValues()))
            ->addRemoteFilter($filters->getRemoteMode())
            ->addMinSalaryFilter($filters->getMinAnnualSalary(), $filters->getMinDailySalary())
            ->addMinDurationFilter($filters->getMinDuration())
            ->addMaxDurationFilter($filters->getMaxDuration())
            ->addPublishedSinceFilter($filters->getPublishedSince())
            ->addLocationKeysFilter($filters->getLocationKeys())
            ->addStartsAfterFilter($filters->getStartsAfter())
            ->addSkillsFilter($filters->getSkills())
        ;

        // order
        self::withOrderByFilters($qb, $filters->getOrder());

        return $qb->getPaginator($page, $itemsPerPage);
    }

    public function findSuggested(JobPostingsFilters $filters, ?int $page = null, ?int $itemsPerPage = null): \ArrayIterator
    {
        return $this->getPaginatorSuggested($filters, $page, $itemsPerPage)
            ->getIterator()
        ;
    }

    public function countSuggested(JobPostingsFilters $filters): int
    {
        return (int) $this->getPaginatorSuggested($filters)
            ->getTotalItems()
        ;
    }

    public function getPaginatorSearch(JobPostingsFilters $filters, ?int $page = null, ?int $itemsPerPage = null): JobPostingsPaginator
    {
        $qb = $this->jobPostingsQueryBuilder->createQueryBuilder();

        // where
        $qb
            ->addPublishedFilter()
            ->addContractFilter(Contract::getFreeWorkValues())
            ->addContractFilter(array_intersect($filters->getContracts(), Contract::getFreeWorkValues()))
            ->addRemoteFilter($filters->getRemoteMode())
            ->addMinSalaryFilter($filters->getMinAnnualSalary(), $filters->getMinDailySalary())
            ->addMinDurationFilter($filters->getMinDuration())
            ->addMaxDurationFilter($filters->getMaxDuration())
            ->addPublishedSinceFilter($filters->getPublishedSince())
            ->addLocationKeysFilter($filters->getLocationKeys())
            ->addKeywordFilter($filters->getKeywords())
            ->addSkillsFilter($filters->getSkills())
            ->addJobsFilter($filters->getJobs())
            ->addPublishedAfterFilter($filters->getPublishedAfter())
            ->addPublishedBeforeFilter($filters->getPublishedBefore())
        ;

        // order
        self::withOrderByFilters($qb, $filters->getOrder());

        return $qb->getPaginator($page, $itemsPerPage);
    }

    public function getPaginatorTurnoverSearch(Request $request): JobPostingsPaginator
    {
        // 0. get parameters from query
        // filters
        $minDuration = JobPostingsFilters::buildInteger($request->query->get('minDuration'));
        $maxDuration = JobPostingsFilters::buildInteger($request->query->get('maxDuration'));
        $publishedSince = JobPostingsFilters::buildString($request->query->get('publishedSince'));
        $locations = JobPostingsFilters::buildArray($request->query->get('locations'));
        $minDailySalary = JobPostingsFilters::buildInteger($request->query->get('minDailySalary'));
        $maxDailySalary = JobPostingsFilters::buildInteger($request->query->get('maxDailySalary'));
        $remoteMode = JobPostingsFilters::buildArray($request->query->get('remoteMode'));
        $keywords = JobPostingsFilters::buildArray($request->query->get('keywords'));
        $businessActivity = $request->query->get('businessActivity');
        $intercontractOnly = JobPostingsFilters::buildBoolean($request->query->get('intercontractOnly'));

        // pagination
        $page = 0 === ($page = (int) $request->query->get('page')) ? 1 : $page;
        $itemsPerPage = 0 === ($itemsPerPage = (int) $request->query->get('itemsPerPage')) ? $this->appItemsPerPage : $itemsPerPage;

        // order
        $order = $request->query->get('order', JobPostingsFilters::ORDER_RELEVANCE);

        // 1. build es query
        $qb = $this->jobPostingsQueryBuilder->createQueryBuilder();

        // where
        $qb
            ->addStatusFilter(Status::PUBLISHED)
            ->addContractFilter(Contract::getTurnoverValues())
            ->addRemoteFilter($remoteMode)
            ->addDailySalaryFilter($minDailySalary, $maxDailySalary)
            ->addMinDurationFilter($minDuration)
            ->addMaxDurationFilter($maxDuration)
            ->addPublishedSinceFilter($publishedSince)
            ->addLocationKeysFilter($locations)
            ->addKeywordFilter($keywords)
            ->addCompanyBusinessActivityFilter($businessActivity)
        ;

        if (true === $intercontractOnly) {
            $qb->addContractFilter([Contract::INTERCONTRACT], true);
        }

        // order
        self::withOrderByFilters($qb, $order);

        return $qb->getPaginator($page, $itemsPerPage);
    }

    public function countTurnoverSearch(Request $request): int
    {
        return (int) $this->getPaginatorTurnoverSearch($request)
            ->getTotalItems()
        ;
    }

    private static function withOrderByFilters(JobPostingsQueryBuilder $query, string $orderFilter): void
    {
        if (JobPostingsFilters::ORDER_RELEVANCE === $orderFilter) {
            $query->getQuery()->addSort(['_score' => ['order' => 'desc']]);
            $query->getQuery()->addSort(['publishedAt' => ['order' => 'desc']]);
            $query->getQuery()->addSort(['id' => ['order' => 'desc']]);
        } elseif (JobPostingsFilters::ORDER_DATE === $orderFilter) {
            $query->getQuery()->addSort(['publishedAt' => ['order' => 'desc']]);
        } elseif (JobPostingsFilters::ORDER_MIN_DAILY_SALARY === $orderFilter) {
            $query->getQuery()->addSort(['minDailySalary' => ['order' => 'asc']]);
            $query->getQuery()->addSort(['maxDailySalary' => ['order' => 'asc']]);
            $query->getQuery()->addSort(['publishedAt' => ['order' => 'desc']]);
        }
    }

    public function findSearch(JobPostingsFilters $filters, ?int $page = null, ?int $itemsPerPage = null): \ArrayIterator
    {
        return $this->getPaginatorSearch($filters, $page, $itemsPerPage)
            ->getIterator()
        ;
    }

    public function countSearch(JobPostingsFilters $filters): int
    {
        return (int) $this->getPaginatorSearch($filters)
            ->getTotalItems()
        ;
    }

    public function findForFeed(FeedRss $feedRss): array
    {
        $query = $this
            ->createQueryBuilder('jp', 'jp.id')
            ->where('jp.published = true')
            ->orderBy('jp.publishedAt', Criteria::DESC)
        ;

        self::withContractsQueryPart($query, (string) $feedRss->getType());

        if (false === $feedRss->getBlacklistCompanies()->isEmpty()) {
            self::withBlacklistCompaniesQueryPart($query, $feedRss->getBlacklistCompanies());
        }

        return $query->getQuery()->getResult();
    }

    public function findForFeedByRegionSlug(FeedRss $feedRss, string $regionSlug): array
    {
        $query = $this
            ->createQueryBuilder('jp', 'jp.id')
            ->where('jp.published = true')
            ->orderBy('jp.publishedAt', Criteria::DESC)
        ;

        self::withContractsQueryPart($query, (string) $feedRss->getType());
        self::withRegionSlugQueryPart($query, $regionSlug);

        if (false === $feedRss->getBlacklistCompanies()->isEmpty()) {
            self::withBlacklistCompaniesQueryPart($query, $feedRss->getBlacklistCompanies());
        }

        return $query->getQuery()->getResult();
    }

    public function findForFeedByCountryCode(FeedRss $feedRss, string $countryCode, ?int $limit = null): array
    {
        $query = $this
            ->createQueryBuilder('jp', 'jp.id')
            ->where('jp.published = true')
            ->orderBy('jp.publishedAt', Criteria::DESC)
        ;

        self::withContractsQueryPart($query, (string) $feedRss->getType());
        self::withCountryCodeQueryPart($query, $countryCode);

        if (false === $feedRss->getBlacklistCompanies()->isEmpty()) {
            self::withBlacklistCompaniesQueryPart($query, $feedRss->getBlacklistCompanies());
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

    public function findMostViewed(FeedRss $feedRss, int $limit): array
    {
        $mostViewedJobPostingIds = $this->_em->createQueryBuilder()
            ->select('count(jbt) as HIDDEN count, jb.id')
            ->from(JobPostingUserTrace::class, 'jbt')
            ->join('jbt.jobPosting', 'jb')
            ->where('jb.publishedAt is not null')
            ->groupBy('jbt.jobPosting')
            ->orderBy('count', Criteria::DESC)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

        $mostViewedJobPostingIds = array_column($mostViewedJobPostingIds, 'id');
        $query = $this->createQueryBuilder('jp', 'jp.id')
            ->where('jp.id IN (:ids)')
            ->orderBy('FIELD(jp.id, :ids)')
            ->setParameter('ids', $mostViewedJobPostingIds)
        ;

        if (false === $feedRss->getBlacklistCompanies()->isEmpty()) {
            self::withBlacklistCompaniesQueryPart($query, $feedRss->getBlacklistCompanies());
        }

        return $query->getQuery()->getResult();
    }

    protected static function withContractsQueryPart(QueryBuilder $query, string $type): void
    {
        $contracts = FeedRssType::isContractor($type)
            ? Contract::getFreeValues()
            : Contract::getWorkValues();

        $orStatements = $query->expr()->orX();
        foreach ($contracts as $key => $contract) {
            $arg = 'contract' . $key;
            $orStatements->add(sprintf('JSON_CONTAINS(jp.contracts, :%s) = 1', $arg));
            $query->setParameter($arg, sprintf('"%s"', $contract));
        }

        $query->andWhere($orStatements);
    }

    protected static function withBlacklistCompaniesQueryPart(QueryBuilder $query, Collection $companies): void
    {
        $feedRssBlacklistCompanyWhere = sprintf(
            'c.id NOT IN (%s)',
            implode(
                ',',
                array_map(static function (FeedRssBlacklistCompany $feedRssBlacklistCompany) {
                    return null !== $feedRssBlacklistCompany->getCompany() ? $feedRssBlacklistCompany->getCompany()->getId() : 0;
                }, $companies->getValues())
            )
        );

        $query
            ->join('jp.company', 'c')
            ->andWhere($feedRssBlacklistCompanyWhere)
        ;
    }

    protected static function withRegionSlugQueryPart(QueryBuilder $query, string $regionSlug): void
    {
        $query
            ->andWhere('jp.location.adminLevel1Slug = :regionSlug')
            ->setParameter('regionSlug', $regionSlug)
        ;
    }

    protected static function withCountryCodeQueryPart(QueryBuilder $query, string $countryCode): void
    {
        $query
            ->andWhere('jp.location.countryCode = :countryCode')
            ->setParameter('countryCode', $countryCode)
        ;
    }

    public function findWithSkills(): array
    {
        $query = $this->createQueryBuilder('jp')
            ->join('jp.skills', 's')
            ->where('s.id IS NOT NULL')
        ;

        return $query->getQuery()->getResult();
    }

    public function findDepractedDraftToDelete(): array
    {
        $lessThirtyDays = (new \DateTime())->modify('-30 days');
        $query = $this->createQueryBuilder('j')
            ->select('j')
            ->where('j.updatedAt < :lessThirtyDays')
            ->andWhere('j.status = :status')
            ->setParameters([
                'lessThirtyDays' => $lessThirtyDays,
                'status' => Status::DRAFT,
            ])
        ;

        return $query->getQuery()->getResult();
    }

    public function findToPushToTop(): array
    {
        $date = Carbon::now()->subWeek()->endOfDay();
        $query = $this->createQueryBuilder('j')
            ->select('j')
            ->where('j.publishedAt < :date')
            ->andWhere('j.status = :status')
            ->andWhere('j.pushToTop = :pushToTop')
            ->andWhere('j.pushedToTopCount = :pushedToTopCount')
            ->setParameters([
                'date' => $date,
                'status' => Status::PUBLISHED,
                'pushToTop' => true,
                'pushedToTopCount' => 0,
            ])
        ;

        return $query->getQuery()->getResult();
    }
}
