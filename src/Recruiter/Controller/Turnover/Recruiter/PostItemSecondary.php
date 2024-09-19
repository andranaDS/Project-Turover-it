<?php

namespace App\Recruiter\Controller\Turnover\Recruiter;

use App\Folder\Manager\FolderManager;
use App\Recruiter\Entity\Recruiter;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

final class PostItemSecondary
{
    public function __invoke(?UserInterface $recruiter, Recruiter $data, FolderManager $folderManager): Recruiter
    {
        if (!$recruiter instanceof Recruiter) {
            throw new AuthenticationException();
        }

        $data
            ->setPasswordUpdateRequired(true)
            ->setCompany($recruiter->getCompany())
        ;

        $folderManager->generateFolders($data);

        return $data;
    }
}
