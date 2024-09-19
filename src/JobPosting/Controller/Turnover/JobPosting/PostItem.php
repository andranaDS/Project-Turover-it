<?php

namespace App\JobPosting\Controller\Turnover\JobPosting;

use App\Core\Entity\LocationKeyLabel;
use App\JobPosting\Entity\JobPosting;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class PostItem
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Request $request, UserInterface $recruiter, DenormalizerInterface $denormalizer): JobPosting
    {
        if (!$recruiter instanceof Recruiter) {
            throw new BadRequestException();
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['locationKey']) && !empty($data['locationKey'])) {
            $data['location'] = $this->transformLocationKeyinLocation($data['locationKey']);
        }

        $jobPosting = $denormalizer->denormalize($data, JobPosting::class, null, [
            'groups' => ['job_posting:write'],
        ]);

        $jobPosting->setCompany($recruiter->getCompany());

        return $jobPosting;
    }

    private function transformLocationKeyinLocation(string $locationKey): ?array
    {
        /** @var ?LocationKeyLabel $location */
        $location = $this->em->getRepository(LocationKeyLabel::class)->findOneByKey($locationKey);

        return ($location) ? $location->getData() : null;
    }
}
