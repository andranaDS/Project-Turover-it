<?php

namespace App\Folder\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\Folder\Entity\Folder;
use App\Folder\Enum\FolderType;
use App\Folder\Manager\FolderManager;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\Recruiter\Entity\Recruiter;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FolderFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private FolderManager $fm;
    private array $recruiters = [];

    public function __construct(string $env, FolderManager $fm)
    {
        parent::__construct($env);
        $this->fm = $fm;
    }

    public function load(ObjectManager $manager): void
    {
        // generate mandatory folders
        foreach ($manager->getRepository(Recruiter::class)->findAll() as $recruiter) {
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
            $this->fm->generateFolders($recruiter);
        }
        $manager->flush();

        // generate personal folders
        foreach ($this->getData() as $d) {
            $folder = (new Folder())
                ->setRecruiter($d['recruiter'])
                ->setName($d['name'])
                ->setType(FolderType::PERSONAL)
            ;
            $manager->persist($folder);
        }
        $manager->flush();
    }

    public function getDevData(): array
    {
        $names = [
            'Python', 'Java', 'JavaScript', 'PHP', 'Ruby',
            'Swift', 'Objective-C', 'SQL', 'Architecte', 'Auditeur',
            'Consultant', 'Développeur', 'Ingénieur commercial', 'Lead Developer', 'R&D engineer',
            'Technicien IT', 'Technicien micro / réseaux', 'UI designer', 'UX designer', 'Webmaster',
        ];

        $data = [];
        foreach ($this->recruiters as $recruiter) {
            foreach (Arrays::getRandomSubarray(array: $names, max: 2) as $name) {
                $data[] = [
                    'recruiter' => $recruiter,
                    'name' => 'Recherche ' . $name,
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'name' => 'Recherche PHP',
            ],
            [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'name' => 'Recherche Java',
            ],
            [
                'recruiter' => $this->recruiters['arya.stark@got.com'],
                'name' => 'Développeurs Vue.js',
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            RecruiterFixtures::class,
        ];
    }
}
