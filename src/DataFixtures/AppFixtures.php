<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for ($i = 1; $i <= 30; $i++) {

            $trick = new Trick();

            $trick->setTitle("Titre du trick n°$i")
                ->setSlug("titre-du-trik-$i")
                ->setCoverImage("http://placehold.it/1000x300")
                ->setContent("<p>Description du trik.. Un contenu riche</p>");
            $manager->persist($trick);
        }

        $manager->flush();
    }
}
