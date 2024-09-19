<?php

namespace App\Messaging\Serializer;

use App\Messaging\Entity\Feed;
use App\Messaging\Entity\FeedUser;
use App\User\Contracts\UserInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class FeedNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private NormalizerInterface $decorated;
    private Security $security;

    public function __construct(NormalizerInterface $decorated, Security $security)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }

        $this->decorated = $decorated;
        $this->security = $security;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = $this->decorated->normalize($object, $format, $context);

        if (!$object instanceof Feed) {
            return $data;
        }

        if ((null === $loggedUser = $this->security->getUser()) || !$loggedUser instanceof UserInterface) {
            return $data;
        }

        $authorFeedUser = null;
        $receiverFeedUser = null;

        foreach ($object->getFeedUsers()->getValues() as $feedUser) {
            /* @var FeedUser $feedUser */
            // TODO: change to array if multiple receivers needed
            ($feedUser->getUser()->getId() === $loggedUser->getId()) ? $authorFeedUser = $feedUser : $receiverFeedUser = $feedUser;
        }

        if ($authorFeedUser && $receiverFeedUser && \is_array($data)) {
            $data['authorFeedUser'] = $this->decorated->normalize($authorFeedUser, $format, $context);
            $data['receiverFeedUser'] = $this->decorated->normalize($receiverFeedUser, $format, $context);
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
