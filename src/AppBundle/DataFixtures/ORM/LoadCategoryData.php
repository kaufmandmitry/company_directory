<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        $depthTree = $this->container->getParameter('depth_category_tree');
        $widthTree = $this->container->getParameter('width_category_tree');
        for ($i = 0; $i < $widthTree; $i++) {
            $parentCategory = new Category();
            $parentCategory->setName('Root '. $i);

            $manager->persist($parentCategory);
            $manager->flush();
            self::recursiveCategoreTreeFill($parentCategory, 0, $depthTree, $widthTree, $manager);
        }
    }

    /* @param ObjectManager $manager
     * @param Category $parentCategory
     */
    static private function recursiveCategoreTreeFill($parentCategory, $currentDepth, $maxDepth, $widthTree, $manager) {
        if ($currentDepth == $maxDepth) return;
        for ($i = 0; $i < $widthTree; $i++) {
            $category = new Category();
            $category->setName('Category ' . $parentCategory->getId() . ' ' . $i . ' ' . $currentDepth);
            $category->setParentCategory($parentCategory);
            $manager->persist($category);
            $manager->flush();
            LoadCategoryData::recursiveCategoreTreeFill($category, $currentDepth + 1, $maxDepth, $widthTree, $manager);
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