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

}
