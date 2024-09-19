<?php

namespace App\Recruiter\Controller\Turnover\Recruiter;

use App\Folder\Manager\FolderManager;
use App\Recruiter\Entity\Recruiter;
use Carbon\Carbon;

final class PostItem
{
    public function __invoke(Recruiter $data, FolderManager $folderManager): Recruiter
    {
        $data
            ->setMain(true)
            ->setLoggedAt(Carbon::now())
        ;

        $folderManager->generateFolders($data);

        return $data;
    }
}
