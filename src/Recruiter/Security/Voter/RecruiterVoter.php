<?php

namespace App\Recruiter\Security\Voter;

use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class RecruiterVoter extends Voter
{
    private Security $security;
    private EntityManagerInterface $em;
    private RequestStack $requestStack;

    public function __construct(Security $security, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    protected function supports(string $attribute, $subject): bool
    {
        $supportsAttribute = \in_array($attribute, ['RECRUITER_ME', 'RECRUITER_MINE'], true);
        $supportsSubject = $subject instanceof Recruiter || null === $subject;

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $recruiter = $token->getUser();

        if (!$recruiter instanceof Recruiter) {
            return false;
        }

        if (!$this->security->isGranted('ROLE_RECRUITER', $recruiter)) {
            return false;
        }

        if (null === $subject) {
            $request = $this->requestStack->getCurrentRequest();
            $subject = $this->em->getRepository(Recruiter::class)->findOneById($request?->attributes->get('id'));

            if (null === $subject) {
                return false;
            }
        }

        return match ($attribute) {
            'RECRUITER_ME' => $this->isMe($recruiter, $subject),
            'RECRUITER_MINE' => $this->isMine($recruiter, $subject),
            default => false,
        };
    }

    protected function isMe(Recruiter $recruiter, Recruiter $subject): bool
    {
        return $recruiter === $subject;
    }

    protected function isMine(Recruiter $recruiter, Recruiter $subject): bool
    {
        return $recruiter->isMain() && $subject->isSecondary() && $recruiter->getCompany() === $subject->getCompany();
    }
}
