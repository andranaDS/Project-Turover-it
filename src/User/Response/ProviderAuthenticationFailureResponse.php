<?php

namespace App\User\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ProviderAuthenticationFailureResponse extends JsonResponse
{
    private string $message;

    public function __construct(string $message = 'An error occured.', int $statusCode = Response::HTTP_UNAUTHORIZED)
    {
        $this->message = $message;

        parent::__construct(null, $statusCode, ['WWW-Authenticate' => 'Bearer']);
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        $this->setData();

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setData($data = []): self
    {
        return parent::setData((array) $data + ['code' => $this->statusCode, 'message' => $this->message]);
    }
}
