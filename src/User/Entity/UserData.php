<?php

namespace App\User\Entity;

use App\User\Repository\UserDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserDataRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"last_activity_at"}),
 *     @ORM\Index(columns={"last_forum_activity_at"}),
 *     @ORM\Index(columns={"cron_alert_missions_exec_at"}),
 *     @ORM\Index(columns={"cron_no_job_posting_search_exec_at"}),
 *     @ORM\Index(columns={"cron_profile_uncompleted_exec_at"}),
 *     @ORM\Index(columns={"cron_profile_not_visible_exec_at"}),
 *     @ORM\Index(columns={"cron_no_immediate_availability_exec_at"}),
 * })
 */
class UserData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTimeInterface $lastActivityAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTimeInterface $lastForumActivityAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTimeInterface $cronAlertMissionsExecAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTimeInterface $cronNoJobPostingSearchExecAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTimeInterface $cronProfileUncompletedExecAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTimeInterface $cronProfileNotVisibleExecAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?\DateTimeInterface $cronNoImmediateAvailabilityExecAt;

    public function __construct()
    {
        $this->lastActivityAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastActivityAt(): ?\DateTimeInterface
    {
        return $this->lastActivityAt;
    }

    public function setLastActivityAt(?\DateTimeInterface $lastActivityAt): self
    {
        $this->lastActivityAt = $lastActivityAt;

        return $this;
    }

    public function getLastForumActivityAt(): ?\DateTimeInterface
    {
        return $this->lastForumActivityAt;
    }

    public function setLastForumActivityAt(?\DateTimeInterface $lastForumActivityAt): self
    {
        $this->lastForumActivityAt = $lastForumActivityAt;

        return $this;
    }

    public function getCronAlertMissionsExecAt(): ?\DateTimeInterface
    {
        return $this->cronAlertMissionsExecAt;
    }

    public function setCronAlertMissionsExecAt(?\DateTimeInterface $execAt): self
    {
        $this->cronAlertMissionsExecAt = $execAt;

        return $this;
    }

    public function getCronNoJobPostingSearchExecAt(): ?\DateTimeInterface
    {
        return $this->cronNoJobPostingSearchExecAt;
    }

    public function setCronNoJobPostingSearchExecAt(?\DateTimeInterface $execAt): self
    {
        $this->cronNoJobPostingSearchExecAt = $execAt;

        return $this;
    }

    public function getCronProfileUncompletedExecAt(): ?\DateTimeInterface
    {
        return $this->cronProfileUncompletedExecAt;
    }

    public function setCronProfileUncompletedExecAt(?\DateTimeInterface $execAt): self
    {
        $this->cronProfileUncompletedExecAt = $execAt;

        return $this;
    }

    public function getCronProfileNotVisibleExecAt(): ?\DateTimeInterface
    {
        return $this->cronProfileNotVisibleExecAt;
    }

    public function setCronProfileNotVisibleExecAt(?\DateTimeInterface $cronProfileNotVisibleExecAt): self
    {
        $this->cronProfileNotVisibleExecAt = $cronProfileNotVisibleExecAt;

        return $this;
    }

    public function getCronNoImmediateAvailabilityExecAt(): ?\DateTimeInterface
    {
        return $this->cronNoImmediateAvailabilityExecAt;
    }

    public function setCronNoImmediateAvailabilityExecAt(?\DateTimeInterface $cronNoImmediateAvailabilityExecAt): self
    {
        $this->cronNoImmediateAvailabilityExecAt = $cronNoImmediateAvailabilityExecAt;

        return $this;
    }
}
