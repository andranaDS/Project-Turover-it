<?php

namespace App\Company\Controller\FreeWork\Company;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyBlacklist;
use App\Company\Entity\CompanyUserFavorite;
use App\User\Contracts\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class Favorite
{
    public function __invoke(Company $data, Security $security, EntityManagerInterface $em): Response
    {
        if ((null === $user = $security->getUser()) || !$user instanceof UserInterface) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        if (null !== $companyFavorite = $em->getRepository(CompanyUserFavorite::class)->findOneBy([
            'company' => $data,
            'user' => $user,
        ])) {
            // remove from favorites
            $em->remove($companyFavorite);
            $status = Response::HTTP_NO_CONTENT;
        } else {
            // add to favorites
            $companyFavorite = (new CompanyUserFavorite())
                ->setUser($user)
                ->setCompany($data)
            ;
            $em->persist($companyFavorite);

            // remove from blacklists if necessary
            if (null !== $companyBlacklist = $em->getRepository(CompanyBlacklist::class)->findOneBy([
                'company' => $data,
                'user' => $user,
            ])) {
                $em->remove($companyBlacklist);
            }

            $status = Response::HTTP_CREATED;
        }

        $em->flush();

        return new Response(status: $status);
    }
}
