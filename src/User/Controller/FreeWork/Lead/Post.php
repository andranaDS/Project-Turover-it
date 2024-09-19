<?php

namespace App\User\Controller\FreeWork\Lead;

use App\Partner\Enum\Partner;
use App\User\Entity\User;
use App\User\Entity\UserLead;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Post
{
    private HttpClientInterface $client;

    public function __invoke(
        Request $request,
        User $data,
        EntityManagerInterface $entityManager,
        HttpClientInterface $client
    ): User {
        $this->client = $client;
        $requestData = Json::decode($request->getContent(), Json::FORCE_ARRAY);

        if (false === isset($requestData['formContent'])) {
            throw new BadRequestHttpException('missing data');
        }

        if (null === $data->getPartner() || Partner::NONE === $data->getPartner()->getPartner()) {
            throw new BadRequestHttpException('This user does not have an associated partner.');
        }

        if (null !== $entityManager->getRepository(UserLead::class)->findOneBy(['user' => $data, 'isSuccess' => true])) {
            throw new BadRequestHttpException('User lead already sent');
        }

        if (isset($requestData['context'])) {
            $requestData['formContent'][] = $requestData['context'];
        }

        $userLead = (new UserLead())->setContent($requestData['formContent']);
        $entityManager->persist($userLead);

        if (null === $data->getPartner()->getApiUrl() || false === $this->pushDataToPartnerApi($requestData['formContent'], $userLead, $data->getPartner()->getApiUrl())) {
            $entityManager->flush();
            throw new BadRequestHttpException('The partner api returned an error');
        }

        $userLead->setIsSuccess(true);
        $entityManager->flush();

        return $data;
    }

    private function pushDataToPartnerApi(array $data, UserLead $userLead, string $apiUri): bool
    {
        try {
            $responses = $this->client->request(
                'POST',
                $apiUri,
                [
                    'headers' => [
                        'accept' => 'application/json',
                    ],
                    'json' => $data,
                ]
            );

            $userLead->setResponseStatusCode($responses->getStatusCode());

            return Response::HTTP_OK === $responses->getStatusCode();
        } catch (\Exception|TransportExceptionInterface $e) {
            return false;
        }
    }
}
