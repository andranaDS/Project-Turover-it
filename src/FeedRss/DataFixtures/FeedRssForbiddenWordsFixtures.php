<?php

namespace App\FeedRss\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\FeedRss\Entity\FeedRssForbiddenWord;
use Doctrine\Persistence\ObjectManager;

class FeedRssForbiddenWordsFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $word) {
            $forbiddenWOrk = (new FeedRssForbiddenWord())->setName($word);

            $manager->persist($forbiddenWOrk);
        }

        $manager->flush();
    }

    public function getData(): array
    {
        return [
            'civil',
            'transport',
            'relation',
            'business',
            'rh',
            'polyvalent',
            'traducteur',
            'redacteur',
            'adjoint',
            'operateur',
            'clientele',
            'account',
            'country',
            'mecaniques',
            'mecanique',
            'acheteur',
            'electricite',
            'ssii',
            'journee',
            'agence',
            'business',
            'administratif',
            'recrutement',
            'webmarketing',
            'marketing',
            'polyvalent',
            'juriste',
            'juridique',
            'affaires',
            'affaire',
            'commercial',
            'qualite',
            'economiste',
            'dessinateur',
            'climatisation',
            'maitre',
            'employe',
            'bureau',
            'procedes',
            'administratif',
            'navale',
            'commande',
            'vehicule',
            'plombier',
            'plomberie',
            'agence',
            'agences',
            'vente',
            'ventes',
            'revetements',
            'materiaux',
            'acheteur',
            'hydroelectricite',
            'marketing',
            'sourcing',
            'batterie',
            'batteries',
            'affaire',
            'affaires',
            'thermique',
            'mecaniques',
            'mecanique',
            'projeteur',
            'dessinateur',
            'projeteurs',
            'dessinateurs',
            'electrique',
            'business',
            'assistante',
            'paie',
            'vente',
            'controleur',
            'comptable',
            'commercial',
            'prospecteur',
            'recouvrement',
            'couvreur',
            'menuisier',
            'thermicien',
            'electricite',
            'televendeur',
            'teleprospecteur',
            'plombier',
            'chauffeur',
            'publicite',
            'maintenance',
            'electricien',
            'chaudronnerie',
            'mecanicien',
            'conducteur',
            'soudure',
            'travaux',
            'charpentier',
            'atelier',
            'paie',
            'direction',
            'tuyauterie',
            'approvisionneur',
            'logistique',
            'ferroviere',
            'coffreur',
            'assistant',
            'recrutement',
            'normalisation',
            'ssii',
            'de direction',
            'de gestion',
            'grands comptes',
            'service client',
            'de recherche',
            'de recherce',
        ];
    }
}
