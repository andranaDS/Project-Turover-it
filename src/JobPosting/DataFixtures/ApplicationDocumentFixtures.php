<?php

namespace App\JobPosting\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\ApplicationDocument;
use App\User\DataFixtures\UserDocumentsFixtures;
use App\User\Entity\UserDocument;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ApplicationDocumentFixtures extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private array $applications = [];
    private array $documents = [];

    public function load(ObjectManager $manager): void
    {
        // fetch applications
        foreach ($manager->getRepository(Application::class)->findAll() as $application) {
            $this->applications[$application->getId()] = $application;
        }

        // fetch documents
        foreach ($manager->getRepository(UserDocument::class)->findAll() as $document) {
            $this->documents[$document->getId()] = $document;
        }

        // process data
        foreach ($this->getData() as $d) {
            $document = (new ApplicationDocument())
                ->setDocument($d['document'])
                ->setApplication($d['application'])
            ;

            $manager->persist($document);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        foreach ($this->applications as $application) {
            /** @var Application $application */
            $hasDocument = mt_rand(0, 1);

            if ($hasDocument) {
                $user = $application->getUser();
                if ($user) {
                    $userDocuments = $user->getDocuments();
                    $countDocuments = $userDocuments->count();

                    if ($countDocuments > 0) {
                        $documentNumber = mt_rand(1, $countDocuments);
                        for ($i = 0; $i < $documentNumber; ++$i) {
                            $data[] = [
                                'document' => $userDocuments->get($i),
                                'application' => $application,
                            ];
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'application' => $this->applications[3],
                'document' => $this->documents[1],
            ],
            [
                'application' => $this->applications[3],
                'document' => $this->documents[2],
            ],
            [
                'application' => $this->applications[4],
                'document' => $this->documents[4],
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            UserDocumentsFixtures::class,
            ApplicationFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['application'];
    }
}
