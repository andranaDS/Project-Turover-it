<?php

namespace App\Company\Security\Voter;

use App\Company\Entity\Site;
use App\Recruiter\Entity\Recruiter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class SiteVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        $supportsAttribute = \in_array($attribute, ['SITE_MINE', 'SITE_POST'], true);
        $supportsSubject = $subject instanceof Site;

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $recruiter = $token->getUser();

        if (!$recruiter instanceof Recruiter) {
            return false;
        }

        if (!$this->security->isGranted('ROLE_RECRUITER', $recruiter)) {
            return false;
        }

        return match ($attribute) {
            'SITE_MINE' => $this->isMine($recruiter, $subject),
            'SITE_POST' => $this->isPost($recruiter),
            default => false,
        };
    }

    protected function isMine(Recruiter $recruiter, Site $subject): bool
    {
        return $recruiter->isMain() && $recruiter->getCompany() === $subject->getCompany();
    }

    protected function isPost(Recruiter $recruiter): bool
    {
        return true === $recruiter->isMain();
    }
}
