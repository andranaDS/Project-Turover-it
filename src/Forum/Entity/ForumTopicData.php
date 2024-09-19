<?php

namespace App\Forum\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Forum\Controller\FreeWork\ForumTopicData\GetByTopics;
use App\Forum\Repository\ForumTopicDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ForumTopicDataRepository::class)
 *
 * @ApiResource (
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"forum_topic_data:get"}},
 *              "cache_headers"={"max_age"=0, "shared_max_age"=0},
 *          }
 *     },
 *     collectionOperations={
 *          "get_by_topics"={
 *              "method"="GET",
 *              "path"="/forum_topic_datas",
 *              "normalization_context"={"groups"={"forum_topic_data:get"}},
 *              "controller"=GetByTopics::class,
 *              "deserialize"=false,
 *           }
 *     },
 * )
 *
 * @ApiFilter(PropertyFilter::class)
 */
class ForumTopicData
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", options={"unsigned"=true}, unique=true)
     * @ApiProperty(identifier=true)
     * @Groups({"forum_topic_data:get"})
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"forum_topic_data:get"})
     */
    private int $postsCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"forum_topic_data:get"})
     */
    private int $repliesCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"forum_topic_data:get"})
     */
    private int $viewsCount = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"forum_topic_data:get"})
     */
    private int $upvotesCount = 0;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPostsCount(): ?int
    {
        return $this->postsCount;
    }

    public function setPostsCount(int $postsCount): self
    {
        $this->postsCount = $postsCount;

        return $this;
    }

    public function getRepliesCount(): ?int
    {
        return $this->repliesCount;
    }

    public function setRepliesCount(int $repliesCount): self
    {
        $this->repliesCount = $repliesCount;

        return $this;
    }

    public function getViewsCount(): ?int
    {
        return $this->viewsCount;
    }

    public function setViewsCount(int $viewsCount): self
    {
        $this->viewsCount = $viewsCount;

        return $this;
    }

    public function getUpvotesCount(): ?int
    {
        return $this->upvotesCount;
    }

    public function setUpvotesCount(int $upvotesCount): self
    {
        $this->upvotesCount = $upvotesCount;

        return $this;
    }
}
