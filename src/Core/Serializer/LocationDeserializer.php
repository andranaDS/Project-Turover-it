<?php

namespace App\Core\Serializer;

use ApiPlatform\Core\EventListener\DeserializeListener as DecoratedListener;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Core\Entity\Location;
use App\Core\Entity\LocationKeyLabel;
use App\JobPosting\Entity\JobPostingSearchRecruiterAlert;
use App\JobPosting\Entity\JobPostingSearchRecruiterFavorite;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Embedded;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class LocationDeserializer
{
    private DecoratedListener $decorated;
    private Reader $reader;
    private DenormalizerInterface $denormalizer;
    private EntityManagerInterface $em;
    private SerializerContextBuilderInterface $serializerContextBuilder;

    public function __construct(DenormalizerInterface $denormalizer, Reader $reader, DecoratedListener $decorated, EntityManagerInterface $em, SerializerContextBuilderInterface $serializerContextBuilder)
    {
        $this->denormalizer = $denormalizer;
        $this->reader = $reader;
        $this->decorated = $decorated;
        $this->em = $em;
        $this->serializerContextBuilder = $serializerContextBuilder;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $customDenormalize = false;
        $request = $event->getRequest();

        if (!$attributes = RequestAttributesExtractor::extractAttributes($request)) {
            return;
        }

        $reflectionClass = new \ReflectionClass($attributes['resource_class']);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if (
                !empty($annotation = $this->reader->getPropertyAnnotation($reflectionProperty, Embedded::class))
                && Location::class === $annotation->class
            ) {
                $customDenormalize = true;
            }
        }

        if (\in_array($attributes['resource_class'], [JobPostingSearchRecruiterAlert::class, JobPostingSearchRecruiterFavorite::class], true)
            && ($request->isMethod(Request::METHOD_POST) || $request->isMethod(Request::METHOD_PUT))) {
            $customDenormalize = true;
        }

        if (
            $customDenormalize &&
                \in_array($request->attributes->get('_route'), [
                    'api_users_freework_patch_profile_personal_info_item',
                    'api_users_freework_patch_profile_job_search_preferences_item',
                    'api_companies_turnover_patch_directory_item',
                    'api_job_posting_search_recruiter_alerts_turnover_post_collection',
                    'api_job_posting_search_recruiter_alerts_turnover_put_item',
                    'api_job_posting_search_recruiter_favorites_turnover_post_collection',
                ], true)
        ) {
            $this->denormalizeLocation($request, $attributes);
        } else {
            $this->decorated->onKernelRequest($event);
        }
    }

    private function denormalizeLocation(Request $request, array $attributes): void
    {
        $context = $this->serializerContextBuilder->createFromRequest($request, true, $attributes);

        $populated = $request->attributes->get('data');
        if (null !== $populated) {
            $context['object_to_populate'] = $populated;
        }

        $initialContent = $formattedContent = json_decode($request->getContent(), true);

        if (null === $initialContent) {
            return;
        }

        $this->denormalizeLocationKeys($initialContent, $formattedContent);

        if (\array_key_exists('locationKey', $initialContent) && \is_string($initialContent['locationKey'])) {
            $location = $this->transformLocationKeyinLocation($initialContent['locationKey']);
            $formattedContent['location'] = new Location();
            if ($location) {
                $formattedContent['location'] = $location;
            }
        }

        $object = $this->denormalizer->denormalize($formattedContent, $attributes['resource_class'], null, $context);
        $request->attributes->set('data', $object);
    }

    public function denormalizeLocationKeys(array $initialContent, array &$formattedContent): void
    {
        if (\array_key_exists('locationKeys', $initialContent) && \is_array($initialContent['locationKeys'])) {
            $formattedContent['locations'] = [];
            foreach ($initialContent['locationKeys'] as $locationKey) {
                $location = $this->transformLocationKeyinLocation($locationKey);
                if ($location) {
                    $formattedContent['locations'][] = ['location' => $location];
                }
            }
        }
    }

    private function transformLocationKeyinLocation(string $locationKey): ?array
    {
        /** @var ?LocationKeyLabel $location */
        $location = $this->em->getRepository(LocationKeyLabel::class)->findOneByKey($locationKey);

        return ($location) ? $location->getData() : null;
    }
}
