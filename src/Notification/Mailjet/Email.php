<?php

namespace App\Notification\Mailjet;

use Symfony\Component\Mime\Email as BaseEmail;

class Email extends BaseEmail
{
    protected ?int $templateId = null;
    protected array $variables = [];

    public function getTemplateId(): ?int
    {
        return $this->templateId;
    }

    public function setTemplateId(?int $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }
}
