<?php

namespace App\Folder\Manager;

use App\Folder\Entity\Folder;
use App\Folder\Enum\FolderType;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;

class FolderManager
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function generateFolders(Recruiter $recruiter): void
    {
        $folders = $this->em->getRepository(Folder::class)->findBy(['recruiter' => $recruiter]);

        if (empty($folders)) {
            foreach (FolderType::getMandatoryTypes() as $type) {
                $folder = (new Folder())
                    ->setType($type)
                    ->setRecruiter($recruiter)
                ;

                $this->em->persist($folder);
            }
        }
    }
}
