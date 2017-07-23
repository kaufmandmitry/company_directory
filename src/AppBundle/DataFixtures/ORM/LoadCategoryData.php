<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /* @param ObjectManager $manager */
    public function load(ObjectManager $manager)
    {
        /* @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        /* @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);

        $depthTree = $this->container->getParameter('depth_category_tree');
        $widthTree = $this->container->getParameter('width_category_tree');

        /* @var Category[] */
        $categories = [];
        $parentCategories[] = null;

        for ($level = 1; $level <= $depthTree; $level++) { // For level of a tree

            for ($i = 0; $i < pow($widthTree, $level); $i++) {
                $categories[$i] = new Category();
                $categories[$i]->setName('Category ' . $level . ' ' . $i);
                $categories[$i]->setParentCategory($parentCategories[($i - $i % $widthTree) / $widthTree]);
                $em->persist($categories[$i]);
            }
            $em->flush();
            $parentCategories = $categories;
            $categories = [];
        }
    }

    public function getOrder()
    {
        return 1;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}