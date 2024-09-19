<?php

namespace App\Company\Controller\Turnover\Company;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyRecruiterFavorite;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class Favorite
{
    public function __invoke(Company $data, Security $security, EntityManagerInterface $em): Response
    {
        if ((null === $recruiter = $security->getUser()) || !$recruiter instanceof Recruiter) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        if (null !== $companyFavorite = $em->getRepository(CompanyRecruiterFavorite::class)->findOneBy([
            'company' => $data,
            'recruiter' => $recruiter,
        ])) {
            // remove from favorites
            $em->remove($companyFavorite);
            $status = Response::HTTP_NO_CONTENT;
        } else {
            // add to favorites
            $companyFavorite = (new CompanyRecruiterFavorite())
                ->setCompany($data)
            ;
            $em->persist($companyFavorite);

            $status = Response::HTTP_CREATED;
        }

        $em->flush();

        return new Response(status: $status);
    }
}
