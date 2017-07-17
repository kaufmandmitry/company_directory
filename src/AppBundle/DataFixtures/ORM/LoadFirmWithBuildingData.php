<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Building;
use AppBundle\Entity\Category;
use AppBundle\Entity\Firm;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFirmWithBuildingData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categories = $manager->getRepository(Category::class)->findAll();
        foreach ($categories as $i => $category) {
            $building = new Building();
            $building->setStreetName('Street ' . (($i % 10) + 1)); // For 10 building per street
            $building->setBuildingNumber($i);
            $building->setCoordinateX(rand(-1000, 1000));
            $building->setCoordinateY(rand(-1000, 1000));

            $manager->persist($building);
            $manager->flush();

            $firm = new Firm();
            $firm->setName('Firm ' . $i);
            $firm->setBuilding($building);

            $firm->addCategory($category);
            $firm->addCategory($categories[rand(0, count($categories))]);
            $firm->addCategory($categories[rand(0, count($categories))]);

            $countPhones = rand(1, 5);
            for ($j = 0; $j < $countPhones; $j++) {
                $firm->addPhoneNumber('Phone number ' . $j);
            }
//
            $manager->persist($firm);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 2;
    }
}