<?php

namespace App\Company\Controller\Turnover\Company;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Company\Entity\Company;
use App\Company\Entity\CompanyPicture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Vich\UploaderBundle\Handler\UploadHandler;

final class PostDirectoryMedia
{
    public function __invoke(Request $request, Company $company, ValidatorInterface $validator, UploadHandler $uploadHandler, IriConverterInterface $iriConverter, EntityManagerInterface $em, PropertyAccessorInterface $propertyAccessor): Company
    {
        $videoFile = $request->files->get('videoFile');
        $logoFile = $request->files->get('logoFile');
        $coverPictureFile = $request->files->get('coverPictureFile');
        $companyPicturesFiles = $request->files->get('pictures', []);

        $companyPicturesIRIs = $request->request->all('pictures');
        $videoRequestFile = $request->request->get('videoFile');
        $logoRequestFile = $request->request->get('logoFile');
        $coverPictureRequestFile = $request->request->get('coverPictureFile');

        // handle simple file
        $this->handleFileRemove([$videoFile, $logoFile, $coverPictureFile], [$videoRequestFile, $logoRequestFile, $coverPictureRequestFile], $uploadHandler, $company, $propertyAccessor);
        $this->handleFileAdd([$videoFile, $logoFile, $coverPictureFile], $company, $propertyAccessor);

        // handle collection
        if (null !== $companyPicturesFiles && \count($companyPicturesFiles) > 0) {
            // delete unsend IRIs & update position
            $this->handleCollectionUpdate($company, $iriConverter, $uploadHandler, $companyPicturesIRIs);

            // add new ones
            foreach ($companyPicturesFiles as $position => $companyPictureData) {
                if (!empty($companyPictureData['imageFile'])) {
                    $companyPicture = (new CompanyPicture())
                        ->setImageFile($companyPictureData['imageFile'])
                        ->setPosition($position)
                    ;
                    $company->addPicture($companyPicture);
                }
            }
        } elseif (\count($companyPicturesIRIs) > 0) { // handle only IRIs
            $this->handleCollectionUpdate($company, $iriConverter, $uploadHandler, $companyPicturesIRIs);
        }

        $validator->validate($company, ['groups' => ['Default', 'company:post:directory_media']]);

        return $company;
    }

    private function handleCollectionUpdate(Company $company, IriConverterInterface $iriConverter, UploadHandler $uploadHandler, array $companyPicturesIRIs): void
    {
        $currentPictures = $company->getPictures();
        foreach ($currentPictures as $picture) {
            if (!\in_array($iriConverter->getIriFromItem($picture), $companyPicturesIRIs, true)) {
                // delete unsend
                $uploadHandler->remove($picture, 'imageFile');
                $company->removePicture($picture);
            } else {
                // update position
                $picture->setPosition((int) array_search($iriConverter->getIriFromItem($picture), $companyPicturesIRIs, true));
            }
        }
    }

    private function handleFileRemove(array $files, array $parameters, UploadHandler $uploadHandler, Company $company, PropertyAccessorInterface $propertyAccessor): void
    {
        $fieldNames = ['video', 'logo', 'coverPicture'];

        for ($i = 0; $i < \count($fieldNames); ++$i) {
            if (null === $files[$i] && null !== $parameters[$i]) {
                $propertyAccessor->setValue($company, $fieldNames[$i], null);
            }
        }
    }

    private function handleFileAdd(array $files, Company $company, PropertyAccessorInterface $propertyAccessor): void
    {
        $fieldNames = ['video', 'logo', 'coverPicture'];

        for ($i = 0; $i < \count($fieldNames); ++$i) {
            if (!empty($files[$i])) {
                $propertyAccessor->setValue($company, $fieldNames[$i] . 'File', $files[$i]);
            }
        }
    }
}
