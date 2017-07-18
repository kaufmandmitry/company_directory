<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $parentCategory = new Category();
        $parentCategory->setName('Root');
        $manager->persist($parentCategory);
        $manager->flush();
        $parentCategory->setName($parentCategory->getId() . '. ' . $parentCategory->getName());
        $manager->persist($parentCategory);
        $manager->flush();
        self::recursiveCategoreTreeFill(0, 5, $parentCategory, $manager);
    }

    /* @param ObjectManager $manager
     * @param Category $parentCategory
     */
    static private function recursiveCategoreTreeFill($currentDepth, $maxDepth, $parentCategory, $manager) {
        if ($currentDepth == $maxDepth) return;
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
        return 1;
    }
}