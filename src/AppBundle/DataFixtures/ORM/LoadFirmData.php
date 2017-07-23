<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Building;
use AppBundle\Entity\Category;
use AppBundle\Entity\Firm;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
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
        /* @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $countFirmOnCategory = $this->container->getParameter('count_firm_on_category');

        /* @var Category[] $categories*/
        $categories = $manager->getRepository(Category::class)->findAll();
        $buildings  = $manager->getRepository(Building::class)->findAll();

        /* @var Firm[] $firms */
        $firms = [];
        $countRows = 0;

        foreach ($categories as $iCategory => $category) {
            for ($i = 0; $i < $countFirmOnCategory; $i++) {
                $firms[$countRows] = new Firm();
                $firms[$countRows]->setName($iCategory * $countFirmOnCategory + $i + 1 . ' Firm');

                // Add currentCategory
                $firms[$countRows]->addCategory($category);

                // Add 2 random category
                for ($j = 0; $j < 2; $j++) {
                    do {
                        $addingCategory = $categories[array_rand($categories)];
                    } while ($firms[$countRows]->hasCategory($addingCategory));
                    $firms[$countRows]->addCategory($addingCategory);
                }

                $countPhones = rand(1, 3);
                for ($j = 0; $j < $countPhones; $j++) {
                    $firms[$countRows]->addPhoneNumber('Phone number ' . $j);
                }

                $firms[$countRows]->setBuilding($buildings[array_rand($buildings)]);

                $em->persist($firms[$countRows]);

                $countRows++;
                if ($countRows >= 1000) {
                    $em->flush();
                    $em->clear(Firm::class);
                    $countRows = 0;
                }
            }
        }
        $em->flush();
        $em->clear();
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