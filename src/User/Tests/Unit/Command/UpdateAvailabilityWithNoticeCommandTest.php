<?php

namespace App\User\Tests\Unit\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateAvailabilityWithNoticeCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:alert:update-availability-with-notice');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        self::assertEmailCount(2);

        $emails = [
            0 => 'user-no-availability-45-days-status@free-work.fr',
            2 => 'user-no-availability-75-days-status@free-work.fr',
        ];

        foreach ($emails as $key => $email) {
            $rawMessage = self::getMailerMessage($key);
            self::assertNotNull($email);
            self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <profile@free-work.com>');
            self::assertEmailHeaderSame($rawMessage, 'to', $email);
            self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: Toujours en poste ?');
        }
    }
}
