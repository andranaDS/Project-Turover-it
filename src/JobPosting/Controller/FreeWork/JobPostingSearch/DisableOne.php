<?php

namespace App\JobPosting\Controller\FreeWork\JobPostingSearch;

use App\JobPosting\Entity\JobPostingSearch;
use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DisableOne
{
    /**
     * @Route(
     *     name="api_job_posting_search_freework_disable_one",
     *     path="/availability/disable-one/{jobPostingSearchId}",
     *     methods={"GET"},
     *     host="%api_free_work_base_url%",
     * )
     * @Cache(smaxage="0", maxage="0")
     */
    public function __invoke(Request $request, EntityManagerInterface $em, RouterInterface $router, TranslatorInterface $translator, UrlSignerInterface $urlSigner, int $jobPostingSearchId): RedirectResponse
    {
        if (false === $urlSigner->validate($request->getUri())) {
            $params = [
                'status' => 'error',
                'code' => '86a679f9',
                'message' => $translator->trans('job_posting_search.disable_one.error'),
            ];
        } elseif (null === $jobPostingSearch = $em->find(JobPostingSearch::class, $jobPostingSearchId)) {
            $params = [
                'status' => 'error',
                'code' => 'a9d859cd',
                'message' => $translator->trans('job_posting_search.disable_one.error'),
            ];
        } else {
            $jobPostingSearch->setActiveAlert(false);

            $em->flush();
            $params = [
                'status' => 'success',
                'message' => $translator->trans('job_posting_search.disable_one.success', ['%alertName%' => $jobPostingSearch->getTitle()]),
            ];
        }

        return new RedirectResponse($router->generate('candidates_availability_update', $params));
    }
}
