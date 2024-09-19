<?php

namespace App\Forum\Manager;

use App\Forum\Entity\ForumPost;
use Doctrine\ORM\EntityManagerInterface;

class ForumPostManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function delete(ForumPost $forumPost): void
    {
        $forumPost
            ->setContentJson(null)
            ->setContentHtml(null)
            ->setDeletedAt(new \DateTime())
        ;

        if (0 === $forumPost->getChildren()->count()) {
            $forumPost->setHidden(true);
        }

        $this->em->flush();
    }
}
