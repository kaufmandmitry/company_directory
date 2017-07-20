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

        return $this->renderData($count);
    }

    /**
     * List Firms
     * @Route("/firms/list/{page}/{perPage}", name="firmsList",  requirements={"page": "\d+", "perPage": "\d+"},
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
     * @Route("/firms/{id}", name="firmsView", requirements={"id": "\d+"})
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
            ->select(['f.id', 'f.name', 'f.phoneNumbers', 'b.streetName as street', 'b.buildingNumber as building'])
            ->innerJoin('f.building', 'b')
            ->where($qb->expr()->eq('f.id', $id))
            ->getQuery()
            ->getArrayResult();
        if (!$firm) {
            return $this->renderError(404, 'Not found');
        }
        return $this->renderData($firm);
    }

    /**
     * List Firms in radius
     * @Route("/firms/inRadius/{x}/{y}/{r}/{page}/{perPage}", name="firmsInRadius",
     *     requirements={"x": "\-?\d+(\.\d{0,})?", "y": "\-?\d+(\.\d{0,})?", "r": "\-?\d+(\.\d{0,})?", "page": "\d+", "perPage": "\d+"},
     *     defaults={"page": 1, "perPage": 100})
     * @Method("GET")
     *
     * @param integer $page
     * @param integer $perPage
     * @param float $x
     * @param float $y
     * @param float $r
     *
     * @return Response
     */
    public function firmsInRadiusAction($x, $y, $r, $page, $perPage)
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
     * List firms by category
     * @Route("/firms/byCategory/{id}/{page}/{perPage}", name="firmsByCategory",
     *     requirements={"id": "\d+", "page": "\d+", "perPage": "\d+"},
     *     defaults={"page": 1, "perPage": 100})
     * @Method("GET")
     *
     * @param integer $id
     * @param integer $page
     * @param integer $perPage
     *
     * @return Response
     */
    public function firmsByCategoryAction($id, $page, $perPage)
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);
        $qb = $firmRepository->createQueryBuilder('f');
        /* @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categoryIds = $categoryRepository->getChildrenCategoryIds($id);
//var_dump($categoryIds);
//die();
        $firmsList = $qb
            ->select(['f.id', 'f.name', 'f.phoneNumbers', 'c.id as categoryId', 'c.name as categoryName'])
            ->innerJoin(Category::class, 'c', 'WITH')
//            ->setParameter('id', $id)
            ->getQuery()->getSQL();
        var_dump($firmsList);die();
//            ->setFirstResult(($page - 1) * $perPage)
//            ->setMaxResults($perPage)
//            ->getArrayResult();

        return $this->renderData($firmsList);
    }

    /**
     * List firms by category
     * @Route("/firms/byName/{name}/{page}/{perPage}", name="firmsByName",
     *     requirements={"page": "\d+", "perPage": "\d+"},
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

        $name = '%' . $name . '%';
        $firmsList = $qb
            ->select(['f.id', 'f.name', 'f.phoneNumbers'])
            ->where($qb->expr()->like('f.name', ':name'))
            ->setParameter('name', $name)
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($firmsList);
    }
}
