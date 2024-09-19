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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class PatchItem
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Request $request, User $user, Security $security, DenormalizerInterface $denormalizer): User
    {
        /** @var Recruiter $recruiter */
        $recruiter = $security->getUser();

        if (!$recruiter instanceof Recruiter) {
            throw new AuthenticationException();
        }

        $data = $request->request->all();
        unset($data['documents']);

        $userDocuments = ($request->files->get('documents') ?? []) + ($request->request->get('documents') ?? []);

        if (\array_key_exists('visible', $data)) {
            $data['visible'] = (bool) $data['visible'];
        }

        if (\array_key_exists('averageDailyRate', $data)) {
            $data['averageDailyRate'] = (int) $data['averageDailyRate'];
        }

        if (\array_key_exists('grossAnnualSalary', $data)) {
            $data['grossAnnualSalary'] = (int) $data['grossAnnualSalary'];
        }

        if (\array_key_exists('diplomaLevel', $data)) {
            $diplomaLevel = (int) $data['diplomaLevel'];
            $data['formation'] = ['diplomaLevel' => $diplomaLevel];
        }

        if (\array_key_exists('locationKeys', $data)) {
            $data['locations'] = $this->denormalizeLocationKeys($data);
        }

        $user = $denormalizer->denormalize($data, User::class, null, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $user,
            'groups' => ['user:turnover_write'],
        ]);

        if (isset($userDocuments) && !empty($userDocuments)) {
            foreach ($user->getDocuments() as $document) {
                $this->em->remove($document);
            }

            $this->em->flush();

            foreach ($userDocuments as $row) {
                $userDocument = (new UserDocument())
                    ->setDocumentFile($row['documentFile'] ?? null)
                    ->setContent($row['content'] ?? null)
                ;
                $user->addDocument($userDocument);
            }
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
