<?php

namespace App\Recruiter\Entity;

use App\Recruiter\Repository\RecruiterNotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RecruiterNotificationRepository::class)
 */
class RecruiterNotification
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $newApplicationEmail = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $newApplicationNotification = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $endBroadcastJobPostingEmail = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $endBroadcastJobPostingNotification = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $dailyResumeEmail = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $dailyJobPostingEmail = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $jobPostingPublishATSEmail = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $jobPostingPublishATSNotification = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $newsletterEmail = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $subscriptionEndEmail = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $subscriptionEndNotification = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $invoiceEmail = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $invoiceNotification = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $orderEmail = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $orderNotification = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $subscriptionEndReminderEmail = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"recruiter:get", "recruiter:patch:notification"})
     */
    private bool $subscriptionEndReminderNotification = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isNewApplicationEmail(): ?bool
    {
        return $this->newApplicationEmail;
    }

    public function setNewApplicationEmail(bool $newApplicationEmail): self
    {
        $this->newApplicationEmail = $newApplicationEmail;

        return $this;
    }

    public function isNewApplicationNotification(): ?bool
    {
        return $this->newApplicationNotification;
    }

    public function setNewApplicationNotification(bool $newApplicationNotification): self
    {
        $this->newApplicationNotification = $newApplicationNotification;

        return $this;
    }

    public function isEndBroadcastJobPostingEmail(): ?bool
    {
        return $this->endBroadcastJobPostingEmail;
    }

    public function setEndBroadcastJobPostingEmail(bool $endBroadcastJobPostingEmail): self
    {
        $this->endBroadcastJobPostingEmail = $endBroadcastJobPostingEmail;

        return $this;
    }

    public function isEndBroadcastJobPostingNotification(): ?bool
    {
        return $this->endBroadcastJobPostingNotification;
    }

    public function setEndBroadcastJobPostingNotification(bool $endBroadcastJobPostingNotification): self
    {
        $this->endBroadcastJobPostingNotification = $endBroadcastJobPostingNotification;

        return $this;
    }

    public function isDailyResumeEmail(): ?bool
    {
        return $this->dailyResumeEmail;
    }

    public function setDailyResumeEmail(bool $dailyResumeEmail): self
    {
        $this->dailyResumeEmail = $dailyResumeEmail;

        return $this;
    }

    public function isDailyJobPostingEmail(): ?bool
    {
        return $this->dailyJobPostingEmail;
    }

    public function setDailyJobPostingEmail(bool $dailyJobPostingEmail): self
    {
        $this->dailyJobPostingEmail = $dailyJobPostingEmail;

        return $this;
    }

    public function isJobPostingPublishATSEmail(): ?bool
    {
        return $this->jobPostingPublishATSEmail;
    }

    public function setJobPostingPublishATSEmail(bool $jobPostingPublishATSEmail): self
    {
        $this->jobPostingPublishATSEmail = $jobPostingPublishATSEmail;

        return $this;
    }

    public function isJobPostingPublishATSNotification(): ?bool
    {
        return $this->jobPostingPublishATSNotification;
    }

    public function setJobPostingPublishATSNotification(bool $jobPostingPublishATSNotification): self
    {
        $this->jobPostingPublishATSNotification = $jobPostingPublishATSNotification;

        return $this;
    }

    public function isNewsletterEmail(): ?bool
    {
        return $this->newsletterEmail;
    }

    public function setNewsletterEmail(bool $newsletterEmail): self
    {
        $this->newsletterEmail = $newsletterEmail;

        return $this;
    }

    public function isSubscriptionEndEmail(): ?bool
    {
        return $this->subscriptionEndEmail;
    }

    public function setSubscriptionEndEmail(bool $subscriptionEndEmail): self
    {
        $this->subscriptionEndEmail = $subscriptionEndEmail;

        return $this;
    }

    public function isSubscriptionEndNotification(): ?bool
    {
        return $this->subscriptionEndNotification;
    }

    public function setSubscriptionEndNotification(bool $subscriptionEndNotification): self
    {
        $this->subscriptionEndNotification = $subscriptionEndNotification;

        return $this;
    }

    public function isInvoiceEmail(): ?bool
    {
        return $this->invoiceEmail;
    }

    public function setInvoiceEmail(bool $invoiceEmail): self
    {
        $this->invoiceEmail = $invoiceEmail;

        return $this;
    }

    public function isInvoiceNotification(): ?bool
    {
        return $this->invoiceNotification;
    }

    public function setInvoiceNotification(bool $invoiceNotification): self
    {
        $this->invoiceNotification = $invoiceNotification;

        return $this;
    }

    public function isOrderEmail(): ?bool
    {
        return $this->orderEmail;
    }

    public function setOrderEmail(bool $orderEmail): self
    {
        $this->orderEmail = $orderEmail;

        return $this;
    }

    public function isOrderNotification(): ?bool
    {
        return $this->orderNotification;
    }

    public function setOrderNotification(bool $orderNotification): self
    {
        $this->orderNotification = $orderNotification;

        return $this;
    }

    public function isSubscriptionEndReminderEmail(): ?bool
    {
        return $this->subscriptionEndReminderEmail;
    }

    public function setSubscriptionEndReminderEmail(bool $subscriptionEndReminderEmail): self
    {
        $this->subscriptionEndReminderEmail = $subscriptionEndReminderEmail;

        return $this;
    }

    public function isSubscriptionEndReminderNotification(): ?bool
    {
        return $this->subscriptionEndReminderNotification;
    }

    public function setSubscriptionEndReminderNotification(bool $subscriptionEndReminderNotification): self
    {
        $this->subscriptionEndReminderNotification = $subscriptionEndReminderNotification;

        return $this;
    }
}
