<?php

namespace App\User\Tests\Unit\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ProfileNotVisibleEmailCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:alert:profile-not-visible');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        self::assertEmailCount(2);

        $emails = [
            0 => 'user-non-visible-status-3-months@free-work.fr',
            2 => 'user-non-visible-status-6-months@free-work.fr',
        ];

        foreach ($emails as $key => $email) {
            $rawMessage = self::getMailerMessage($key);
            self::assertNotNull($email);
            self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <profile@free-work.com>');
            self::assertEmailHeaderSame($rawMessage, 'to', $email);
            self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: Votre profil n’est pas visible par les recruteurs');
        }
    }
}
