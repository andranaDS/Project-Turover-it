<?php

namespace App\JobPosting\Enum;

use Greg0ire\Enum\AbstractEnum;

final class Contract extends AbstractEnum
{
    public const PERMANENT = 'permanent';
    public const FIXED_TERM = 'fixed-term';
    public const INTERNSHIP = 'internship';
    public const CONTRACTOR = 'contractor';
    public const APPRENTICESHIP = 'apprenticeship';
    public const INTERCONTRACT = 'intercontract';

    public static function getWorkValues(): array
    {
        return [self::PERMANENT, self::FIXED_TERM, self::INTERNSHIP, self::APPRENTICESHIP];
    }

    public static function getFreeValues(): array
    {
        return [self::CONTRACTOR, self::INTERCONTRACT];
    }

    public static function getTemporaryValues(): array
    {
        return [self::APPRENTICESHIP, self::FIXED_TERM, self::INTERNSHIP, self::CONTRACTOR, self::INTERCONTRACT];
    }

    public static function getPermanentValues(): array
    {
        return [self::PERMANENT];
    }

    public static function isFree(?string $contract): bool
    {
        return \in_array($contract, self::getFreeValues(), true);
    }

    public static function isWork(?string $contract): bool
    {
        return \in_array($contract, self::getWorkValues(), true);
    }

    public static function isTemporary(?string $contract): bool
    {
        return \in_array($contract, self::getTemporaryValues(), true);
    }

    public static function isPermanent(?string $contract): bool
    {
        return \in_array($contract, self::getTemporaryValues(), true);
    }

    public static function getFreeWorkValues(): array
    {
        return [self::FIXED_TERM, self::INTERNSHIP, self::PERMANENT, self::APPRENTICESHIP, self::CONTRACTOR];
    }

    public static function getTurnoverValues(): array
    {
        return [self::INTERCONTRACT];
    }
}
