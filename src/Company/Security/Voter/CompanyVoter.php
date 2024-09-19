<?php

namespace App\Company\Security\Voter;

use App\Company\Entity\Company;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CompanyVoter extends Voter
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
        $supportsAttribute = \in_array($attribute, ['COMPANY_ME', 'COMPANY_MINE'], true);
        $supportsSubject = $subject instanceof Company || null === $subject;

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

        if (null === $subject) {
            $request = $this->requestStack->getCurrentRequest();
            $subject = $this->em->getRepository(Company::class)->findOneBySlug($request?->attributes->get('slug'));

            if (null === $subject) {
                return false;
            }
        }

        return match ($attribute) {
            'COMPANY_ME' => $this->isMe($recruiter, $subject),
            'COMPANY_MINE' => $this->isMine($recruiter, $subject),
            default => false,
        };
    }

    protected function isMe(Recruiter $recruiter, Company $subject): bool
    {
        return $recruiter->getCompany() === $subject;
    }

    protected function isMine(Recruiter $recruiter, Company $subject): bool
    {
        return $recruiter->getCompany() === $subject;
    }
}
