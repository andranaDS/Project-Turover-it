<?php

namespace App\User\Tests\Unit\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RelaunchProfileNotUpdatedWithImmediateAvailabilityCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:alert:relaunch-profile-not-updated-immediate-availability');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        self::assertEmailCount(1);

        $rawMessage = self::getMailerMessage();
        self::assertNotNull($rawMessage);
        self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <profile@free-work.com>');
        self::assertEmailHeaderSame($rawMessage, 'to', 'user-immediate-availability-last-activity-3-months-updated-4-months@free-work.fr');
        self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: Votre profil nécessite une mise à jour');
    }
}
