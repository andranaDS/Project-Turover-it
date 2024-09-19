<?php

namespace App\Core\Serializer;

use App\Core\Annotation\ApiEnum;
use App\Core\Annotation\ApiFileUrl;
use App\Core\Annotation\ApiThumbnailUrls;
use App\Core\Cache\AnnotationsCache;
use Doctrine\Common\Util\ClassUtils;
use Greg0ire\Enum\Bridge\Symfony\Translator\GetLabel;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

final class ItemNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private NormalizerInterface $decorated;
    private AnnotationsCache $annotationsCache;
    private GetLabel $enum;
    private UploaderHelper $uploaderHelper;
    private CacheManager $imagineCacheManager;

    public function __construct(
        NormalizerInterface $decorated,
        AnnotationsCache $annotationsCache,
        GetLabel $enum,
        UploaderHelper $uploaderHelper,
        CacheManager $imagineCacheManager
    ) {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }

        $this->decorated = $decorated;
        $this->annotationsCache = $annotationsCache;
        $this->enum = $enum;
        $this->uploaderHelper = $uploaderHelper;
        $this->imagineCacheManager = $imagineCacheManager;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = $this->decorated->normalize($object, $format, $context);

        if (false === \is_object($object) || !\is_array($data)) {
            return $data;
        }

        $annotations = $this->getObjectClassAnnotations($object);

        $data = $this->processEnum($annotations, $data);
        $data = $this->processThumbnails($annotations, $data);

        return $this->processFiles($annotations, $data, $object);
    }

    private function getObjectClassAnnotations(object $object): array
    {
        return $this->annotationsCache->getAnnotations()[ClassUtils::getClass($object)] ?? [];
    }

    private function processEnum(array $annotations, array $data): array
    {
        foreach ($annotations[ApiEnum::class] ?? [] as $property => $properties) {
            if (\array_key_exists($property, $data) && !empty($data[$property])) {
                $data[$property] = [
                    'value' => $data[$property],
                    'label' => $this->enum->__invoke($data[$property], $properties['class'], $properties['translationDomain']),
                ];
            }
        }

        return $data;
    }

    private function processThumbnails(array $annotations, array $data): array
    {
        foreach ($annotations[ApiThumbnailUrls::class] ?? [] as $classProperty => $annotationsProperties) {
            if (\array_key_exists($classProperty, $data) && !empty($data[$classProperty])) {
                $thumbnailUrls = [];
                foreach ($annotationsProperties['filters'] as $filter) {
                    $thumbnailUrls[$filter['name']] = $this->imagineCacheManager->generateUrl($data[$classProperty], $filter['filter']);
                }
                $data[$classProperty] = $thumbnailUrls;
            }
        }

        return $data;
    }

    private function processFiles(array $annotations, array $data, object $object): array
    {
        foreach ($annotations[ApiFileUrl::class] ?? [] as $classProperty => $annotationsProperties) {
            if (\array_key_exists($classProperty, $data) && !empty($data[$classProperty])) {
                $data[$classProperty] = $this->uploaderHelper->asset($object, $annotationsProperties['property']);
            }
        }

        return $data;
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $this->decorated instanceof DenormalizerInterface ? $this->decorated->supportsDenormalization($data, $type, $format) : false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->decorated instanceof DenormalizerInterface ? $this->decorated->denormalize($data, $class, $format, $context) : false;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
