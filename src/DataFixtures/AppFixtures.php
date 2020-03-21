<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for ($i = 1; $i <= 30; $i++) {

            $trick = new Trick();

            $title = $faker->sentence();

            $coverImage = $faker->imageUrl(1000, 300);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(6)) . '</p>';

            $trick->setTitle($title)
                ->setCoverImage($coverImage)
                ->setContent($content);
            $manager->persist($trick);
        }

        $manager->flush();
    }
}
