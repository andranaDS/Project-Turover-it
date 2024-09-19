<?php

namespace App\User\Tests\Unit\Entity;

use App\User\Entity\User;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private static int $passwordRequestTtl = 60;

    public function testIsPasswordRequestActiveWithActiveRequest(): void
    {
        $user = (new User())
            ->setPasswordRequestedAt((new \DateTime())->modify('-1 second'))
        ;

        self::assertTrue($user->isPasswordRequestActive(self::$passwordRequestTtl));
    }

    public function testIsPasswordRequestActiveWithExpiredRequest(): void
    {
        $user = (new User())
            ->setPasswordRequestedAt((new \DateTime())->modify('-120 seconds'))
        ;

        self::assertFalse($user->isPasswordRequestActive(self::$passwordRequestTtl));
    }

    private static int $emailRequestTtl = 60;

    public function testIsEmailRequestActiveWithActiveRequest(): void
    {
        $user = (new User())
            ->setEmailRequestedAt((new \DateTime())->modify('-1 second'))
        ;

        self::assertTrue($user->isEmailRequestActive(self::$emailRequestTtl));
    }

    public function testIsEmailRequestActiveWithExpiredRequest(): void
    {
        $user = (new User())
            ->setEmailRequestedAt((new \DateTime())->modify('-120 seconds'))
        ;

        self::assertFalse($user->isEmailRequestActive(self::$emailRequestTtl));
    }

    public function testIsDeleted(): void
    {
        $user = (new User())
            ->setDeletedAt(Carbon::now())
        ;

        self::assertTrue($user->isDeleted());
    }

    public function testIsNotDeleted(): void
    {
        $user = (new User())
            ->setDeletedAt(null)
        ;

        self::assertFalse($user->isDeleted());
    }

    public function testGetUsername(): void
    {
        $user = (new User())
            ->setEmail('hello@hello.com')
        ;

        self::assertSame('hello@hello.com', $user->getUsername());
    }

    public function testToString(): void
    {
        $user = (new User())
            ->setNickname('hello')
            ->setEmail('hello@hello.com')
        ;

        self::assertSame('hello (hello@hello.com)', $user->__toString());
    }

    public function testTermsOfServiceAccepted(): void
    {
        $user = (new User())
            ->setTermsOfService(true)
        ;

        self::assertNotNull($user->getTermsOfServiceAcceptedAt());
    }

    public function testTermsOfServiceNotAccepted(): void
    {
        $user = (new User())
            ->setTermsOfService(false)
        ;

        self::assertNull($user->getTermsOfServiceAcceptedAt());
    }
}
