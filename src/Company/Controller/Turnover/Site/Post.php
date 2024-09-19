<?php

namespace App\Company\Controller\Turnover\Site;

use App\Company\Entity\Site;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class Post
{
    public function __invoke(EntityManagerInterface $em, Security $security, Site $data): Site
    {
        /** @var Recruiter $recruiter */
        $recruiter = $security->getUser();

        $data->setCompany($recruiter->getCompany());

        return $data;
    }
}
