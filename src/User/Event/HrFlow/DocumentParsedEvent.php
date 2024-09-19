<?php

namespace App\User\Event\HrFlow;

use App\User\Entity\UserDocument;
use Symfony\Contracts\EventDispatcher\Event;

class DocumentParsedEvent extends Event
{
    public const NAME = 'hr_flow.document.parsed';

    protected UserDocument $userDocument;

    protected array $data;

    public function __construct(UserDocument $userDocument, array $data)
    {
        $this->userDocument = $userDocument;
        $this->data = $data;
    }

    public function getUserDocument(): UserDocument
    {
        return $this->userDocument;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
