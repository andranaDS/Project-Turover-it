<?php

namespace App\Sync\Util;

class Converter
{
    public static function convertArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $array[$key] = self::convertArray($value);
            } elseif (\is_string($value)) {
                $array[$key] = self::convertString($value);
            }
        }

        return $array;
    }

    public static function convertString(?string $string): ?string
    {
        if (null === $string) {
            return null;
        }

        $string = trim($string);

        if ('' === $string) {
            return null;
        }

        // replace html entities in caps
        $string = preg_replace_callback(
            '/&[A-Z]+;/',
            function (array $matches) {
                return mb_strtolower($matches[0], 'utf8');
            },
            $string
        );

        if (null === $string) {
            return null;
        }

        $string = self::unicodeDecode($string);

        return self::htmlEntityDecode($string);
    }

    public static function htmlEntityDecode(string $string): string
    {
        $string = str_ireplace('&amp;', '&', $string);
        $string = str_ireplace('&#133;', '…', $string);
        $string = str_ireplace('&#61692;', '•', $string);
        $string = str_ireplace('&#8203;', ' ', $string);
        $string = str_ireplace('&#156;', 'œ', $string);

        return html_entity_decode($string, \ENT_QUOTES);
    }

    public static function unicodeDecode(string $string): string
    {
        $string = str_ireplace('u0001', '?', $string);
        $string = str_ireplace('u0002', '?', $string);
        $string = str_ireplace('u0003', '?', $string);
        $string = str_ireplace('u0004', '?', $string);
        $string = str_ireplace('u0005', '?', $string);
        $string = str_ireplace('u0006', '?', $string);
        $string = str_ireplace('u0007', '•', $string);
        $string = str_ireplace('u0008', '?', $string);
        $string = str_ireplace('u0009', '?', $string);
        $string = str_ireplace('u000A', '?', $string);
        $string = str_ireplace('u000B', '?', $string);
        $string = str_ireplace('u000C', '?', $string);
        $string = str_ireplace('u000D', '?', $string);
        $string = str_ireplace('u000E', '?', $string);
        $string = str_ireplace('u000F', '¤', $string);
        $string = str_ireplace('u0010', '?', $string);
        $string = str_ireplace('u0011', '?', $string);
        $string = str_ireplace('u0012', '?', $string);
        $string = str_ireplace('u0013', '?', $string);
        $string = str_ireplace('u0014', '¶', $string);
        $string = str_ireplace('u0015', '§', $string);
        $string = str_ireplace('u0016', '?', $string);
        $string = str_ireplace('u0017', '?', $string);
        $string = str_ireplace('u0018', '?', $string);
        $string = str_ireplace('u0019', '?', $string);
        $string = str_ireplace('u001A', '?', $string);
        $string = str_ireplace('u001B', '?', $string);
        $string = str_ireplace('u001C', '?', $string);
        $string = str_ireplace('u001D', '?', $string);
        $string = str_ireplace('u001E', '?', $string);
        $string = str_ireplace('u001F', '?', $string);
        $string = str_ireplace('u0020', ' ', $string);
        $string = str_ireplace('u0021', '!', $string);
        $string = str_ireplace('u0022', '"', $string);
        $string = str_ireplace('u0023', '#', $string);
        $string = str_ireplace('u0024', '$', $string);
        $string = str_ireplace('u0025', '%', $string);
        $string = str_ireplace('u0026', '&', $string);
        $string = str_ireplace('u0027', "'", $string);
        $string = str_ireplace('u0028', '(', $string);
        $string = str_ireplace('u0029', ')', $string);
        $string = str_ireplace('u002A', '*', $string);
        $string = str_ireplace('u002B', '+', $string);
        $string = str_ireplace('u002C', ',', $string);
        $string = str_ireplace('u002D', '-', $string);
        $string = str_ireplace('u002E', '.', $string);
        $string = str_ireplace('u2026', '…', $string);
        $string = str_ireplace('u002F', '/', $string);
        $string = str_ireplace('u0030', '0', $string);
        $string = str_ireplace('u0031', '1', $string);
        $string = str_ireplace('u0032', '2', $string);
        $string = str_ireplace('u0033', '3', $string);
        $string = str_ireplace('u0034', '4', $string);
        $string = str_ireplace('u0035', '5', $string);
        $string = str_ireplace('u0036', '6', $string);
        $string = str_ireplace('u0037', '7', $string);
        $string = str_ireplace('u0038', '8', $string);
        $string = str_ireplace('u0039', '9', $string);
        $string = str_ireplace('u003A', ':', $string);
        $string = str_ireplace('u003B', ';', $string);
        $string = str_ireplace('u003C', '<', $string);
        $string = str_ireplace('u003D', '=', $string);
        $string = str_ireplace('u003E', '>', $string);
        $string = str_ireplace('u2264', '=', $string);
        $string = str_ireplace('u2265', '=', $string);
        $string = str_ireplace('u003F', '?', $string);
        $string = str_ireplace('u0040', '@', $string);
        $string = str_ireplace('u0041', 'A', $string);
        $string = str_ireplace('u0042', 'B', $string);
        $string = str_ireplace('u0043', 'C', $string);
        $string = str_ireplace('u0044', 'D', $string);
        $string = str_ireplace('u0045', 'E', $string);
        $string = str_ireplace('u0046', 'F', $string);
        $string = str_ireplace('u0047', 'G', $string);
        $string = str_ireplace('u0048', 'H', $string);
        $string = str_ireplace('u0049', 'I', $string);
        $string = str_ireplace('u004A', 'J', $string);
        $string = str_ireplace('u004B', 'K', $string);
        $string = str_ireplace('u004C', 'L', $string);
        $string = str_ireplace('u004D', 'M', $string);
        $string = str_ireplace('u004E', 'N', $string);
        $string = str_ireplace('u004F', 'O', $string);
        $string = str_ireplace('u0050', 'P', $string);
        $string = str_ireplace('u0051', 'Q', $string);
        $string = str_ireplace('u0052', 'R', $string);
        $string = str_ireplace('u0053', 'S', $string);
        $string = str_ireplace('u0054', 'T', $string);
        $string = str_ireplace('u0055', 'U', $string);
        $string = str_ireplace('u0056', 'V', $string);
        $string = str_ireplace('u0057', 'W', $string);
        $string = str_ireplace('u0058', 'X', $string);
        $string = str_ireplace('u0059', 'Y', $string);
        $string = str_ireplace('u005A', 'Z', $string);
        $string = str_ireplace('u005B', '[', $string);
        $string = str_ireplace('u005C', '\\', $string);
        $string = str_ireplace('u005D', ']', $string);
        $string = str_ireplace('u005E', '^', $string);
        $string = str_ireplace('u005F', '_', $string);
        $string = str_ireplace('u0060', '`', $string);
        $string = str_ireplace('u0061', 'a', $string);
        $string = str_ireplace('u0062', 'b', $string);
        $string = str_ireplace('u0063', 'c', $string);
        $string = str_ireplace('u0064', 'd', $string);
        $string = str_ireplace('u0065', 'e', $string);
        $string = str_ireplace('u0066', 'f', $string);
        $string = str_ireplace('u0067', 'g', $string);
        $string = str_ireplace('u0068', 'h', $string);
        $string = str_ireplace('u0069', 'i', $string);
        $string = str_ireplace('u006A', 'j', $string);
        $string = str_ireplace('u006B', 'k', $string);
        $string = str_ireplace('u006C', 'l', $string);
        $string = str_ireplace('u006D', 'm', $string);
        $string = str_ireplace('u006E', 'n', $string);
        $string = str_ireplace('u006F', 'o', $string);
        $string = str_ireplace('u0070', 'p', $string);
        $string = str_ireplace('u0071', 'q', $string);
        $string = str_ireplace('u0072', 'r', $string);
        $string = str_ireplace('u0073', 's', $string);
        $string = str_ireplace('u0074', 't', $string);
        $string = str_ireplace('u0075', 'u', $string);
        $string = str_ireplace('u0076', 'v', $string);
        $string = str_ireplace('u0077', 'w', $string);
        $string = str_ireplace('u0078', 'x', $string);
        $string = str_ireplace('u0079', 'y', $string);
        $string = str_ireplace('u007A', 'z', $string);
        $string = str_ireplace('u007B', '{', $string);
        $string = str_ireplace('u007C', '|', $string);
        $string = str_ireplace('u007D', '}', $string);
        $string = str_ireplace('u02DC', '˜', $string);
        $string = str_ireplace('u007E', '~', $string);
        $string = str_ireplace('u007F', '', $string);
        $string = str_ireplace('u00A2', '¢', $string);
        $string = str_ireplace('u00A3', '£', $string);
        $string = str_ireplace('u00A4', '¤', $string);
        $string = str_ireplace('u20AC', '€', $string);
        $string = str_ireplace('u00A5', '¥', $string);
        $string = str_ireplace('u0026quot;', '"', $string);
        $string = str_ireplace('u0026gt;', '>', $string);

        return str_ireplace('u0026lt;', '>', $string);
    }
}
