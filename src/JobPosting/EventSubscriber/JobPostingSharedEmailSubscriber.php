<?php

namespace App\JobPosting\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Mailer\Mailer;
use App\JobPosting\Email\JobpostingSharedEmail;
use App\JobPosting\Entity\JobPostingShare;
use App\JobPosting\Enum\DurationPeriod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class JobPostingSharedEmailSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;
    private RouterInterface $router;
    private TranslatorInterface $translator;

    public function __construct(Mailer $mailer, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', EventPriorities::POST_WRITE],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $jobPostingShare = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $route = $event->getRequest()->get('_route');

        if (!$jobPostingShare instanceof JobPostingShare || Request::METHOD_POST !== $method || 'api_job_posting_shares_turnover_post_collection' !== $route) {
            return;
        }

        $durationPeriod = match ($jobPostingShare->getJobPosting()?->getDurationPeriod()) {
            DurationPeriod::DAY => $this->translator->trans('job_posting_share.duration_period.day'),
            DurationPeriod::MONTH => $this->translator->trans('job_posting_share.duration_period.month'),
            DurationPeriod::YEAR => $this->translator->trans('job_posting_share.duration_period.year'),
            default => null
        };

        $email = (new JobpostingSharedEmail())
            ->to((string) $jobPostingShare->getEmail())
            ->setVariables([
                'job_title' => $jobPostingShare->getJobPosting()?->getTitle(),
                'location' => $jobPostingShare->getJobPosting()?->getLocation()->getLocality(),
                'duration' => $jobPostingShare->getJobPosting()?->getDurationValue() . ' ' . $durationPeriod,
                'salary' => $jobPostingShare->getJobPosting()?->getMinDailySalary() . '-' . $jobPostingShare->getJobPosting()?->getMaxDailySalary() . '€',
                'start_date' => $jobPostingShare->getJobPosting()?->getStartsAt()?->format('d-m-Y'),
                'job_description' => $jobPostingShare->getJobPosting()?->getDescription() . ' ' . $jobPostingShare->getJobPosting()?->getCandidateProfile() . ' ' . $jobPostingShare->getJobPosting()?->getCompanyDescription(),
                'profile_description' => $jobPostingShare->getJobPosting()?->getCandidateProfile(),
                'company_description' => $jobPostingShare->getJobPosting()?->getCompanyDescription(),
                'contact' => $jobPostingShare->getJobPosting()?->getApplicationContact(),
                'link' => $this->router->generate('turnover_front_job_posting_share', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'company' => $jobPostingShare->getJobPosting()?->getCompany()?->getName(),
                'user_email' => $jobPostingShare->getSharedBy()?->getEmail(),
            ])
        ;

        $this->mailer->send($email);
    }
}
