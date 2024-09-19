<?php

namespace App\JobPosting\EventSubscriber;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Core\Entity\Skill;
use App\Core\Serializer\LocationDeserializer;
use App\JobPosting\Entity\JobPostingSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class JobPostingSearchDeserializeSubscriber implements EventSubscriberInterface
{
    private LocationDeserializer $decorated;
    private DenormalizerInterface $denormalizer;
    private EntityManagerInterface $em;
    private SerializerContextBuilderInterface $serializerContextBuilder;

    public function __construct(DenormalizerInterface $denormalizer, LocationDeserializer $decorated, EntityManagerInterface $em, SerializerContextBuilderInterface $serializerContextBuilder)
    {
        $this->denormalizer = $denormalizer;
        $this->decorated = $decorated;
        $this->em = $em;
        $this->serializerContextBuilder = $serializerContextBuilder;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 2],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$attributes = RequestAttributesExtractor::extractAttributes($request)) {
            return;
        }

        if (JobPostingSearch::class === $attributes['resource_class']
            && ($request->isMethod(Request::METHOD_POST) || $request->isMethod(Request::METHOD_PUT))
        ) {
            $this->denormalizeJobPostingSearch($request, $attributes);
        } else {
            $this->decorated->onKernelRequest($event);
        }
    }

    private function denormalizeJobPostingSearch(Request $request, array $attributes): void
    {
        $context = $this->serializerContextBuilder->createFromRequest($request, true, $attributes);

        $populated = $request->attributes->get('data');
        if (null !== $populated) {
            $context['object_to_populate'] = $populated;
        }

        $initialContent = $formattedContent = json_decode($request->getContent(), true);

        $this->decorated->denormalizeLocationKeys($initialContent, $formattedContent);

        // skills transform from ['slug'] to ['IRI']
        if (\array_key_exists('skills', $initialContent) && \is_array($contentSkills = $initialContent['skills'])) {
            $formattedContent['skills'] = [];
            foreach ($contentSkills as $skillSlug) {
                $skill = $this->em->getRepository(Skill::class)->findOneBySlug($skillSlug);
                if ($skill) {
                    $formattedContent['skills'][] = '/skills/' . $skill->getId();
                }
            }
        }

        $object = $this->denormalizer->denormalize($formattedContent, $attributes['resource_class'], null, $context);
        $request->attributes->set('data', $object);
    }
}
