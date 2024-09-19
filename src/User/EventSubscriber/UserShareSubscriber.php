<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Entity\SoftSkill;
use App\Core\Mailer\Mailer;
use App\User\Email\UserShareEmail;
use App\User\Entity\User;
use App\User\Entity\UserMobility;
use App\User\Entity\UserShare;
use App\User\Entity\UserSkill;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserShareSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', EventPriorities::POST_WRITE],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $userShare = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $route = $event->getRequest()->get('_route');

        if (!$userShare instanceof UserShare || Request::METHOD_POST !== $method || 'api_user_shares_turnover_post_user_share_collection' !== $route) {
            return;
        }

        /** @var User $user */
        $user = $userShare->getUser();

        $email = (new UserShareEmail())
            ->to((string) $userShare->getEmail())
            ->setVariables([
                'cv_title' => $user->getProfileJobTitle(),
                'studies_level' => $user->getFormation()?->getDiplomaLevel(),
                'experience' => $user->getExperienceYear(),
                'hard_skills' => implode(', ', array_filter($user->getSkills()->map(function (UserSkill $userSkill) {
                    return $userSkill->getSkill()?->getName();
                })->getValues())),
                'soft_skills' => implode(', ', array_filter($user->getSoftSkills()->map(function (SoftSkill $softSkill) {
                    return $softSkill->getName();
                })->getValues())),
                'location' => '',
                'mobility' => implode(', ', array_filter($user->getLocations()->map(function (UserMobility $userMobility) {
                    return $userMobility->getLocation()?->getLabel();
                })->getValues())),
                'link' => '#',
                'company' => $userShare->getSharedBy()?->getCompany()?->getName(),
                'user_email' => $userShare->getSharedBy()?->getEmail(),
            ])
        ;

        $this->mailer->send($email);
    }
}
