<?php

namespace App\JobPosting\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Entity\Location;
use App\JobPosting\Entity\JobPostingSearchRecruiterLog;
use App\JobPosting\Entity\JobPostingSearchRecruiterLogLocation;
use App\JobPosting\Transformer\JobPostingSearchRecruiterLogDto;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class JobPostingSearchRecruiterLogSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
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

        if ('api_job_postings_turnover_get_collection' !== $request->attributes->get('_route')) {
            return;
        }

        if (null === $recruiter = $this->security->getUser()) {
            return;
        }

        if (!$recruiter instanceof Recruiter) {
            return;
        }

        $queryParams = $request->query->all();
        $jobPostingSearchRecruiterLogDto = new JobPostingSearchRecruiterLogDto($this->em, $queryParams);
        $jobPostingSearchRecruiterLog = (new JobPostingSearchRecruiterLog())
            ->setKeywords($jobPostingSearchRecruiterLogDto->getKeywords())
            ->setIntercontractOnly($jobPostingSearchRecruiterLogDto->getIntercontractOnly())
            ->setMinDuration($jobPostingSearchRecruiterLogDto->getMinDuration())
            ->setMaxDuration($jobPostingSearchRecruiterLogDto->getMaxDuration())
            ->setMinDailySalary($jobPostingSearchRecruiterLogDto->getMinDailySalary())
            ->setMaxDailySalary($jobPostingSearchRecruiterLogDto->getMaxDailySalary())
            ->setStartsAt($jobPostingSearchRecruiterLogDto->getStartsAt())
            ->setRemoteMode($jobPostingSearchRecruiterLogDto->getRemoteMode())
            ->setPublishedSince($jobPostingSearchRecruiterLogDto->getPublishedSince())
            ->setBusinessActivity($jobPostingSearchRecruiterLogDto->getBusinessActivity())
            ->setRecruiter($recruiter)
        ;

        if (null !== $locations = $jobPostingSearchRecruiterLogDto->getLocations()) {
            foreach ($locations as $location) {
                $locationEntity = (new Location())
                    ->setStreet($location['street'])
                    ->setLocality($location['locality'])
                    ->setPostalCode($location['postalCode'])
                    ->setAdminLevel1($location['adminLevel1'])
                    ->setAdminLevel2($location['adminLevel2'])
                    ->setCountry($location['country'])
                    ->setCountryCode($location['countryCode'])
                    ->setLatitude($location['latitude'])
                    ->setLongitude($location['longitude'])
                ;

                $jobPostingSearchRecruiterLogLocation = (new JobPostingSearchRecruiterLogLocation())
                    ->setLocation($locationEntity)
                ;

                $jobPostingSearchRecruiterLog->addLocation($jobPostingSearchRecruiterLogLocation);
            }
        }

        $this->em->persist($jobPostingSearchRecruiterLog);
        $this->em->flush();
    }
}
