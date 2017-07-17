<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoryData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $parentCategory = new Category();
        $parentCategory->setName('Category 0');
        $manager->persist($parentCategory);
        $manager->flush();
        self::recursiveCategoreTreeFill(0, 3, $parentCategory, $manager);
    }

    static private function recursiveCategoreTreeFill($currentDepth, $maxDepth, $parentCategory, $manager) {
        if ($currentDepth == $maxDepth) return;
        $category = new Category();
        $category->setName('Root category');
        $manager->persist($category);
        $manager->flush();
        for ($i = 0; $i < 3; $i++) {
            $category = new Category();
            $category->setName('Category ' . $parentCategory->getId() . ' ' . $i . ' ' . $currentDepth);
            $category->setParentCategory($parentCategory);
            $manager->persist($category);
            $manager->flush();
            $category->setName($category->getId() . '. ' . $category->getName());
            $manager->persist($category);
            $manager->flush();
            LoadCategoryData::recursiveCategoreTreeFill($currentDepth + 1, $maxDepth, $category, $manager);
        }
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 1;
    }
}