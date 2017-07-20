<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Firm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $root_categories = $em->getRepository(Category::class)->findBy(['parentCategory' => null]);

        $collection = new \Doctrine\Common\Collections\ArrayCollection($root_categories);
        $category_iterator = new \AppBundle\Entity\Helpers\RecursiveCategoryIterator($collection);
        $recursive_iterator = new \RecursiveIteratorIterator($category_iterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($recursive_iterator as $index => $child_category)
        {
            echo '<option value="' . $child_category->getId() . '">' . str_repeat('&nbsp;&nbsp;', $recursive_iterator->getDepth()) . $child_category->getName() . '</option>';
        }

        return new Response();
    }
}
