<?php

namespace App\JobPosting\Controller\FreeWork\JobPosting;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingUserFavorite;
use App\User\Contracts\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class Favorite
{
    private Security $security;
    private EntityManagerInterface $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(JobPosting $data): Response
    {
        if ((null === $user = $this->security->getUser()) || !$user instanceof UserInterface) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        if (null !== $jobPostingFavorite = $this->em->getRepository(JobPostingUserFavorite::class)->findOneBy([
            'jobPosting' => $data,
            'user' => $user,
        ])) {
            $this->em->remove($jobPostingFavorite);
            $status = Response::HTTP_NO_CONTENT;
        } else {
            // We need to fetch the job from the database to persist it, as it is load from elastic
            $job = $this->em->getRepository(JobPosting::class)->findOneById($data->getId());

            $jobPostingFavorite = (new JobPostingUserFavorite())
                    ->setUser($user)
                    ->setJobPosting($job)
            ;

            $this->em->persist($jobPostingFavorite);

            $status = Response::HTTP_CREATED;
        }

        $this->em->flush();

        return new Response(status: $status);
    }
}
