<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie1 = new Movie();
        $movie1->setTitle('The Dark Knight');
        $movie1->setReleaseYear(2008);
        $movie1->setDescription('This is the description of The Dark Knight');
        $movie1->setImagePath('https://www.prime1studio.com/media/catalog/product/cache/1/image/1400x1400/
        17f82f742ffe127f42dca9de82fb58b1/h/d/hdmmdc-02_a19.jpg');

        $movie1->addActor($this->getReference('actor_1'));
        $movie1->addActor($this->getReference('actor_2'));

        $manager->persist($movie1);

        $movie2 = new Movie();
        $movie2->setTitle('Avengers: Endgame');
        $movie2->setReleaseYear(2019);
        $movie2->setDescription('This is the description of Avengers: Endgame');
        $movie2->setImagePath('https://m.media-amazon.com/images/M/
        MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_.jpg');

        $movie2->addActor($this->getReference('actor_3'));
        $movie2->addActor($this->getReference('actor_4'));

        $manager->persist($movie2);

        $manager->flush();


    }
}
