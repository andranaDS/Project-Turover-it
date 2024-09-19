<?php

namespace App\Core\Controller\Locations;

use App\Core\Manager\LocationManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Cities
{
    /**
     * @Route(name="api_core_locations_cities", path="/locations/cities", methods={"GET"})
     * @Cache(smaxage="2592000", maxage="900")
     */
    public function __invoke(Request $request, LocationManager $lm, NormalizerInterface $normalizer): JsonResponse
    {
        if (!$search = $request->query->get('search')) {
            return new JsonResponse(['error' => 'Search is mandatory'], Response::HTTP_BAD_REQUEST);
        }

        $normalizedResults = $normalizer->normalize($lm->autocompleteCities($search, 5), 'json', ['groups' => 'location']);
        $normalizedDeduplicateResults = [];

        if (\is_array($normalizedResults)) {
            // deduplicate
            $locationKeys = [];
            foreach ($normalizedResults as $normalizedResult) {
                if (false === isset($locationKeys[$normalizedResult['key']])) {
                    $locationKeys[$normalizedResult['key']] = true;
                    $normalizedDeduplicateResults[] = $normalizedResult;
                }
            }

            // store location key/label matches
            $lm->storeLocationKeyLabel($normalizedDeduplicateResults);
        }

        return new JsonResponse($normalizedDeduplicateResults, Response::HTTP_OK);
    }
}
