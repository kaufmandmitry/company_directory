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

        return $this->renderData($count);
    }

    /**
     * List Categories
     * @Route("/categories/list/{page}/{perPage}", name="categoriesList",  requirements={"page": "\d+", "perPage": "\d+"},
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
            ->select(['c.id', 'pc.id as parentCategoryId', 'c.name'])
            ->leftJoin('c.parentCategory', 'pc')
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($categoriesList);
    }

    /**
     * View category
     * @Route("/categories/{id}", name="categoriesView", requirements={"id": "\d+"})
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
            ->select(['c.id', 'pc.id as parentCategoryId', 'c.name', 'pc.name as parentCategoryName'])
            ->leftJoin('c.parentCategory', 'pc')
            ->where($qb->expr()->eq('c.id', $id))
            ->getQuery()
            ->getArrayResult();
        if (!$category) {
            return $this->renderError(404, 'Not found');
        }
        return $this->renderData($category);
    }

    /**
     * List of firms in the category
     * @Route("/categoryFirms/{id}/{page}/{perPage}", name="categoriesList",
     *     requirements={"id": "\d+", "page": "\d+", "perPage": "\d+"}, defaults={"page": 1, "perPage": 100})
     * @Method("GET")
     *
     * @param integer $id
     * @param integer $page
     * @param integer $perPage
     *
     * @return Response
     */
    public function categoryFirmsAction($id, $page, $perPage)
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);

        $qb = $firmRepository->createQueryBuilder('f');
        $firmsList = $firmRepository->createQueryBuilder('f')
            ->select(['f.id', 'f.name', 'f.phoneNumbers'])
            ->innerJoin(Category::class, 'c', 'WITH', 'c.id = :requestedId')
            ->setParameter('requestedId', $id)
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($firmsList);
    }

}
