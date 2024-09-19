<?php

namespace App\Blog\Manager;

use App\Blog\Entity\BlogPostUpvote;
use App\User\Contracts\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Arrays;

class BlogDataManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUserBlogData(UserInterface $user, array $scopes = []): array
    {
        $data = [];

        if (\in_array('blog_post_upvotes', $scopes, true)) {
            $data['blog_post_upvotes'] = $this->getUserBlogPostPostUpvotes($user);
        }

        return $data;
    }

    private function getUserBlogPostPostUpvotes(UserInterface $user): array
    {
        return Arrays::map($this->em->getRepository(BlogPostUpvote::class)->findPostIdByUser($user), static function (array $element) {
            return (int) $element['postId'];
        });
    }
}
