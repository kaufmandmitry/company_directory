<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Building;
use AppBundle\Entity\Category;
use AppBundle\Entity\Firm;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFirmData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /* @var Category[] $categories*/
        $categories = $manager->getRepository(Category::class)->findAll();
        $countCategories = count($categories);
        $buildings  = $manager->getRepository(Building::class)->findAll();
        $countBuildings = count($buildings);

        foreach ($categories as $i => $category) {
            
            $firm = new Firm();
            $firm->setName($i . ' Firm' . ' ' . $category->getId());

            // Add currentCategory
            $firm->addCategory($category);

            // Add 2 random category
            for ($j = 0; $j < 2; $j++) {
                do {
                    $addingCategory = $categories[array_rand($categories)];
                } while($firm->hasCategory($addingCategory));
                $firm->addCategory($addingCategory);
            }

            $countPhones = rand(1, 5);
            for ($j = 0; $j < $countPhones; $j++) {
                $firm->addPhoneNumber('Phone number ' . $j);
            }

            $firm->setBuilding($buildings[array_rand($buildings)]);
            $manager->persist($firm);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 3;
    }
}