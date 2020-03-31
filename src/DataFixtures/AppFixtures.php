<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        $users = [];
        $genres = ['male', 'female'];

        for ($i = 1; $i <= 10; $i++) {

            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 90) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $password = $this->encoder->encodePassword($user, 'password');

            $user->setEmail($faker->email)
                ->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setPicture($picture)
                ->setPassword($password);
            $manager->persist($user);
            $users[] = $user;

            $trick = new Trick();

            $category = new  Category();

            $category->setName($faker->city);

            $manager->persist($category);

            $comment = new Comment();

            $comment->setAuthor($faker->name)
                ->setContent($faker->sentence)
                ->setStatus('valid')
                ->setDate($faker->dateTime);

            $manager->persist($comment);

            $title = $faker->sentence();

            $content = '<p>' . join('</p><p>', $faker->paragraphs(6)) . '</p>';

            $trick->setTitle($title)
                ->setAuthor($user)
                ->setCreatedAt($faker->dateTime)
                ->setModifiedAt($faker->dateTime)
                ->setCategory($category)
                ->setContent($content);

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();

                $image->setFilename($faker->imageUrl())
                    ->setCaption($faker->sentence)
                    ->setTrick($trick);

                $manager->persist($image);
            }

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
