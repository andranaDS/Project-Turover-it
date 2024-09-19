<?php

namespace App\Company\Controller\FreeWork\Company;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Company\Repository\CompanyRepository;
use Symfony\Component\HttpFoundation\Request;

class GetHomepage
{
    public function __invoke(Request $request, CompanyRepository $companyRepository, int $itemsPerPageDefault): Paginator
    {
        $page = (int) $request->query->get('page', '1');
        $itemsPerPage = (int) $request->query->get('itemsPerPage', (string) $itemsPerPageDefault);

        return $companyRepository->getHomepageCompanies($page, $itemsPerPage);
    }
}
