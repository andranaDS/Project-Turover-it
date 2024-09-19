<?php

namespace App\User\Tests\Unit\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AlertUserWithoutResumeDocumentCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:alert:user-profile-uncomplete');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        self::assertEmailCount(5);

        $emails = [
            0 => 'user-to-enable-with-expired-token@free-work.fr',
            2 => 'user-without-document-registration-2-days@free-work.fr',
            4 => 'user-without-document-registration-7-days@free-work.fr',
            6 => 'user-without-document-registration-40-days@free-work.fr',
            8 => 'user-without-document-registration-30-days@free-work.fr',
        ];

        foreach ($emails as $key => $email) {
            $rawMessage = self::getMailerMessage($key);
            self::assertNotNull($email);
            self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <profile@free-work.com>');
            self::assertEmailHeaderSame($rawMessage, 'to', $email);
            self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: Complétez votre profil IT');
        }
    }
}
