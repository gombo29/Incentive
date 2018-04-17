<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\testBranch;
use incentive\AppBundle\Entity\testUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Text controller.
 *
 * @Route("/test")
 * @Method({"GET"})
 *
 */
class TestController extends Controller
{
    /**
     * @Route("/", name="test_home")
     */
    public function indexAction(Request $request)
    {
        $level = $request->get('level');
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('incentiveAppBundle:testBranch')->createQueryBuilder('p');
        /**@var testBranch $data */
        $qb
            ->select('p.id', 'p.name', 'p.level', 'pa.id as parent_id')
            ->leftJoin('p.parent', 'pa');
        if ($level) {
            $qb
                ->where('p.level = :level')
                ->setParameter('level', $level);
        }

        $data = $qb
            ->getQuery()
            ->getArrayResult();

        return new JsonResponse(array(
            'code' => 200,
            'data' => $data
        ));
    }

    /**
     * @Route("/user", name="test_user")
     */
    public function userAction(Request $request)
    {

        $sectionId = $request->get('sectionId');

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('incentiveAppBundle:testUser')->createQueryBuilder('p');
        /**@var testUser $data */

        if ($sectionId) {
            $qb->where('p.sectionId = :sectionId')
                ->setParameter('sectionId', $sectionId);
        }

        $data = $qb
            ->getQuery()
            ->getArrayResult();

        return new JsonResponse(array(
            'code' => 200,
            'data' => $data
        ));
    }
}
