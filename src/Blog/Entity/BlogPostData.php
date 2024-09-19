<?php

namespace App\Blog\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Blog\Repository\BlogPostDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BlogPostDataRepository::class)
 *
 * @ApiResource (
 *      itemOperations={
 *          "freework_get"={
 *              "method"="GET",
 *              "condition"= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
 *              "normalization_context"={"groups"={"blog_post_data:get"}},
 *              "cache_headers"={"max_age"=0, "shared_max_age"=0},
 *          }
 *     },
 *     collectionOperations={},
 * )
 *
 * @ApiFilter(PropertyFilter::class)
 */
class BlogPostData
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", options={"unsigned"=true}, unique=true)
     * @ApiProperty(identifier=true)
     * @Groups({"blog_post_data:get"})
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"blog_post_data:get"})
     */
    private int $upvotesCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"blog_post_data:get"})
     */
    private int $viewsCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"blog_post_data:get"})
     */
    private int $recentViewsCount = 0;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUpvotesCount(): int
    {
        return $this->upvotesCount;
    }

    public function setUpvotesCount(int $upvotesCount): self
    {
        $this->upvotesCount = $upvotesCount;

        return $this;
    }

    public function getRecentViewsCount(): int
    {
        return $this->recentViewsCount;
    }

    public function setRecentViewsCount(int $recentViewsCount): self
    {
        $this->recentViewsCount = $recentViewsCount;

        return $this;
    }

    public function getViewsCount(): int
    {
        return $this->viewsCount;
    }

    public function setViewsCount(int $viewsCount): self
    {
        $this->viewsCount = $viewsCount;

        return $this;
    }
}
