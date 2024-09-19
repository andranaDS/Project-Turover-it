<?php

namespace App\User\Contracts;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface as BaseUser;

/**
 * Interface UserInterface - A user as it should be in this application.
 */
interface UserInterface extends BaseUser
{
    public function getId(): ?int;

    public function getEmail(): ?string;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getNickname(): ?string;

    public function getEnabled(): ?bool;

    public function setForumPostUpvotesCount(int $forumPostUpvotesCount): self;

    public function setForumPostsCount(int $forumPostsCount): self;

    public function getSkills(): Collection;

    public function getJobs(): Collection;

    public function getGrossAnnualSalary(): ?int;

    public function getAverageDailyRate(): ?int;

    public function getLocations(): Collection;

    public function getFulltimeTeleworking(): ?bool;

    public function getContracts(): ?array;
}
