<?php

namespace App\Sync\Tests\Unit\Transformer\JobPosting;

use App\Sync\Transformer\JobPosting\LocationTransformer;
use PHPUnit\Framework\TestCase;

class LocationTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        self::assertSame('Villepinte Ile de France', LocationTransformer::transform('Villepinte (Ile de France)'));
        self::assertSame('Villepinte', LocationTransformer::transform('Villepinte (environ)'));
        self::assertSame('Villepinte', LocationTransformer::transform('Villepinte'));

        self::assertSame('La Défense, Hauts-de-Seine, Île-de-France', LocationTransformer::transform('La Défense (92)'));

        self::assertSame('Paris - 2ème arrondissement, Paris, Île-de-France', LocationTransformer::transform('Paris - 2ème arrondissement (75)'));
        self::assertSame('Aix-en-Provence, Bouches-du-Rhône, Provence-Alpes-Côte d\'Azur', LocationTransformer::transform('Aix-en-Provence (13)'));
        self::assertSame('Courbevoie, Hauts-de-Seine, Île-de-France', LocationTransformer::transform('Courbevoie (92)'));

        self::assertSame('Paris', LocationTransformer::transform('Paris, Toulouse ou Bordeaux'));
        self::assertSame('Paris', LocationTransformer::transform('Bordeaux, Paris ou Toulouse'));
        self::assertSame('Toulouse', LocationTransformer::transform('Bordeaux ou Toulouse'));
        self::assertSame('Marseille', LocationTransformer::transform('Tele travaille et Marseille'));
        self::assertSame('Lyon', LocationTransformer::transform('Lyon environ 1h'));
        self::assertSame('Lyon', LocationTransformer::transform('Lyon et périphérie (69003)'));
        self::assertSame('Roubaix', LocationTransformer::transform('Roubaix, HDF'));

        self::assertSame('Hauts-de-Seine, Île-de-France', LocationTransformer::transform('(92)'));
        self::assertSame('Hauts-de-Seine, Île-de-France', LocationTransformer::transform('92'));
        self::assertSame('Vienne, Nouvelle-Aquitaine', LocationTransformer::transform('86'));
        self::assertSame('La Réunion, La Réunion', LocationTransformer::transform('(974)'));
        self::assertSame('Mayotte, Mayotte', LocationTransformer::transform('976'));

        self::assertSame('France', LocationTransformer::transform('France'));
        self::assertSame('England', LocationTransformer::transform('England'));

        self::assertSame('Val-de-Marne, Île-de-France', LocationTransformer::transform('94 - IDF (Proche Paris)'));
        self::assertSame('Hauts-de-Seine, Île-de-France', LocationTransformer::transform('92 (IDF, Paris sud)'));
        self::assertSame('Seine-Saint-Denis, Île-de-France', LocationTransformer::transform('IDF 93 (Proche Paris)'));
        self::assertSame('Essonne, Île-de-France', LocationTransformer::transform('Proche Evry (91 - IDF)'));
        self::assertSame('Seine-Saint-Denis, Île-de-France', LocationTransformer::transform('93 Est (IDF - Proche Paris)'));

        self::assertSame('Île-de-France', LocationTransformer::transform('IDF'));
        self::assertSame('Île-de-France', LocationTransformer::transform('Ipsum IDF'));
        self::assertSame('Île-de-France', LocationTransformer::transform('Wissous IDF'));
        self::assertSame('Provence-Alpes-Côte d\'Azur', LocationTransformer::transform('PACA – Sophia Antipolis – Occasional international travel'));
        self::assertSame('Hauts-de-France', LocationTransformer::transform('Lorem, HDF'));

        self::assertNull(LocationTransformer::transform('remote'));
        self::assertNull(LocationTransformer::transform('teletravail'));
        self::assertNull(LocationTransformer::transform('télétravail'));
        self::assertNull(LocationTransformer::transform('Remote'));
        self::assertNull(LocationTransformer::transform('Teletravail'));
        self::assertNull(LocationTransformer::transform('Télétravail'));
        self::assertNull(LocationTransformer::transform('homeworking'));
        self::assertNull(LocationTransformer::transform('Homeworking'));
    }
}
