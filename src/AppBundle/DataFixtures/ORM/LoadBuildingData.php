<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Building;
use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadBuildingData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        /* @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $countBuildingOnCategory = $this->container->getParameter('count_building_on_category');

        $countCategories = $manager->getRepository(Category::class)->getCount();
        $em->getConnection()->beginTransaction();

        for ($i = 0; $i < $countCategories; $i++) {
            for ($j = 0; $j < $countBuildingOnCategory; $j++) {
                $building = new Building();
                $building->setStreetName('Street ' . $i); // Count of street is equal to count of category
                $building->setBuildingNumber($j);         //For 3 buildings per street
                $building->setCoordinateX(rand(-10000, 10000));
                $building->setCoordinateY(rand(-10000, 10000));
                $em->persist($building);
                $em->flush();

                if ($i * $countBuildingOnCategory + $j > 100) {
                    $em->getConnection()->commit();
                    $em->getConnection()->beginTransaction();
                }
            }
        }
        $em->getConnection()->commit();
    }

    public function getOrder()
    {
        return 2;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}