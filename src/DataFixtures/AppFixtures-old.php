<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\PostalAdress;
use App\Entity\PostLike;
use App\Entity\State;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;

class AppFixtures extends Fixture
{

    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= count($this->getState()); $i++) {
            $state = new State();
            $state->setName($this->getState[$i]);
            $state->setCode($i);
        }
    }

    public function getState()
    {
        return [
            'Ain',
            'Aisne',
            'Allier',
            'Hautes-Alpes',
            'Alpes-de-Haute-Provence',
            'Alpes-Maritimes',
            'Ardèche',
            'Ardennes',
            'Ariège',
            'Aube',
            'Aude',
            'Aveyron',
            'Bouches-du-Rhône',
            'Calvados',
            'Cantal',
            'Charente',
            'Charente-Maritime',
            'Cher',
            'Corrèze',
            'Corse-du-sud',
            'Haute-corse',
            'Côte-d\'or',
            'Côtes-d\'armor',
            'Creuse',
            'Dordogne',
            'Doubs',
            'Drôme',
            'Eure',
            'Eure-et-Loir',
            'Finistère',
            'Gard',
            'Haute-Garonne',
            'Gers',
            'Gironde',
            'Hérault',
            'Ile-et-Vilaine',
            'Indre',
            'Indre-et-Loire',
            'Isère',
            'Jura',
            'Landes',
            'Loir-et-Cher',
            'Loire',
            'Haute-Loire',
            'Loire-Atlantique',
            'Loiret',
            'Lot',
            'Lot-et-Garonne',
            'Lozère',
            'Maine-et-Loire',
            'Manche',
            'Marne',
            'Haute-Marne',
            'Mayenne',
            'Meurthe-et-Moselle',
            'Meuse',
            'Morbihan',
            'Moselle',
            'Nièvre',
            'Nord',
            'Oise',
            'Orne',
            'Pas-de-Calais',
            'Puy-de-Dôme',
            'Pyrénées-Atlantiques',
            'Hautes-Pyrénées',
            'Pyrénées-Orientales',
            'Bas-Rhin',
            'Haut-Rhin',
            'Rhône',
            'Haute-Saône',
            'Saône-et-Loire',
            'Sarthe',
            'Savoie',
            'Haute-Savoie',
            'Paris',
            'Seine-Maritime',
            'Seine-et-Marne',
            'Yvelines',
            'Deux-Sèvres',
            'Somme',
            'Tarn',
            'Tarn-et-Garonne',
            'Var',
            'Vaucluse',
            'Vendée',
            'Vienne',
            'Haute-Vienne',
            'Vosges',
            'Yonne',
            'Territoire de Belfort',
            'Essonne',
            'Hauts-de-Seine',
            'Seine-Saint-Denis',
            'Val-de-Marne',
            'Val-d\'oise',
            'Mayotte',
            'Guadeloupe',
            'Guyane',
            'Martinique',
            'Réunion',
        ];
    }

    private function getRandomState(): string
    {
        $tagNames = $this->getState();
        shuffle($tagNames);
        $selectedTags = \array_slice($tagNames, 0, random_int(1, 2));

        return $selectedTags;
    }
}
