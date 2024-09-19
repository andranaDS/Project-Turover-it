<?php

namespace App\User\Controller\FreeWork\User;

use App\Blog\Manager\BlogDataManager;
use App\Company\Manager\CompanyDataManager;
use App\Forum\Manager\ForumDataManager;
use App\JobPosting\Manager\JobPostingDataManager;
use App\User\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class Data
{
    private static array $scopes = [
        'blog_post_upvotes',
        'forum_topic_traces',
        'forum_topic_favorites',
        'forum_topic_participations',
        'forum_post_upvotes',
        'company_favorites',
        'company_blacklists',
        'job_posting_favorites',
        'job_posting_application_in_progress',
        'job_posting_application_ko',
        'job_posting_traces',
    ];

    public function __invoke(User $data, Request $request, Security $security, BlogDataManager $bdm, ForumDataManager $fdm, CompanyDataManager $cdm, JobPostingDataManager $jpdm): Response
    {
        if (null === $requestedScopes = $request->query->get('scopes')) {
            $requestedScopes = self::$scopes;
        } else {
            $requestedScopes = array_intersect(explode(',', $requestedScopes), self::$scopes);
        }

        $data = array_merge(
            $bdm->getUserBlogData($data, $requestedScopes),
            $fdm->getUserForumData($data, $requestedScopes),
            $cdm->getUserCompanyData($data, $requestedScopes),
            $jpdm->getUserJobPostingData($data, $requestedScopes),
        );

        return new JsonResponse($data);
    }
}
