<?php

namespace App\DataFixtures;

use App\Entity\Car;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cars = [
            ['BMW', 'X5'],
            ['Audi', 'A4'],
            ['Mercedes', 'C-Class'],
            ['Toyota', 'Corolla'],
        ];

        foreach ($cars as [$brand, $model]) {
            $car = new Car();
            $car->setBrand($brand);
            $car->setModel($model);

            $manager->persist($car);
        }

        $manager->flush();
    }
}
