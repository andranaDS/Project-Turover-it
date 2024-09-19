<?php

namespace App\Core\Spam;

use App\Core\Annotation\SensitiveContentEntity;
use App\Core\Annotation\SensitiveContentProperty;
use App\Core\Repository\SensitiveContentRepository;
use App\Core\Util\ContentDetector;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class SensitiveContentDetector
{
    private SensitiveContentRepository $repository;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(SensitiveContentRepository $repository, PropertyAccessorInterface $propertyAccessor)
    {
        $this->repository = $repository;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function isSensitiveObject(object $object, array &$detectedContents): bool
    {
        $reader = new AnnotationReader();

        $reflectionClass = new \ReflectionClass(ClassUtils::getClass($object));
        if (null === $reader->getClassAnnotation($reflectionClass, SensitiveContentEntity::class)) {
            return false;
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if (null === $reader->getPropertyAnnotation($reflectionProperty, SensitiveContentProperty::class)) {
                continue;
            }

            $propertyName = $reflectionProperty->getName();
            $propertyValue = $this->propertyAccessor->getValue($object, $propertyName);

            $propertyDetectedContents = [];
            if (null !== $propertyValue && true === $this->isSensitiveValue($propertyValue, $propertyDetectedContents)) {
                $detectedContents[$propertyName] = $propertyDetectedContents;
            }
        }

        return !empty($detectedContents);
    }

    public function isSensitiveValue(string $value, array &$detectedContents): bool
    {
        $contentsToDetect = $this->repository->findContents();

        if (empty($contentsToDetect)) {
            return false;
        }

        $detectedContents = ContentDetector::detect($value, $contentsToDetect);

        return !empty($detectedContents);
    }
}
