<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Building;
use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadBuildingData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $countCategories = count($manager->getRepository(Category::class)->findAll());
        for ($i = 0; $i < $countCategories; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $building = new Building();
                $building->setStreetName('Street ' . $i); // Count of street is equal to count of category
                $building->setBuildingNumber($j);         //For 3 buildings per street
                $building->setCoordinateX(rand(-1000, 1000));
                $building->setCoordinateY(rand(-1000, 1000));

                $manager->persist($building);
                $manager->flush();
            }
        }
    }

    public function getOrder()
    {
        return 2;
    }
}