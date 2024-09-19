<?php

namespace App\User\Service;

use App\User\Contracts\ResumeParserInterface;
use App\User\Entity\User;
use App\User\Entity\UserDocument;
use App\User\Event\HrFlow\DocumentParsedEvent;
use App\User\Hydrator\HrFlowParserToUserHydrator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HrFlowResumeParser implements ResumeParserInterface
{
    private HttpClientInterface $client;

    private string $hrFlowSourceKey;

    private HrFlowParserToUserHydrator $hydrator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        HttpClientInterface $hrFlowClient,
        string $hrFlowSourceKey,
        HrFlowParserToUserHydrator $hydrator,
        EventDispatcherInterface $dispatcher
    ) {
        $this->client = $hrFlowClient;
        $this->hrFlowSourceKey = $hrFlowSourceKey;
        $this->hydrator = $hydrator;
        $this->dispatcher = $dispatcher;
    }

    public function parseResume(string $filepath, User $user, UserDocument $userDocument): ?User
    {
        $formData = new FormDataPart([
            'sync_parsing' => '1',
            'source_key' => $this->hrFlowSourceKey,
            'file' => DataPart::fromPath($filepath),
        ]);

        try {
            $response = $this->client->request('POST', 'profile/parsing/file', [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]);

            $data = json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

            if (!isset($data['data'])) {
                return null;
            }

            $this->hydrator->hydrateUserDocument($data['data'], $userDocument);
            $this->hydrator->hydrateUser($data['data'], $user);

            $this->dispatcher->dispatch(new DocumentParsedEvent($userDocument, $data), DocumentParsedEvent::NAME);
        } catch (\Exception $e) {
            return null;
        }

        return $user;
    }
}
