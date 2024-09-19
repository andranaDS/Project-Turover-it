<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\Contact;
use App\Core\Enum\ContactService;
use App\Core\Util\Arrays;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class ContactFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        foreach ($this->getData() as $d) {
            $contact = (new Contact())
                ->setFullname($d['fullname'])
                ->setEmail($d['email'])
                ->setService($d['service'])
                ->setSubject($d['subject'])
                ->setMessage($d['message'])
                ->setCreatedAt($d['createdAt'])
                ->setUser($d['user'])
            ;
            $manager->persist($contact);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $faker = Faker::create('fr_FR');

        $data = [];

        $contactCount = 10;
        /* @var ?User $user */
        for ($i = 0; $i < $contactCount; ++$i) {
            $user = 0 === mt_rand(0, 1) ? Arrays::getRandom($this->users) : null;
            $createdAt = $faker->dateTimeBetween('- 6 months', '- 1 month');

            $data[] = [
                'fullname' => $user ? $user->getFirstName() . ' ' . $user->getLastName() : $faker->firstName() . ' ' . $faker->lastName(),
                'email' => $user ? $user->getEmail() : $faker->email,
                'service' => Arrays::getRandom(ContactService::getConstants()),
                'subject' => $faker->text(mt_rand(25, 150)),
                'message' => $faker->paragraph(mt_rand(1, 3)),
                'createdAt' => $createdAt,
                'user' => $user ?? null,
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'fullname' => 'Claude Monet',
                'email' => 'claude.monet@free-work.fr',
                'service' => ContactService::USERS,
                'subject' => 'Contact - Subject - 1',
                'message' => 'Contact - Message - 1',
                'createdAt' => new \DateTime('2021-01-01 20:00:00'),
                'user' => $this->users['claude.monet@free-work.fr'],
            ],
            [
                'fullname' => 'Vincent Van Gogh',
                'email' => 'vincent.van-gogh@free-work.fr',
                'service' => ContactService::SALES,
                'subject' => 'Contact - Subject - 2',
                'message' => 'Contact - Message - 2',
                'createdAt' => new \DateTime('2021-01-01 20:30:00'),
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
            ],
            [
                'fullname' => 'Henri Matisse',
                'email' => 'henri.matisse@free-work.fr',
                'service' => ContactService::USERS,
                'subject' => 'Contact - Subject - 3',
                'message' => 'Contact - Message - 3',
                'createdAt' => new \DateTime('2021-01-01 21:00:00'),
                'user' => $this->users['henri.matisse@free-work.fr'],
            ],
            [
                'fullname' => 'Berthe Morisot',
                'email' => 'berthe.morisot@free-work.fr',
                'service' => ContactService::SALES,
                'subject' => 'Contact - Subject - 4',
                'message' => 'Contact - Message - 4',
                'createdAt' => new \DateTime('2021-01-01 21:30:00'),
                'user' => null,
            ],
            [
                'fullname' => 'Mary Cassatt',
                'email' => 'mary.cassatt@free-work.fr',
                'service' => ContactService::USERS,
                'subject' => 'Contact - Subject - 5',
                'message' => 'Contact - Message - 5',
                'createdAt' => new \DateTime('2021-01-01 22:00:00'),
                'user' => null,
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
        ];
    }
}
