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
            ->setCountry('France')
            ->setCity('Saintes')
            ->setCountry('France')
            ->setState('Nouvelle Aquitaine')
            ->setZipCode((int) '17100')
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

        /* Tag pour les articles */
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

        /* articles factices du site */
        for ($k = 0; $k < 30; $k++) {
            $post = new Post();

            $title      = $faker->sentence();
            $color1 = $faker->hexColor;
            $color2 = $faker->hexColor;
            $c1 = substr($color1, 1);
            $c2 = substr($color2, 1);
            $postImg = "https://via.placeholder.com/800x300/" . $c1 . "/" . $c2 . "?text=" . $faker->city . "-" . $faker->country;
            //$coverImage = $faker->imageUrl(800, 300);
            $introduction = $faker->paragraph(2);
            $content    = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

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

    public function getState()
    {
        return [
            'Yvelines',
            'Yonne',
            'Vosges',
            'Vienne',
            'Vendée',
            'Vaucluse',
            'Var',
            'Val-de-Marne',
            'Val-d\'oise',
            'Territoire de Belfort',
            'Tarn-et-Garonne',
            'Tarn',
            'Somme',
            'Seine-Saint-Denis',
            'Seine-Maritime',
            'Seine-et-Marne',
            'Saône-et-Loire',
            'Savoie',
            'Sarthe',
            'Réunion',
            'Rhône',
            'Pyrénées-Orientales',
            'Pyrénées-Atlantiques',
            'Puy-de-Dôme',
            'Pas-de-Calais',
            'Paris',
            'Orne',
            'Oise',
            'Nord',
            'Nièvre',
            'Moselle',
            'Morbihan',
            'Meuse',
            'Meurthe-et-Moselle',
            'Mayotte',
            'Mayenne',
            'Martinique',
            'Marne',
            'Manche',
            'Maine-et-Loire',
            'Lozère',
            'Lot-et-Garonne',
            'Lot',
            'Loiret', 'LOIRET',
            'Loire-Atlantique',
            'Loire', 'LOIRE',
            'Loir-et-Cher',
            'Landes',
            'Jura',
            'Isère',
            'Indre-et-Loire',
            'Indre',
            'Ile-et-Vilaine',
            'Hérault',
            'Hauts-de-Seine',
            'Hautes-Pyrénées',
            'Hautes-Alpes',
            'Haute-Vienne',
            'Haute-Saône',
            'Haute-Savoie',
            'Haute-Marne',
            'Haute-Loire',
            'Haute-Garonne',
            'Haute-corse',
            'Haut-Rhin',
            'Guyane',
            'Guadeloupe',
            'Gironde',
            'Gers',
            'Gard',
            'Finistère',
            'Eure-et-Loir',
            'Eure', 'EURE',
            'Essonne',
            'Drôme',
            'Doubs',
            'Dordogne',
            'Deux-Sèvres',
            'Côtes-d\'armor',
            'Côte-d\'or',
            'Creuse',
            'Corse-du-sud',
            'Corrèze',
            'Cher',
            'Charente-Maritime',
            'Charente',
            'Cantal',
            'Calvados',
            'Bouches-du-Rhône',
            'Bas-Rhin',
            'Aveyron',
            'Aude',
            'Aube',
            'Ariège',
            'Ardèche',
            'Ardennes',
            'Alpes-Maritimes',
            'Alpes-de-Haute-Provence',
            'Allier',
            'Aisne',

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
