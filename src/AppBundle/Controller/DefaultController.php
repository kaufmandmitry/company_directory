<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Firm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var Category[] $res */
        $category = $em->getRepository(Category::class)->findOneBy(['id' => 1]);

        $res = $category->getFirms();

        foreach ($res as $firm) {
            $categories = $firm->getCategories();
            foreach ($categories as $category) {
                var_dump($category->getFirms()[0]->getName());
            }
        }

        $root_categories = $em->getRepository(Category::class)->findBy(['parentCategory' => null]);

        $collection = new \Doctrine\Common\Collections\ArrayCollection($root_categories);
        $category_iterator = new \AppBundle\Entity\Helpers\RecursiveCategoryIterator($collection);
        $recursive_iterator = new \RecursiveIteratorIterator($category_iterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($recursive_iterator as $index => $child_category)
        {
            echo '<option value="' . $child_category->getId() . '">' . str_repeat('&nbsp;&nbsp;', $recursive_iterator->getDepth()) . $child_category->getName() . '</option>';
        }
        die();
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}
