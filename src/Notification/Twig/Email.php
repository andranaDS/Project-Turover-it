<?php

namespace App\Notification\Twig;

use Symfony\Component\Mime\Email as BaseEmail;

class Email extends BaseEmail
{
    protected ?string $template = null;
    protected ?string $notification = null;
    protected array $context = [];
    protected ?string $senderType = null;

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): void
    {
        $this->template = $template;
    }

    public function context(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @internal
     */
    public function __serialize(): array
    {
        return [$this->template, $this->context, parent::__serialize()];
    }

    /**
     * @internal
     */
    public function __unserialize(array $data): void
    {
        [$this->template, $this->context, $parentData] = $data;

        parent::__unserialize($parentData);
    }

    public function getNotification(): ?string
    {
        return $this->notification;
    }

    public function setNotification(?string $notification): void
    {
        $this->notification = $notification;
    }

    public function getSenderType(): ?string
    {
        return $this->senderType;
    }
}
