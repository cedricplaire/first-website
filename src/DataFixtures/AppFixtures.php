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

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
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
        $auser->setCity('Saintes');
        $auser->setRoles(['ROLE_ADMIN']);

        $manager->persist($auser);

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
            $fakeUser->setFullName($first . ' ' . $last);
            $fakeUser->setBirthday($faker->dateTimeThisCentury);
            $fakeUser->setUseGravatar(false);
            $fakeUser->setAge(mt_rand(18, 65));
            $fakeUser->setCity($faker->city);
            //$fakeUser->setState($faker->state);
            $fakeUser->setRoles(['ROLE_USER']);
            $fakeUser->setAvatarPerso($picture);
            $fakeUser->createGravatarUrl();

            $manager->persist($fakeUser);
            $users[] = $fakeUser;
        }

        /* Tag pour les articles */
        for ($j = 0; $j < 20; $j++) {
            $tag = new Tag();
            $tag->setName($faker->word . $j);

            $manager->persist($tag);
            $this->addReference('tag-' . $j, $tag);
        };

        /* articles factices du site */
        for ($k = 0; $k < 30; $k++) {
            $post = new Post();

            $title      = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content    = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users) - 1)];

            $post->setTitle($title)
                ->setSummary($introduction)
                ->setContent($content)
                ->setAuthor($user);

            $post->initializeSlug();

            for ($t = 0; $t < (mt_rand(1, 4)); $t++) {
                $post->addTag($this->getReference('tag-' . mt_rand(0, 19)));
            }

            for ($l = 0; $l < 5; $l++) {
                $comment = new Comment();
                $comment->setAuthor($user);
                $comment->setContent($faker->sentences(3, true));
                $comment->setPost($post);
                $comment->setPublishedAt($faker->dateTimeThisYear);

                $manager->persist($comment);
            }
            $manager->persist($post);
        }

        $manager->flush();
    }
}
