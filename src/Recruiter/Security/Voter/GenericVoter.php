<?php

namespace App\Recruiter\Security\Voter;

use App\Recruiter\Entity\Recruiter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class GenericVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, ['RECRUITER_MAIN', 'RECRUITER_SECONDARY'], true);
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

        return match ($attribute) {
            'RECRUITER_MAIN' => $this->isMain($recruiter),
            'RECRUITER_SECONDARY' => $this->isSecondary($recruiter, $subject),
            default => false,
        };
    }

    protected function isMain(Recruiter $recruiter): bool
    {
        return (bool) $recruiter->isMain();
    }

    protected function isSecondary(Recruiter $recruiter, Recruiter $subject): bool
    {
        return (bool) $recruiter->isSecondary();
    }
}
