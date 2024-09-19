<?php

namespace App\User\EventSubscriber;

use App\Core\Util\Files;
use App\User\Entity\HrFlowLog;
use App\User\Event\HrFlow\DocumentParsedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class HrFlowSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;
    private NormalizerInterface $normalizer;

    public static function getSubscribedEvents(): array
    {
        return [
            DocumentParsedEvent::NAME => 'onParsedDocument',
        ];
    }

    public function __construct(EntityManagerInterface $entityManager, NormalizerInterface $normalizer)
    {
        $this->entityManager = $entityManager;
        $this->normalizer = $normalizer;
    }

    public function onParsedDocument(DocumentParsedEvent $event): void
    {
        if (false === $logName = tempnam(sys_get_temp_dir(), 'hr-flow')) {
            return;
        }

        $normalizedResults = $this->normalizer->normalize($event->getUserDocument()->getUser(), 'json', ['groups' => ['user:get:private']]);

        $filesystem = new Filesystem();
        $filesystem->dumpFile(
            $logName,
            json_encode(['hrFlow' => $event->getData(), 'user' => $normalizedResults], \JSON_THROW_ON_ERROR)
        );

        $hrFlowFile = (new HrFlowLog())
            ->setUserDocument($event->getUserDocument())
            ->setLogFile(Files::getUploadedFile($logName))
        ;

        $this->entityManager->persist($hrFlowFile);
    }
}
