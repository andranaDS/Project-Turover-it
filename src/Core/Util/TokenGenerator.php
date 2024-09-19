<?php

namespace App\Core\Util;

class TokenGenerator
{
    /**
     * Generate a token with number and chars.
     */
    public static function generate(int $length = 16): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars, 'utf-8');

        for ($i = 0, $token = ''; $i < $length; ++$i) {
            $index = mt_rand(0, $count - 1);
            $token .= mb_substr($chars, $index, 1);
        }

        return $token;
    }

    /**
     * Generate a token with number and chars from a value.
     */
    public static function generateFromValue(string $value, int $length = 16): string
    {
        return strrev(substr(sha1($value), 0, $length));
    }
}
