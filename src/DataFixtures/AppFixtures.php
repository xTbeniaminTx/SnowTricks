<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for ($i = 1; $i <= 30; $i++) {

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
                ->setAuthor($faker->name)
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
