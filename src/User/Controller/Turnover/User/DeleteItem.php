<?php

namespace App\User\Controller\Turnover\User;

use App\JobPosting\Entity\Application;
use App\JobPosting\Enum\ApplicationState;
use App\Recruiter\Entity\Recruiter;
use App\User\Entity\User;
use App\User\Manager\UserManager;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

final class DeleteItem
{
    public function __invoke(User $data, Security $security, EntityManagerInterface $em, UserManager $um): Response
    {
        if (!($security->getUser()) instanceof Recruiter) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $applications = $em->getRepository(Application::class)->findBy(['user' => $data]);
        foreach ($applications as $application) {
            /* @var Application $application */
            $application->setState(ApplicationState::CANCELLED);
        }

        $response = new Response(status: Response::HTTP_NO_CONTENT);

        $data->setDeletedAt(Carbon::now());

        $em->flush();

        return $response;
    }
}
