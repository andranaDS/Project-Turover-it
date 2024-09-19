<?php

namespace App\Notification\Twig;

use Symfony\Component\Mime\Address;
use Twig\Environment;

class WrappedTemplatedEmail
{
    private Environment $twig;
    private Email $message;

    public function __construct(Environment $twig, Email $message)
    {
        $this->twig = $twig;
        $this->message = $message;
    }

    public function toName(): string
    {
        return $this->message->getTo()[0]->getName();
    }

    public function image(string $image, string $contentType = null): string
    {
        $file = $this->twig->getLoader()->getSourceContext($image);
        if ($path = $file->getPath()) {
            $this->message->embedFromPath($path, $image, $contentType);
        } else {
            $this->message->embed($file->getCode(), $image, $contentType);
        }

        return 'cid:' . $image;
    }

    public function attach(string $file, string $name = null, string $contentType = null): void
    {
        $file = $this->twig->getLoader()->getSourceContext($file);
        if ($path = $file->getPath()) {
            $this->message->attachFromPath($path, $name, $contentType);
        } else {
            $this->message->attach($file->getCode(), $name, $contentType);
        }
    }

    public function setSubject(string $subject): self
    {
        $this->message->subject($subject);

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->message->getSubject();
    }

    public function setReturnPath(string $address): self
    {
        $this->message->returnPath($address);

        return $this;
    }

    public function getReturnPath(): ?Address
    {
        return $this->message->getReturnPath();
    }

    public function addFrom(string $address, string $name = ''): self
    {
        $this->message->addFrom(new Address($address, $name));

        return $this;
    }

    /**
     * @return Address[]
     */
    public function getFrom(): array
    {
        return $this->message->getFrom();
    }

    public function addReplyTo(string $address): self
    {
        $this->message->addReplyTo($address);

        return $this;
    }

    /**
     * @return Address[]
     */
    public function getReplyTo(): array
    {
        return $this->message->getReplyTo();
    }

    public function addTo(string $address, string $name = ''): self
    {
        $this->message->addTo(new Address($address, $name));

        return $this;
    }

    /**
     * @return Address[]
     */
    public function getTo(): array
    {
        return $this->message->getTo();
    }

    public function addCc(string $address, string $name = ''): self
    {
        $this->message->addCc(new Address($address, $name));

        return $this;
    }

    /**
     * @return Address[]
     */
    public function getCc(): array
    {
        return $this->message->getCc();
    }

    public function addBcc(string $address, string $name = ''): self
    {
        $this->message->addBcc(new Address($address, $name));

        return $this;
    }

    /**
     * @return Address[]
     */
    public function getBcc(): array
    {
        return $this->message->getBcc();
    }

    public function setPriority(int $priority): self
    {
        $this->message->priority($priority);

        return $this;
    }

    public function getPriority(): int
    {
        return $this->message->getPriority();
    }

    public function addTextHeader(string $property, string $value): self
    {
        $this->message->getHeaders()->addTextHeader($property, $value);

        return $this;
    }

    public function getTextHeader(string $property): ?string
    {
        if (null === $header = $this->message->getHeaders()->get($property)) {
            return null;
        }

        return $header->getBodyAsString();
    }
}
