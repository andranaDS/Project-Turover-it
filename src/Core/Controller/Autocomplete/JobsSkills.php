<?php

namespace App\Core\Controller\Autocomplete;

use App\Core\Entity\Job;
use App\Core\Entity\Skill;
use App\Core\Entity\SkillJob;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JobsSkills
{
    /**
     * @Route(name="api_core_autocomplete_jobs_skills", path="/jobs_skills/autocomplete", methods={"GET"})
     * @Cache(smaxage="7200", maxage="300")
     */
    public function __invoke(Request $request, EntityManagerInterface $em, NormalizerInterface $normalizer): JsonResponse
    {
        if (null === $q = $request->query->get('q')) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        $results = [];
        foreach ($em->getRepository(Job::class)->searchNameForContributionS($q) as $job) {
            /* @var Job $job */
            $results[] = new SkillJob($job->getNameForContribution(), $job->getNameForContributionSlug(), SkillJob::TYPE_JOB);
        }

        foreach ($em->getRepository(Skill::class)->searchDisplayed($q) as $skill) {
            /* @var Skill $skill */
            $results[] = new SkillJob($skill->getName(), $skill->getSlug(), SkillJob::TYPE_SKILL);
        }

        $normalizedResults = $normalizer->normalize($results, 'json', ['groups' => ['core:autocomplete:jobs_skills']]);

        return new JsonResponse($normalizedResults, Response::HTTP_OK);
    }
}
