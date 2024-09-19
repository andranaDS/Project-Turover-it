<?php

namespace App\JobPosting\Controller\FreeWork\JobPostingSearch;

use App\User\Entity\User;
use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DisableAll
{
    /**
     * @Route(
     *     name="api_job_posting_search_freework_disable_all",
     *     path="/availability/disable-all/{userId}",
     *     methods={"GET"},
     *     host="%api_free_work_base_url%",
     * )
     * @Cache(smaxage="0", maxage="0")
     */
    public function __invoke(Request $request, EntityManagerInterface $em, RouterInterface $router, TranslatorInterface $translator, UrlSignerInterface $urlSigner, int $userId): RedirectResponse
    {
        if (false === $urlSigner->validate($request->getUri())) {
            $params = [
                'status' => 'error',
                'code' => '86a679f9',
                'message' => $translator->trans('job_posting_search.disable_all.error'),
            ];
        } elseif (null === $user = $em->find(User::class, $userId)) {
            $params = [
                'status' => 'error',
                'code' => 'a9d859cd',
                'message' => $translator->trans('job_posting_search.disable_all.error'),
            ];
        } else {
            foreach ($user->getJobPostingSearches() as $jobPostingSearch) {
                $jobPostingSearch->setActiveAlert(false);
            }

            $em->flush();
            $params = [
                'status' => 'success',
                'message' => $translator->trans('job_posting_search.disable_all.success'),
            ];
        }

        return new RedirectResponse($router->generate('candidates_availability_update', $params));
    }
}
