<?php

namespace App\Notification\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\JobPosting\DataFixtures\ApplicationFixtures;
use App\JobPosting\DataFixtures\JobPostingsFixtures;
use App\JobPosting\DataFixtures\JobPostingTemplatesFixtures;
use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\JobPosting;
use App\Notification\Entity\Notification;
use App\Notification\Enum\NotificationEvent;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\Recruiter\Entity\Recruiter;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class NotificationsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $jobPostings = [];
    private array $applications = [];
    private array $recruiters = [];

    public function load(ObjectManager $manager): void
    {
        $recruiterIds = [];

        // fetch jobPostings
        foreach ($manager->getRepository(JobPosting::class)->findAll() as $jobPosting) {
            $recruiterIds[] = $jobPosting->getAssignedTo()?->getId();
            $this->jobPostings[$jobPosting->getId()] = $jobPosting;
        }

        // fetch applications
        foreach ($manager->getRepository(Application::class)->findAll() as $application) {
            $this->applications[$application->getId()] = $application;
        }

        // fetch recruiters
        $recruiterIds = array_values(array_unique(array_filter($recruiterIds)));
        foreach ($manager->getRepository(Recruiter::class)->findById($recruiterIds) as $recruiter) {
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
        }

        $batchSize = 500;
        $i = 0;

        $data = $this->getData();

        // order by createdAt
        usort($data, static function (array $d1, array $d2) {
            return $d1['createdAt']->getTimestamp() <=> $d2['createdAt']->getTimestamp();
        });

        foreach ($data as $d) {
            $notification = (new Notification())
                ->setRecruiter($d['recruiter'])
                ->setJobPosting($d['jobPosting'])
                ->setApplication($d['application'])
                ->setCreatedAt($d['createdAt'])
                ->setEvent($d['event'])
                ->setReadAt($d['readAt'])
            ;

            $manager->persist($notification);

            if (0 === ++$i % $batchSize) {
                $manager->flush();
            }
        }
        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        $faker = Faker::create('fr_FR');

        $notificationsCount = 10000;
        for ($i = 0; $i < $notificationsCount; ++$i) {
            $event = Arrays::getRandom(NotificationEvent::getConstants());
            $recruiter = Arrays::getRandom($this->recruiters);
            $jobPosting = $application = null;

            if (NotificationEvent::JOB_POSTING_EXPIRING_SOON === $event) {
                $jobPosting = Arrays::getRandom(array_filter($this->jobPostings, static function (JobPosting $jp) use ($recruiter) {
                    return $jp->getAssignedTo()?->getId() === $recruiter->getId();
                }));
            } elseif (\in_array($event, [NotificationEvent::APPLICATION_ABANDONED, NotificationEvent::APPLICATION_NEW], true)) {
                $application = Arrays::getRandom(array_filter($this->applications, static function (Application $a) use ($recruiter) {
                    return $a->getJobPosting()?->getCreatedBy()?->getId() === $recruiter->getId();
                }));
            }

            $date = $faker->dateTimeBetween('-2 years');
            $data[] = [
                'recruiter' => $recruiter,
                'jobPosting' => $jobPosting,
                'application' => $application,
                'createdAt' => $date,
                'readAt' => $date,
                'event' => $event,
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'recruiter' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                'jobPosting' => null,
                'application' => $this->applications[5],
                'createdAt' => new Carbon('2022-03-01 10:00:00'),
                'readAt' => new Carbon('2022-03-01 11:00:00'),
                'event' => NotificationEvent::APPLICATION_NEW,
            ], [
                'recruiter' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                'jobPosting' => null,
                'application' => null,
                'createdAt' => new Carbon('2022-04-01 10:00:00'),
                'readAt' => null,
                'event' => NotificationEvent::SUBSCRIPTION_ENDING_SOON,
            ],
            [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'jobPosting' => null,
                'application' => $this->applications[1],
                'createdAt' => Carbon::today()->subDays(60)->setTime(10, 00, 00),
                'readAt' => Carbon::today()->subDays(60)->setTime(10, 30, 00),
                'event' => NotificationEvent::APPLICATION_NEW,
            ],
            [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'jobPosting' => $this->jobPostings[37],
                'application' => null,
                'createdAt' => Carbon::today()->subDays(10)->setTime(10, 00, 00),
                'readAt' => Carbon::today()->subDays(10)->setTime(10, 30, 00),
                'event' => NotificationEvent::JOB_POSTING_EXPIRING_SOON,
            ], [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'jobPosting' => null,
                'application' => null,
                'createdAt' => Carbon::today()->subDays(4)->setTime(10, 00, 00),
                'readAt' => Carbon::today()->subDays(4)->setTime(10, 30, 00),
                'event' => NotificationEvent::SUBSCRIPTION_ENDING_SOON,
            ],
            [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'jobPosting' => null,
                'application' => null,
                'createdAt' => Carbon::yesterday()->setTime(12, 00, 00),
                'readAt' => null,
                'event' => NotificationEvent::SUBSCRIPTION_ENDING_SOON,
            ],
            [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'jobPosting' => null,
                'application' => $this->applications[4],
                'createdAt' => Carbon::today()->setTime(00, 30, 00),
                'readAt' => null,
                'event' => NotificationEvent::APPLICATION_ABANDONED,
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            ApplicationFixtures::class,
            RecruiterFixtures::class,
            JobPostingsFixtures::class,
            JobPostingTemplatesFixtures::class,
        ];
    }
}
