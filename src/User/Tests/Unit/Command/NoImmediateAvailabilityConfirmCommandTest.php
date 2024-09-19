<?php

namespace App\User\Tests\Unit\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class NoImmediateAvailabilityConfirmCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:alert:no-immediate-availability-confirm');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        self::assertEmailCount(1);

        $rawMessage = self::getMailerMessage();
        self::assertNotNull($rawMessage);
        self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <profile@free-work.com>');
        self::assertEmailHeaderSame($rawMessage, 'to', 'user-no-immediate-availability-14-days@free-work.fr');
        self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: Toujours indisponible ?');
        self::assertEmailTextBodyContains($rawMessage, 'https://api.freework.localhost/availability/immediate/35');
    }
}
