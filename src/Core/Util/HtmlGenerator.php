<?php

namespace App\Core\Util;

use Faker\Factory as Faker;

class HtmlGenerator
{
    public static function generate(): string
    {
        $faker = Faker::create('fr_FR');

        $html = '';

        $elementsCount = mt_rand(4, 10);
        for ($k = 0; $k < $elementsCount; ++$k) {
            $rand = mt_rand(0, 7);
            if (0 === $rand) {
                $html .= sprintf('<p><ul><li>%s</li><li>%s</li><li>%s</li></ul></p>', ucfirst($faker->sentence()), ucfirst($faker->sentence()), ucfirst($faker->sentence()));
            } elseif (1 === $rand) {
                $html .= sprintf(
                    '<p>%s <a href="%s">%s</a> %s</p>',
                    ucfirst($faker->text()),
                    ucfirst($faker->url()),
                    ucfirst($faker->sentence(4)),
                    ucfirst($faker->text())
                );
            } else {
                $html .= sprintf('<p>%s</p>', ucfirst($faker->text(400)));
            }
        }

        return $html;
    }
}
