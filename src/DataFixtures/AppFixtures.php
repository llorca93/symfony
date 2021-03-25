<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\Maison;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        // $maison = new Maison();
        // $maison->setTitle('Jolie maison de campagne');
        // $maison->setDescription('Maison de campagne en bordure de rivière avec domaine de 2 hectares');
        // $maison->setSurface(185);
        // $maison->setRoom(12);
        // $maison->setBedrooms(6);
        // $maison->setPrice(500000);
        // $maison->setImg1('maison1-1.png');

        // $manager->persist($maison);

        $faker = Faker\Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $maison = new Maison();
            $maison->setTitle('Maison de ' . $faker->name());
            $maison->setDescription($faker->text(255));
            $maison->setSurface($faker->numberBetween(59, 199));
            $maison->setRoom($faker->numberBetween(5,15));
            $maison->setBedrooms($faker->numberBetween(1,4));
            $maison->setPrice($faker->numberBetween(7500,500000));
            $maison->setImg1('maison1-1.jpg');
            $manager->persist($maison);
        }

        $manager->flush();
    }
}
