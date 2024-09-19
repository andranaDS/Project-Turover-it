<?php

namespace App\Notification\Mailjet;

use App\Core\Util\Arrays;
use Mailjet\Client;
use Mailjet\Resources;

class Mailjet
{
    private string $mailjetApiKeyPublic;
    private string $mailjetApiKeyPrivate;

    public function __construct(string $mailjetApiKeyPublic, string $mailjetApiKeyPrivate)
    {
        $this->mailjetApiKeyPublic = $mailjetApiKeyPublic;
        $this->mailjetApiKeyPrivate = $mailjetApiKeyPrivate;
    }

    private function createClient(string $version = 'v3'): Client
    {
        return new Client(
            $this->mailjetApiKeyPublic,
            $this->mailjetApiKeyPrivate,
            true,
            ['version' => $version]
        );
    }

    public function getTemplate(int $id): array
    {
        $client = $this->createClient();
        $response = $client->get(Resources::$TemplateDetailcontent, ['id' => $id]);

        if (200 !== $response->getStatus()) {
            throw new \RuntimeException("Template Mailjet #$id can't be fetched.");
        }

        return Arrays::first($response->getData());
    }
}
