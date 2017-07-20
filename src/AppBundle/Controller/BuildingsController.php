<?php

namespace AppBundle\Controller;
use AppBundle\Controller\AdvancedControllers\ApiController;
use AppBundle\Entity\Building;
use AppBundle\Entity\Category;
use AppBundle\Entity\Firm;
use AppBundle\Repository\BuildingRepository;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\FirmRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BuildingsController extends ApiController
{

    /**
     * Count firms
     * @Route("/buildings/count", name="buildingsCount")
     * @Method("GET")
     *
     * @return Response
     */
    public function countAction()
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Building::class);
        $count = $firmRepository->getCount();

        return $this->renderData($count);
    }

    /**
     * List Firms
     * @Route("/buildings/list/{page}/{perPage}", name="buildingsList", requirements={"page": "\d+", "perPage": "\d+"},
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
        /* @var BuildingRepository $buildingRepository */
        $buildingRepository = $this->getDoctrine()->getRepository(Building::class);
        $buildingsList = $buildingRepository->createQueryBuilder('b')
            ->select(['b.id', 'b.streetName', 'b.buildingNumber', 'b.coordinateX', 'b.coordinateY'])
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($buildingsList);
    }

    /**
     * View firm
     * @Route("/building/{id}", name="buildingsView", requirements={"id": "\d+"})
     * @Method("GET")
     *
     * @param integer $id
     *
     * @return Response
     */
    public function viewAction($id)
    {
        /* @var FirmRepository $firmRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Building::class);
        $qb = $firmRepository->createQueryBuilder('b');
        $building = $qb
            ->select(['b.id', 'b.streetName', 'b.buildingNumber', 'b.coordinateX', 'b.coordinateY'])
            ->where($qb->expr()->eq('b.id', $id))
            ->getQuery()
            ->getArrayResult();
        if (!$building) {
            return $this->renderError(404, 'Not found');
        }
        return $this->renderData($building);
    }

    /**
     * Get list of firm in the building
     * @Route("/buildingFirms/{id}/{page}/{perPage}", name="buildingsFirmsList",
     *     requirements={"id": "\d+", "page": "\d+", "perPage": "\d+"}, defaults={"page": 1, "perPage": 100})
     * @Method("GET")
     *
     * @param integer $id
     * @param integer $page
     * @param integer $perPage
     *
     * @return Response
     */
    public function buildingFirmsAction($id, $page, $perPage)
    {
        /* @var CategoryRepository $categoryRepository */
        $firmRepository = $this->getDoctrine()->getRepository(Firm::class);
        $qb = $firmRepository->createQueryBuilder('b');
        $firmsList = $firmRepository->createQueryBuilder('f')
            ->select(['f.id', 'f.name', 'f.phoneNumbers', 'b.streetName as street', 'b.buildingNumber as building'])
            ->innerJoin('f.building', 'b')
            ->where($qb->expr()->eq('b.id', $id))
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getArrayResult();

        return $this->renderData($firmsList);
    }
}