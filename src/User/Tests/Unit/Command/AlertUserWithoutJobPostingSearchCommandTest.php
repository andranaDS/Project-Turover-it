<?php

namespace App\User\Tests\Unit\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AlertUserWithoutJobPostingSearchCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:alert:user-without-job-posting-search');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        self::assertEmailCount(1);

        $rawMessage = self::getMailerMessage();
        self::assertNotNull($rawMessage);
        self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <jobs@free-work.com>');
        self::assertEmailHeaderSame($rawMessage, 'to', 'user-registration-3-days-ago@free-work.fr');
        self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: User Registration 3 days ago, ne ratez aucune opportunité !');
    }
}
