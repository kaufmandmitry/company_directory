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

        /* @var Building[] $buildings */
        $buildings = [];

        $countRows = 0;

        $countBuildingOnCategory = $this->container->getParameter('count_building_on_category');

        $countCategories = $manager->getRepository(Category::class)->getCount();

        for ($i = 0; $i < $countCategories; $i++) {
            for ($j = 0; $j < $countBuildingOnCategory; $j++) {
                $buildings[$countRows] = new Building();
                $buildings[$countRows]->setStreetName('Street ' . $i); // Count of street is equal to count of category
                $buildings[$countRows]->setBuildingNumber($j);         //For 3 buildings per street
                $buildings[$countRows]->setCoordinateX(rand(-10000, 10000));
                $buildings[$countRows]->setCoordinateY(rand(-10000, 10000));
                $em->persist($buildings[$countRows]);
                $countRows++;
                if ($countRows >= 1000) {
                    $em->flush();
                    $em->clear();
                }
            }
        }
        $em->flush();
        $em->clear();
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