<?php

namespace App\Company\Controller\Turnover\Site;

use App\Company\Entity\Site;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class Delete
{
    public function __invoke(EntityManagerInterface $em, Security $security, Site $data, TranslatorInterface $translator): Response
    {
        $countRecruiters = $em->getRepository(Recruiter::class)->count(['site' => $data]);

        if ($countRecruiters > 0) {
            return new JsonResponse(['hydra:title' => $translator->trans('site.delete.error.count_recruiter')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data->setName(null)
            ->setIp(null)
            ->setDeletedAt(new \DateTime())
        ;

        $em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
