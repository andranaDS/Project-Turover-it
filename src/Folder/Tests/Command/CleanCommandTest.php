<?php

namespace Command;

use App\Folder\Entity\Folder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CleanCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:folder:clean');
        $commandTester = new CommandTester($command);

        if (null === $container = $kernel->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        /** @var ServiceEntityRepository $folderRepository */
        $folderRepository = $em->getRepository(Folder::class);

        // init
        /** @var Folder $viewedFolder */
        $viewedFolder = $folderRepository->findOneById(1);
        self::assertNotNull($viewedFolder);
        self::assertSame('viewed', $viewedFolder->getType());

        /** @var Folder $cartFolder */
        $cartFolder = $folderRepository->findOneById(2);
        self::assertNotNull($cartFolder);
        self::assertSame('cart', $cartFolder->getType());

        /** @var Folder $yesterdayCartFolder */
        $yesterdayCartFolder = $folderRepository->findOneById(3);
        self::assertNotNull($yesterdayCartFolder);
        self::assertSame('yesterday_cart', $yesterdayCartFolder->getType());

        // before execution
        self::assertSame(4, $viewedFolder->getUsersCount());
        self::assertSame(3, $cartFolder->getUsersCount());
        self::assertSame(2, $yesterdayCartFolder->getUsersCount());

        // execution
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        $em->refresh($viewedFolder);
        $em->refresh($cartFolder);
        $em->refresh($yesterdayCartFolder);

        // after execution
        self::assertSame(0, $viewedFolder->getUsersCount());
        self::assertSame(0, $viewedFolder->getUsers()->count());

        self::assertSame(0, $cartFolder->getUsersCount());
        self::assertSame(0, $cartFolder->getUsers()->count());

        self::assertSame(3, $yesterdayCartFolder->getUsersCount());
        self::assertSame(3, $yesterdayCartFolder->getUsers()->count());
    }
}
