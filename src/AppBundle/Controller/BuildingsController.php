<?php

namespace AppBundle\Controller;
use AppBundle\Controller\AdvancedControllers\ApiController;
use AppBundle\Entity\Building;
use AppBundle\Entity\Category;
use AppBundle\Entity\Firm;
use AppBundle\Repository\BuildingRepository;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\FirmRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BuildingsController extends ApiController
{

    /**
     * Count building
     * @Route("/buildings/count", name="buildingsCount")
     * @Method("GET")
     *
     * @return Response
     */
    public function countAction()
    {
        /* @var BuildingRepository $buildingRepository */
        $buildingRepository = $this->getDoctrine()->getRepository(Building::class);
        $count = $buildingRepository->getCount();

        return $this->renderData(['count' => $count]);
    }

    /**
     * List buildings
     * @Route("/buildings/list/{page}/{perPage}", name="buildingsList", requirements={"page": "\d+", "perPage": "(100)|(0*\d{1,2})"},
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
     * View building
     * @Route("/buildings/view/{id}", name="buildingsView", requirements={"id": "\d+"})
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
            ->getFirstResult();
        if (!$building) {
            throw $this->createNotFoundException();
        }
        return $this->renderData($building);
    }
}
