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
use App\Entity\PostLike;
use App\Entity\PostalAdress;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $auser = new User();
        $date = new DateTime('NOW');
        $birthday = new DateTime('05-08-1973');
        $age = $date->diff($birthday)->format('Y');
        $password = $this->passwordEncoder->encodePassword($auser, 'BebShow5873');
        $picture = 'https://randomuser.me/api/portraits/men/55.jpg';
        $auser->setEmail('againmusician@gmail.com');
        $auser->setPassword($password);
        $auser->setUsername('Bebshow');
        $auser->setFullName('Plaire Cédric');
        $auser->setUseGravatar(false);
        $auser->setAvatarPerso($picture);
        $auser->createGravatarUrl();
        $auser->setBirthday($date);
        $auser->setAge((int) $age);
        $auser->setGenre(true);
        $auser->setRoles(['ROLE_ADMIN']);

        $manager->persist($auser);
        $postAdresse = new PostalAdress();
        $postAdresse->setState('Nouvelle Aquitaine')
            ->setStreet('23 rue des vendanges')
            ->setCountry('France')
            ->setCity('Saintes')
            ->setCountry('France')
            ->setState('Nouvelle Aquitaine')
            ->setZipCode(17100)
            ->setBuilding('C')
            ->setUser($auser);
        $manager->persist($postAdresse);
        $auser->setAddress($postAdresse);

        $users = [];
        $users[] = $auser;
        $genres = ['male', 'female'];

        for ($i = 0; $i < 10; $i++) {
            $fakeUser = new User();
            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $first = $faker->firstName($genre);
            $last = $faker->lastName;
            $fakeUser->setEmail($faker->email);
            $fakeUser->setPassword($this->passwordEncoder->encodePassword($fakeUser, 'password'));
            $fakeUser->setUsername($faker->userName);
            $fakeUser->setGenre($genre == 'male' ? true : false);
            $fakeUser->setFullName($first . ' ' . $last);
            $fakeUser->setBirthday($faker->dateTimeThisCentury);
            $fakeUser->setUseGravatar(false);
            $fakeUser->setAge(mt_rand(18, 65));
            $fakeUser->setRoles(['ROLE_USER']);
            $fakeUser->setAvatarPerso($picture);
            $fakeUser->createGravatarUrl();

            $manager->persist($fakeUser);

            /* adresse entity to not surcharge User entity */
            $adresse = new PostalAdress();
            $adresse->setCountry($faker->country)
                ->setState($faker->randomElement($this->getStateData()))
                ->setUser($fakeUser)
                ->setZipCode((int) $faker->postcode)
                ->setStreet($faker->streetAddress)
                ->setBuilding($faker->buildingNumber)
                ->setCity($faker->city);
            $manager->persist($adresse);
            $fakeUser->setAddress($adresse);
            $users[] = $fakeUser;
        }

        /* Tag for Post, with link for later call */
        foreach ($this->getTagsData() as $j => $myTag) {
            $tag = new Tag();
            $tag->setName($myTag);

            $manager->persist($tag);
            $this->addReference('tag-' . $myTag, $tag);
        }
        /*for ($j = 0; $j < $this->getTagsData(); $j++) {
            $tag = new Tag();
            $tag->setName($faker->randomElement($this->getTagsData()));

            $manager->persist($tag);
            $this->addReference('tag-' . $j, $tag);
        };*/

        /* dummy Post for web-site */
        for ($k = 0; $k < 30; $k++) {
            $post = new Post();
            $title = $faker->sentence();
            $color1 = $faker->hexColor;
            $color2 = $faker->hexColor;
            $c1 = substr($color1, 1);
            $c2 = substr($color2, 1);
            $postImg = "https://via.placeholder.com/400x200/" . $c1 . "/" . $c2 . "?text=" . $faker->city . "-" . $faker->country;
            //$largeImage = "https://via.placeholder.com/1000x400/" . $c1 . "/" . $c2 . "?text=" . $faker->city . "-" . $faker->country;;
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users) - 1)];

            $post->setTitle($title)
                ->setSummary($introduction)
                ->setContent($content)
                ->setAuthor($user)
                ->setPublishedAt($faker->dateTimeThisYear)
                ->setImage($postImg);

            $post->initializeSlug();

            for ($t = 0; $t < (mt_rand(1, 4)); $t++) {
                $post->addTag(...$this->getRandomTags());
            }

            /* commentaire des articles */
            for ($l = 0; $l < 5; $l++) {
                $comment = new Comment();
                $commentUser = $users[mt_rand(0, count($users) - 1)];
                $comment->setAuthor($commentUser);
                $comment->setContent($faker->sentences(3, true));
                $comment->setPost($post);
                $comment->setPublishedAt($faker->dateTimeThisYear);

                $manager->persist($comment);
            }

            /* like pour les articles */
            for ($j = 0; $j < mt_rand(0, 10); $j++) {
                $like = new PostLike();
                $like->setCreatedAt($faker->dateTimeThisMonth);
                $like->setPost($post)
                    ->setUser($faker->randomElement($users));

                $manager->persist($like);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    # departement_nom
    public function getStateData()
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


    public function getTagsData()
    {
        return [
            "internet",
            "multimédia",
            "JavaScript",
            "Google",
            "blog",
            "navigateur",
            "HTML",
            "logiciel",
            "www",
            "cookie",
            "e-commerce",
            "formulaire",
            "WorldWideWeb",
            "annuaire",
            "PHP",
            "Firefox",
            "référencement",
            "internaute",
            "ordinateur",
            "safari",
            "webmaster",
            "wiki",
            "CSS",
            "framework",
            "toile",
            "URL",
            "Facebook",
            "publicité",
            "moteur-de-recherche",
            "ergonomie",
            "messagerie",
            "standard",
            "proxy",
            "webmestre",
            "contenu",
            "naviguer",
            "portail",
            "serveur",
            "site-web",
            "Yahoo",
            "Apache",
            "hébergeur",
            "page",
            "page-web",
            "sémantique",
            "application",
            "communication",
            "forum",
            "hypertexte",
            "média",
            "utilisateur-accessible",
            "blogueur",
            "développeur",
            "interactif",
            "technologie",
            "webmail",
            "actualité",
            "client",
            "diffusion",
            "marketing",
            "Mozilla-plateforme",
            "web-sémantique",
            "fonctionnalité",
            "hyperlien",
            "indexation",
            "Internet-Explorer",
            "intranet",
            "langage",
            "minitel",
            "outil",
            "Tim-Berners-Lee",
            "Adobe",
            "asp",
            "chrome",
            "consultation",
            "http",
            "marque-page",
            "Mosaic",
            "Netscape",
            "plugin",
            "usenet",
            "XML",
            "hacker",
            "HTML5",
            "MySQL",
            "site",
            "widget",
            "Alexa",
            "applicatif",
            "collaboratif",
            "disponible",
            "document",
            "envoi",
            "information-Java",
            "lien-participatif",
            "api-digital",
            "intégrateur",
            "métadonnée",
            "mobile",
            "navigateur-web",
            "recommandation",
            "réseaux-sociaux",
            "Tor",
            "WordPress",
            "Android",
            "applet",
            "authentification",
            "courriel",
            "fournisseur",
            "owl-rdf",
            "RSS-server",
            "serveur-web",
            "téléchargement",
            "Twitter-uniface",
            "vidéo",
            "courrier-électronique",
            "design",
            "Gmail",
            "IIS",
            "SVG",
            "visioconférence",
            "XHTML",
            "accessibilité",
            "AOL-développement",
            "informathèque",
            "Mozilla-Firefox-Pagerank",
            "réseau",
            "servlet",
            "WebKit",
            "websérie",
            "Berners-Lee",
            "bing",
            "cyber-réputation",
            "défacement",
            "déploiement-dynamique",
            "électronique",
            "Google-Chrome",
            "infolettre",
            "interactivité",
            "interface",
            "médias-sociaux",
            "navigation",
            "NTIC",
            "permalien",
            "pop-up",
            "présentation",
            "rediriger",
            "réseau-mondial",
            "toile-mondiale",
            "voie-de-communication",
            "W³",
            "wan",
            "web-profond",
            "web-surfacique",
            "YouTube"
        ];
    }

    private function getRandomTags(): array
    {
        $tagNames = $this->getTagsData();
        shuffle($tagNames);
        $selectedTags = \array_slice($tagNames, 0, random_int(1, 2));

        return array_map(function ($tagName) {
            return $this->getReference('tag-' . $tagName);
        }, $selectedTags);
    }

    /*private function getRandomState(): string
    {
        $stateNames = $this->getStateData();
        shuffle($StateNames);
        return mt_rand($this->getStateData());
    }*/
}
