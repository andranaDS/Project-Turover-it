<?php

namespace App\Company\Manager;

use App\Company\Entity\CompanyBlacklist;
use App\Company\Entity\CompanyUserFavorite;
use App\User\Contracts\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Arrays;

class CompanyDataManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUserCompanyData(UserInterface $user, array $scopes = []): array
    {
        $data = [];

        if (\in_array('company_favorites', $scopes, true)) {
            $data['company_favorites'] = $this->getUserCompanyFavorites($user);
        }
        if (\in_array('company_blacklists', $scopes, true)) {
            $data['company_blacklists'] = $this->getUserCompanyBlacklists($user);
        }

        return $data;
    }

    private function getUserCompanyFavorites(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(CompanyUserFavorite::class)->findCompanyIdByUser($user), static function (array $element) {
            return (int) $element['companyId'];
        });
    }

    private function getUserCompanyBlacklists(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(CompanyBlacklist::class)->findCompanyIdByUser($user), static function (array $element) {
            return (int) $element['companyId'];
        });
    }
}
