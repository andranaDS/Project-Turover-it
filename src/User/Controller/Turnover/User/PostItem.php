<?php

namespace App\User\Controller\Turnover\User;

use App\Core\Entity\LocationKeyLabel;
use App\Recruiter\Entity\Recruiter;
use App\User\Entity\User;
use App\User\Entity\UserDocument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class PostItem
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Request $request, Security $security, DenormalizerInterface $denormalizer): User
    {
        /** @var Recruiter $recruiter */
        $recruiter = $security->getUser();

        if (!$recruiter instanceof Recruiter) {
            throw new AuthenticationException();
        }

        $data = $request->request->all();
        unset($data['documents']);

        $userDocuments = ($request->files->get('documents') ?? []) + ($request->request->get('documents') ?? []);

        $data['visible'] = (isset($data['visible'])) ? (bool) $data['visible'] : false;
        $data['averageDailyRate'] = (isset($data['averageDailyRate'])) ? (int) $data['averageDailyRate'] : 0;
        $data['grossAnnualSalary'] = (isset($data['grossAnnualSalary'])) ? (int) $data['grossAnnualSalary'] : 0;

        $diplomaLevel = (isset($data['diplomaLevel'])) ? (int) $data['diplomaLevel'] : 0;
        $data['formation'] = ['diplomaLevel' => $diplomaLevel];

        $data['locations'] = $this->denormalizeLocationKeys($data);

        $user = $denormalizer->denormalize($data, User::class, null, [
            'groups' => ['user:turnover_write'],
        ]);

        foreach ($userDocuments as $row) {
            $userDocument = (new UserDocument())
                ->setDocumentFile($row['documentFile'] ?? null)
                ->setContent($row['content'] ?? null)
            ;
            $user->addDocument($userDocument);
        }

        return $user;
    }

    private function transformLocationKeyinLocation(string $locationKey): ?array
    {
        /** @var ?LocationKeyLabel $location */
        $location = $this->em->getRepository(LocationKeyLabel::class)->findOneByKey($locationKey);

        return ($location) ? $location->getData() : null;
    }

    private function denormalizeLocationKeys(array $data): array
    {
        $formattedContent = [];

        if (\array_key_exists('locationKeys', $data) && \is_array($data['locationKeys'])) {
            foreach ($data['locationKeys'] as $locationKey) {
                $location = $this->transformLocationKeyinLocation($locationKey);
                if ($location) {
                    $formattedContent[] = ['location' => $location];
                }
            }
        }

        return $formattedContent;
    }
}
