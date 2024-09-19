<?php

namespace App\Core\DoctrineEventSubscriber;

use App\Core\Email\SensitiveContentEmail;
use App\Core\Mailer\Mailer;
use App\Core\Spam\SensitiveContentDetector;
use App\Core\Util\Strings;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class SensitiveContentEmailSubscriber implements EventSubscriber
{
    private ?Request $request;
    private SensitiveContentDetector $detector;
    private Mailer $mailer;
    private string $marketingRecipientMarketing;
    private PropertyAccessorInterface $propertyAccessor;
    private LoggerInterface $logger;

    public function __construct(
        RequestStack $requestStack,
        SensitiveContentDetector $detector,
        Mailer $mailer,
        PropertyAccessorInterface $propertyAccessor,
        string $marketingRecipientMarketing,
        LoggerInterface $logger
    ) {
        $this->request = $requestStack->getMainRequest();
        $this->detector = $detector;
        $this->mailer = $mailer;
        $this->marketingRecipientMarketing = $marketingRecipientMarketing;
        $this->propertyAccessor = $propertyAccessor;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return null !== $this->request ? [Events::postPersist, Events::postUpdate] : [];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    private function process(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        $objectDetectedContents = [];
        if (false === $this->detector->isSensitiveObject($object, $objectDetectedContents)) {
            return;
        }

        // highlight contents with bold
        $contents = [];
        foreach ($objectDetectedContents as $propertyName => $propertyDetectedContents) {
            $contents[] = preg_replace_callback(
                '/(' . implode('|', $propertyDetectedContents) . ')/mi',
                static function (array $sensitiveContent) {
                    return sprintf('<u><strong>%s</strong></u>', $sensitiveContent[0]);
                },
                $this->propertyAccessor->getValue($object, $propertyName));
        }

        try {
            $email = (new SensitiveContentEmail())
                ->to($this->marketingRecipientMarketing)
                ->context([
                    'object' => $object,
                    'type' => Strings::after(\get_class($object), '\\', -1),
                    'content' => implode("\n", $contents),
                ])
            ;

            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
