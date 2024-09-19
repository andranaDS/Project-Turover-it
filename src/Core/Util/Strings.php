<?php

namespace App\Core\Util;

use Gedmo\Sluggable\Util\Urlizer;
use s9e\TextFormatter\Bundles\Forum;
use Symfony\Component\String\UnicodeString;

class Strings extends \Nette\Utils\Strings
{
    public static function stripTags(string $string = '', string $allowed = null): string
    {
        $string = self::trim(str_replace(["\r", "\n"], ' ', strip_tags($string, $allowed)));
        if (null === $string = preg_replace('/\s+/', ' ', $string)) {
            throw new \InvalidArgumentException(sprintf('"%s" is invalid', $string));
        }

        return $string;
    }

    public static function bbCodeToHtml(string $oldValue): string
    {
        $emoticons = [
            ' :) ' => ' 🙂 ',
            ' :-) ' => ' 🙂 ',
            ' ;) ' => ' 😉 ',
            ' ;-) ' => ' 😉 ',
            ' :D ' => ' 😃 ',
            ' :-D ' => ' 😃 ',
            ' :( ' => ' 🙁 ',
            ' :-( ' => ' 🙁 ',
            ' :-* ' => ' 😘 ',
            ' :P ' => ' 😛 ',
            ' :-P ' => ' 😛 ',
            ' :p ' => ' 😛 ',
            ' :-p ' => ' 😛 ',
            ' ;P ' => ' 😛 ',
            ' ;-P ' => ' 😛 ',
            ' ;p ' => ' 😛 ',
            ' ;-p ' => ' 😛 ',
            ' :? ' => ' 😕 ',
            ' :-? ' => ' 😕 ',
            ' :| ' => ' 😐 ',
            ' :-| ' => ' 😐 ',
            ' :o ' => ' 😮 ',
            ':lol:' => '🤣',
            ':indecision:' => '😕',
            ':angry:' => '😡',
            ':tongue:' => '😛',
            ':omg:' => '😱',
            ':cry:' => '😢',
            ':shock:' => '😲',
            ':smile:' => '🙂',
            ':wink:' => '😉',
            ':roll:' => '🙄',
            ':mrgreen:' => '🙂',
            ':thumbup:' => '👍',
            ':frown:' => '😠',
            ':sad:' => '🙁',
            ':devil:' => '😈',
            ':cool:' => '😎',
            ':thumbdown:' => '👎',
            ':xd:' => '😆',
            ':oops:' => '😨',
            ':arrow:' => '➡️',
            ':idea:' => '💡',
            ':evil:' => '😈',
            ':u:' => '🙂',
            ':blush:' => '☺️',
            ':twisted:' => '😕',
            ':hourra:' => '🤗',
            ':1:' => '👍',
            ':+1:' => '👍',
            ':coeur:' => '😍',
            ':mail:' => '✉️',
            ':heart:' => '❤️',
            ':brokenheart:' => '💔',
            ':angel:' => '😇',
            ':kiss:' => '😘',
        ];

        $newValue = ' ' . $oldValue . ' ';

        $matches = [];
        preg_match_all('/:[\w+]+:/', $newValue, $matches);

        // 1. remove tokens
        $newValue = preg_replace_callback([
            '/\[\/?[\w=]+(:[a-z0-1]+)?(:[a-z0-9]{10}).*?\]/',
        ], function (array $matches) {
            return str_replace([$matches[1], $matches[2]], '', $matches[0]);
        }, $newValue);

        if (!\is_string($newValue)) {
            throw new \RuntimeException();
        }

        // 2. remove some tags
        $newValue = preg_replace([
            '/\[\/?color(=[\w]+)?\]/',
            '/=[0-9]{1,3}pt/',
            '/:https:/',
            "/\u{a0}/",
            "/\[img\](.+?)\[\/img\]/",
        ], [
            '',
            '',
            ': https:',
            ' ',
            '',
        ], $newValue);

        if (!\is_string($newValue)) {
            throw new \RuntimeException();
        }

        // 3. replace emojis
        $newValue = trim(str_replace(array_keys($emoticons), array_values($emoticons), $newValue));

        if (!\is_string($newValue)) {
            throw new \RuntimeException();
        }

        $parser = Forum::getParser();
        $renderer = Forum::getRenderer();
        $parser->disablePlugin('Emoji');
        $parser->disablePlugin('Emoticons');

        $xml = $parser->parse($newValue);

        $renderer->setParameters([
            'L_WROTE' => 'a écrit : ',
        ]);

        return $renderer->render($xml);
    }

    public static function substrToLength(string $haystack, int $length, string $more = '...'): string
    {
        $haystackLength = mb_strlen($haystack, 'utf-8');
        if ($haystackLength <= $length) {
            return $haystack;
        }

        $hasMore = false;
        if (!empty($more)) {
            $hasMore = true;
            $length -= mb_strlen($more, 'utf-8');
        }

        $result = mb_substr($haystack, 0, $length, 'utf-8');

        if (preg_match('/^[a-zàâçéèêëîïôûùüÿñæœ]*$/ui', $haystack[$length])) {
            $pos = mb_strrpos($result, ' ', 0, 'utf-8');
            $result = mb_substr($result, 0, \is_int($pos) ? $pos : null, 'utf-8');
        }

        if (true === $hasMore) {
            $result .= $more;
        }

        return $result;
    }

    public static function textToHtml(string $text): string
    {
        return implode('', Arrays::map(explode("\n", $text), static function (string $part) {
            return '<p>' . preg_replace('/\s+/', ' ', $part) . '</p>';
        }));
    }

    public static function defaultCase(
        string $string,
        array $delimiters = [' ', '-', '\''],
        array $exceptions = ['le', 'la', 'les', 'l\'', 'de', 'du', 'des', 'sur', 'sous', 'dans', 'en']): string
    {
        $string = self::lower($string);

        foreach ($delimiters as $delimiter) {
            $words = explode($delimiter, $string);
            $newWords = [];
            foreach ($words as $word) {
                if (\in_array(self::upper($word), $exceptions, true)) {
                    $word = self::upper($word);
                } elseif (!\in_array($word, $exceptions, true)) {
                    $word = self::firstUpper($word);
                }
                $newWords[] = $word;
            }
            $string = implode($delimiter, $newWords);
        }

        return $string;
    }

    public static function jobCase(string $string): string
    {
        $string = preg_replace_callback(sprintf("/\b(%s)\b/miu", implode('|', ['ceo', 'cto', 'it'])), static function (array $matches) {
            return Strings::upper($matches[0]);
        }, self::firstUpper(self::lower(self::trim($string))));

        if (null === $string) {
            throw new \RuntimeException();
        }

        return $string;
    }

    public static function transformForRss(?string $property): string
    {
        return '<![CDATA[' . ($property ?? '') . ']]';
    }

    /**
     * @see \Gedmo\Sluggable\SluggableListener
     */
    public static function slug(string $value, string $separator = '-'): string
    {
        $transliterator = [Urlizer::class, 'transliterate'];
        $urlizer = [Urlizer::class, 'urlize'];

        // Step 1: transliteration, changing 北京 to 'Bei Jing'
        $slug = $transliterator($value, $separator);

        // Step 2: urlization (replace spaces by '-' etc...)
        return $urlizer($slug, $separator);
    }

    public static function stripAccents(string $value): string
    {
        return (new UnicodeString($value))->ascii()->toString();
    }
}
