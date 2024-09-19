<?php

namespace App\Forum\Tests\Unit\Command;

use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ForumStatisticsCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:forum:statistics');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        self::assertEmailCount(1);

        $rawMessage = self::getMailerMessage();

        self::assertNotNull($rawMessage);
        self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <forum@free-work.com>');
        self::assertEmailHeaderSame($rawMessage, 'to', 'users@free-work.fr');
        self::assertEmailHeaderSame($rawMessage, 'subject',
            sprintf(
                'TEST: Récapitulatif de l\'activité du forum du %s',
                Carbon::yesterday()->format('d/m/Y')
            )
        );

        self::assertEmailTextBodyContains($rawMessage, '0 nouveau topic');
        self::assertEmailTextBodyContains($rawMessage, '0 nouveau post');
        self::assertEmailTextBodyContains($rawMessage, '0 contributeur');
        self::assertEmailTextBodyContains($rawMessage, '0 nouveau contributeur');
        self::assertEmailTextBodyContains($rawMessage, '3 nouveaux contributeurs lors des 6 dernier mois');
    }
}
