<?php

namespace App\User\Tests\Unit\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AlertUserMissionsCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:alert:missions');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        self::assertEmailCount(1);

        $rawMessage = self::getMailerMessage();

        self::assertNotNull($rawMessage);
        self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <jobs@free-work.com>');
        self::assertEmailHeaderSame($rawMessage, 'to', 'claude.monet@free-work.fr');
        self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: 1 offre matchant avec vos critères');
        self::assertEmailTextBodyContains($rawMessage, 'Développeur Java 8 ans Exp - IDF - (H/F)');
        self::assertEmailTextBodyContains($rawMessage, 'https://api.freework.localhost/availability/disable-one/1');
        self::assertEmailTextBodyContains($rawMessage, 'https://api.freework.localhost/availability/disable-all/6');
    }
}
