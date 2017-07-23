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

class FirmsController extends ApiController
{

    /**
     * Count firms
     * @Route("/firms/count", name="firmsCount")
     * @Method("GET")
     *
     * @return Response
     */
    public function countAction()
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);
        $count = $firmRepository->getCount();

        return $this->renderData(['count' => $count]);
    }

    /**
     * List Firms
     * @Route("/firms/list/{page}/{perPage}", name="firmsList",  requirements={"page": "\d+", "perPage": "(100)|(0*\d{1,2})"},
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
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);
        $firmsList = $firmRepository->createQueryBuilder('f')
            ->select(['f.id', 'f.name', 'f.phoneNumbers', 'b.streetName as street', 'b.buildingNumber as building'])
            ->innerJoin('f.building', 'b')
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($firmsList);
    }

    /**
     * View firm
     * @Route("/firms/view/{id}", name="firmsView", requirements={"id": "\d+"})
     * @Method("GET")
     *
     * @param integer $id
     *
     * @return Response
     */
    public function viewAction($id)
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);
        $qb = $firmRepository->createQueryBuilder('f');
        $firm = $qb
            ->select([
                'f.id',
                'f.name',
                'f.phoneNumbers',
                'b.streetName as street',
                'b.buildingNumber as building'
            ])
            ->innerJoin('f.building', 'b')
            ->where($qb->expr()->eq('f.id', $id))
            ->getQuery()
            ->getOneOrNullResult();

        if (!$firm) {
            throw $this->createNotFoundException();
        }

        $firm['categories'] = $qb->select([
            'c.id as categoryId',
            'c.name as categoryName'
        ])
        ->innerJoin('f.categories', 'c')
        ->where($qb->expr()->eq('f.id', $firm['id']))
        ->getQuery()
        ->getArrayResult();

        return $this->renderData($firm);
    }

    /**
     * List firms in radius
     * @Route("/firms/byRadius/{x}/{y}/{r}/{page}/{perPage}", name="firmsByRadius",
     *     requirements={"x": "\-?\d+(\.\d{0,})?", "y": "\-?\d+(\.\d{0,})?", "r": "\-?\d+(\.\d{0,})?",
     *     "page": "\d+", "perPage": "(100)|(0*\d{1,2})"}, defaults={"page": 1, "perPage": 100})
     *
     * @Method("GET")
     * @param integer $page
     * @param integer $perPage
     * @param float $x
     * @param float $y
     * @param float $r
     *
     * @return Response
     */
    public function firmsByRadiusAction($x, $y, $r, $page, $perPage)
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);
        $qb = $firmRepository->createQueryBuilder('f');
        $firmsList = $qb
            ->select(['f.id', 'f.name', 'f.phoneNumbers', 'b.streetName as street', 'b.buildingNumber as building',
                'b.coordinateX', 'b.coordinateY'])
            ->innerJoin('f.building', 'b')
            ->where($qb->expr()->lte('((b.coordinateX - :x) * (b.coordinateX - :x)) + ((b.coordinateY - :y) * (b.coordinateY - :y))', pow($r, 2)))
            ->setParameter('x', $x)
            ->setParameter('y', $y)
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($firmsList);
    }

    /**
     * List firms by name
     * @Route("/firms/byName/{name}/{page}/{perPage}", name="firmsByName",
     *     requirements={"page": "\d+", "perPage": "(100)|(0*\d{1,2})"},
     *     defaults={"page": 1, "perPage": 100})
     * @Method("GET")
     *
     * @param string $name
     * @param integer $page
     * @param integer $perPage
     *
     * @return Response
     */
    public function firmsByNameAction($name, $page, $perPage)
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);
        $qb = $firmRepository->createQueryBuilder('f');

        $firmsList = $qb
            ->select(['f.id', 'f.name', 'f.phoneNumbers'])
            ->where($qb->expr()->like('f.name', ':name'))
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($firmsList);
    }

    /**
     * List firms by category in deep
     * @Route("/firms/byCategoryInDeep/{categoryId}/{page}/{perPage}", name="firmsByCategoryInDeep",
     *     requirements={"id": "\d+", "page": "\d+", "perPage": "(100)|(0*\d{1,2})"},
     *     defaults={"page": 1, "perPage": 100})
     * @Method("GET")
     *
     * @param integer $categoryId
     * @param integer $page
     * @param integer $perPage
     *
     * @return Response
     */
    public function firmsByCategoryInDeepAction($categoryId, $page, $perPage)
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);
        $qb = $firmRepository->createQueryBuilder('f');

        /* @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categoryIds = $categoryRepository->getIdsCategoryTree($categoryId);

        $firmsList = $qb
            ->select(['f.id', 'f.name', 'f.phoneNumbers'])
            ->innerJoin('f.categories', 'c')
            ->where($qb->expr()->in('c.id', $categoryIds))
            ->distinct()
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($firmsList);
    }

    /**
     * List of firms in the category and any subcategories of category
     * @Route("/firms/byCategory/{categoryId}/{page}/{perPage}", name="categoryFirmList",
     *     requirements={"id": "\d+", "page": "\d+", "perPage": "(100)|(0*\d{1,2})"}, defaults={"page": 1, "perPage": 100})
     * @Method("GET")
     *
     * @param integer $categoryId
     * @param integer $page
     * @param integer $perPage
     *
     * @return Response
     */
    public function firmsByCategoryAction($categoryId, $page, $perPage)
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);

        $qb = $firmRepository->createQueryBuilder('f');
        $firmsList = $qb
            ->select(['f.id', 'f.name', 'f.phoneNumbers'])
            ->innerJoin('f.categories', 'c')
            ->where($qb->expr()->eq('c.id', $categoryId))
            ->distinct()
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($firmsList);
    }

}
