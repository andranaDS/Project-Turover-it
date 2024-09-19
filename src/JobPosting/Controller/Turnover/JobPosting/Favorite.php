<?php

namespace App\JobPosting\Controller\Turnover\JobPosting;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingRecruiterFavorite;
use App\Recruiter\Entity\Recruiter;
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
        if (null === $user = $this->security->getUser()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        if (null !== $jobPostingRecruiterFavorite = $this->em->getRepository(JobPostingRecruiterFavorite::class)->findOneBy([
            'jobPosting' => $data,
            'recruiter' => $user,
        ])) {
            $this->em->remove($jobPostingRecruiterFavorite);
            $status = Response::HTTP_NO_CONTENT;
        } else {
            // We need to fetch the job from the database to persist it, as it is load from elastic
            $job = $this->em->getRepository(JobPosting::class)->findOneById($data->getId());
            $recruiter = $this->em->getRepository(Recruiter::class)->findOneBy([
                'email' => $user->getUserIdentifier(),
            ]);

            $jobPostingRecruiterFavorite = (new JobPostingRecruiterFavorite())
                    ->setRecruiter($recruiter)
                    ->setJobPosting($job)
            ;

            $this->em->persist($jobPostingRecruiterFavorite);

            $status = Response::HTTP_CREATED;
        }

        $this->em->flush();

        return new Response(status: $status);
    }
}
