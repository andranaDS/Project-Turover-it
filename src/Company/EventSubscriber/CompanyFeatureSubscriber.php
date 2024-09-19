<?php

namespace App\Company\EventSubscriber;

use App\Company\Event\CompanyFeatureEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\String\UnicodeString;

class CompanyFeatureSubscriber implements EventSubscriberInterface
{
    private PropertyAccessorInterface $propertyAccessor;
    private EntityManagerInterface $em;

    public function __construct(PropertyAccessorInterface $propertyAccessor, EntityManagerInterface $em)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array|\Generator
    {
        $refl = new \ReflectionClass(CompanyFeatureEvents::class);

        foreach ($refl->getConstants() as $event) {
            yield $event => 'complete';
        }
    }

    public function complete(GenericEvent $event, string $eventName): void
    {
        $company = $event->getSubject();
        // snake_case => camelCase
        $property = (new UnicodeString($eventName))->camel()->toString();
        if (null !== $companyFeaturesUsage = $company->getFeaturesUsage()) {
            $this->propertyAccessor->setValue($companyFeaturesUsage, $property . 'At', new \DateTime());
            $this->em->flush();
        }
    }
}
