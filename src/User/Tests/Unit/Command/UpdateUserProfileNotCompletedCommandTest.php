<?php

namespace App\User\Tests\Unit\Command;

use App\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateUserProfileNotCompletedCommandTest extends KernelTestCase
{
    private const USER_TO_UPDATE_IDS = [6, 7, 8];

    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        if (null === $container = $kernel->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        $em = $container->get('doctrine')->getManager();
        $users = $em->getRepository(User::class)->findBy(['id' => self::USER_TO_UPDATE_IDS]);

        self::assertCount(3, $users);

        /** @var User $user */
        foreach ($users as $user) {
            self::assertTrue($user->getProfileCompleted());
        }

        $em->clear();

        $command = $application->find('app:user:update-profile-not-completed');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        $users = $em->getRepository(User::class)->findBy(['id' => self::USER_TO_UPDATE_IDS]);

        /** @var User $user */
        foreach ($users as $user) {
            self::assertFalse($user->getProfileCompleted());
            self::assertNull($user->getFormStep());
        }

        self::assertStringContainsString('Processed 3 users', $commandTester->getDisplay());
    }
}
