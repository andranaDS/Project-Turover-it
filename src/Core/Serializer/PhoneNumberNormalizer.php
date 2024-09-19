<?php

namespace App\Core\Serializer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Serializer\Normalizer\PhoneNumberNormalizer as BasePhoneNumberNormalizer;

final class PhoneNumberNormalizer extends BasePhoneNumberNormalizer
{
    private PhoneNumberUtil $phoneNumberUtil;
    private string $region;

    public function __construct(PhoneNumberUtil $phoneNumberUtil, $region = PhoneNumberUtil::UNKNOWN_REGION, $format = PhoneNumberFormat::E164)
    {
        parent::__construct($phoneNumberUtil, $region, $format);
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->region = $region;
    }

    public function denormalize($data, $class, $format = null, array $context = []): ?PhoneNumber
    {
        if (null === $data) {
            return null;
        }

        try {
            return $this->phoneNumberUtil->parse($data, $this->region);
        } catch (NumberParseException $e) {
            return null;
        }
    }
}
