<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\Branch;
use incentive\AppBundle\Entity\Plan;
use incentive\AppBundle\Entity\Role;
use incentive\AppBundle\Functions\SpecialFunction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home_page")
     */
    public function indexAction()
    {

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'home_page',
                'name' => 'Incentive',
                'isActive' => 1
            ),

        );

        $em = $this->getDoctrine()->getManager();
        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $qb = $em->getRepository('incentiveAppBundle:Plan')->createQueryBuilder('p');
        /**@var Branch $data */
        $selectedPlan = $qb->orderBy('p.id', 'DESC')
            ->select('p.id', 'p.name')
            ->where('p.endDate >= :now')
            ->andWhere('p.startDate <= :now')
            ->setParameter('now', new \DateTime('now'))
            ->getQuery()
            ->getArrayResult();

        $selectedBranches = array();
        if ($selectedPlan) {
            $qb = $em->getRepository('incentiveAppBundle:PlanBranch')->createQueryBuilder('p');
            $selectedBranches = $qb
                ->leftJoin('p.branch', 'b')
                ->addSelect('b')
                ->where('p.plan = :planId')
                ->setParameter('planId', $selectedPlan[0]['id'])
                ->orderBy('p.id', 'ASC')
                ->getQuery()
                ->getArrayResult();

            $qb = $em->getRepository('incentiveAppBundle:MonthlyPlan')->createQueryBuilder('p');
            $monthlyPlans = $qb
                ->select('b.id as branchId, p.serviceValue as val')
                ->leftJoin('p.planBranch', 'pb')
                ->leftJoin('pb.branch', 'b')
                ->where('p.plan = :planId')
                ->setParameter('planId', $selectedPlan[0]['id'])
                ->orderBy('p.id', 'ASC')
                ->getQuery()
                ->getArrayResult();

            foreach ($selectedBranches as $key => $selectedBranch) {
                $isThere = false;
                foreach ($monthlyPlans as $monthlyPlan) {
                    if ($selectedBranch['branch']['id'] == $monthlyPlan['branchId']) {
                        if ($monthlyPlan['val'] > 0) {
                            $isThere = true;
                        }
                    }
                }
                $selectedBranches[$key]['isThere'] = $isThere;
            }
        }

        return $this->render('incentiveAppBundle:Default:index.html.twig', array(
            'title' => 'Удирдлага хэсэг',
            'breadcrumbs' => $breadcrumbs,
            'role' => $role,
            'selectedBranches' => $selectedBranches,
            'plandata' => $selectedPlan

        ));
    }

    // Мод болгож буй функц
    private function makeTree(&$root, $entities)
    {
        $children = array();
        foreach ($entities as $entity) {
            if ($entity['parentId'] == $root['branchId']) {
                $this->makeTree($entity, $entities);
                $children[] = $entity;
            }
        }
        $root['children'] = $children;
    }
}
