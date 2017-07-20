<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Building;
use AppBundle\Entity\Category;
use AppBundle\Entity\Firm;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFirmData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        $countFirmOnCategory = $this->container->getParameter('count_firm_on_category');

        /* @var Category[] $categories*/
        $categories = $manager->getRepository(Category::class)->findAll();
        $buildings  = $manager->getRepository(Building::class)->findAll();

        foreach ($categories as $category) {
            for ($i = 0; $i < $countFirmOnCategory; $i++) {
                $firm = new Firm();
                $firm->setName($i . ' Firm' . ' ' . $category->getId());

                // Add currentCategory
                $firm->addCategory($category);

                // Add 2 random category
                for ($j = 0; $j < 2; $j++) {
                    do {
                        $addingCategory = $categories[array_rand($categories)];
                    } while ($firm->hasCategory($addingCategory));
                    $firm->addCategory($addingCategory);
                }

                $countPhones = rand(1, 3);
                for ($j = 0; $j < $countPhones; $j++) {
                    $firm->addPhoneNumber('Phone number ' . $j);
                }

                $firm->setBuilding($buildings[array_rand($buildings)]);
                $manager->persist($firm);
                $manager->flush();
            }
        }
    }

    public function getOrder()
    {
        return 3;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}