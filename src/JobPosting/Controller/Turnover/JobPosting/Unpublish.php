<?php

namespace App\JobPosting\Controller\Turnover\JobPosting;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class Unpublish
{
    public function __invoke(EntityManagerInterface $em, Security $security, JobPosting $data): JobPosting
    {
        $data->setStatus(Status::INACTIVE);
        $em->persist($data);

        return $data;
    }
}
