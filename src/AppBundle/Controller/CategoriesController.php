<?php

namespace AppBundle\Controller;
use AppBundle\Controller\AdvancedControllers\ApiController;
use AppBundle\Entity\Category;
use AppBundle\Entity\Firm;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\FirmRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CategoriesController extends ApiController
{
    /**
     * Count categories
     * @Route("/categories/count", name="categoriesCount")
     * @Method("GET")
     *
     * @return Response
     */
    public function countAction()
    {
        /* @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $count = $categoryRepository->getCount();

        return $this->renderData(['count' => $count]);
    }

    /**
     * List Categories
     * @Route("/categories/list/{page}/{perPage}", name="categoriesList",  requirements={"page": "\d+", "perPage": "(100)|(0*\d{1,2})"},
     *      defaults={"page": 1, "perPage": 100})
     * @Method("GET")
     *
     * @param integer $page
     * @param integer $perPage
     *
     * @return Response
     */
    public function listAction($page, $perPage)
    {
        /* @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categoriesList = $categoryRepository->createQueryBuilder('c')
            ->select(['c.id', 'c.name'])
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($categoriesList);
    }

    /**
     * View category
     * @Route("/categories/view/{id}", name="categoriesView", requirements={"id": "\d+"})
     * @Method("GET")
     *
     * @param integer $id
     *
     * @return Response
     */
    public function viewAction($id)
    {
        /* @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $qb = $categoryRepository->createQueryBuilder('c');
        $category = $qb
            ->select(['c.id', 'c.name'])
            ->where($qb->expr()->eq('c.id', $id))
            ->getQuery()
            ->getFirstResult();

        if (!$category) {
            throw $this->createNotFoundException();
        }

        $category['parentCategory'] = $qb
            ->select(['pc.id', 'pc.name'])
            ->innerJoin('c.parentCategory', 'pc')
            ->where($qb->expr()->eq('c.id', $category['id']))
            ->getQuery()
            ->getOneOrNullResult();

        $category['childCategories'] = $qb
            ->select(['cc.id', 'cc.name'])
            ->innerJoin('c.childCategories', 'cc')
            ->where($qb->expr()->eq('c.id', $category['id']))
            ->getQuery()
            ->getArrayResult();

        return $this->renderData($category);
    }
}
