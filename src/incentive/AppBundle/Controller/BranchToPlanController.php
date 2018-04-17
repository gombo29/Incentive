<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\Branch;
use incentive\AppBundle\Entity\PlanBranch;
use incentive\AppBundle\Entity\Role;
use incentive\AppBundle\Functions\SpecialFunction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


/**
 * MonthlyPlan controller.
 *
 * @Route("/branch-to-plan")
 */
class BranchToPlanController extends Controller
{
    /**
     * @Route("/{id}", name="branch_to_plan_home")
     */
    public function indexAction($id)
    {
        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'plan_home',
                'name' => 'Төлөвлөгөө',
                'isActive' => 0
            ),
            array(
                'path' => 'monthly_plan_home',
                'name' => 'Төлөвлөгөө дээр салбар нэгж нэмэх',
                'isActive' => 1
            ),

        );

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('incentiveAppBundle:Branch')->createQueryBuilder('p');
        /**@var Branch $data */
        $data = $qb
            ->leftJoin('p.parent', 'pa')
            ->addSelect('pa')
            ->orderBy('p.id', 'asc')
            ->getQuery()
            ->getArrayResult();


        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());


        $qb = $em->getRepository('incentiveAppBundle:PlanBranch')->createQueryBuilder('p');
        /**@var Branch $data */
        $selectedBranch = $qb
            ->select('b.id as branchId')
            ->leftJoin('p.branch', 'b')
            ->where('p.plan = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        foreach ($data as $key => $d) {
            $isSelecter = false;
            foreach ($selectedBranch as $sb) {
                if ($d['id'] == $sb['branchId'])
                    $isSelecter = true;
            }
            $data[$key]['selected'] = $isSelecter;
        }

        $dataTree = array();

        foreach ($data as $entity) {
            if (!$entity['parent']) {
                $this->makeTree($entity, $data);
                $dataTree[] = $entity;
            }
        }

        return $this->render('incentiveAppBundle:BranchToPlan:index.html.twig', array(
            'title' => 'Төлөвлөгөө дээр салбар нэгж нэмэх',
            'breadcrumbs' => $breadcrumbs,
            'data' => $dataTree,
            'planId' => $id,
            'role' => $role
        ));
    }


    private function makeTree(&$root, $entities)
    {
        $children = array();
        foreach ($entities as $entity) {
            if ($entity['parent'] && $entity['parent']['id'] == $root['id']) {
                $this->makeTree($entity, $entities);
                $children[] = $entity;
            }
        }
        $root['children'] = $children;
    }

    /**
     *
     * @Route("/create/{id}", name="branch_add_to_plan", requirements={"id" = "\d+"})
     * @Method({"POST"})
     *
     */
    public function createAction(Request $request, $id)
    {
        if (strtolower($request->getMethod()) === 'post')
        {

            $insertBranches = array();
            $deleteBranches = array();

            $branches = $request->request->get('questions');

            $em = $this->getDoctrine()->getManager();

            // өмнө нь хадгалагдсан байгаа салбаруудын мэдээлэл
            $qb = $em->getRepository('incentiveAppBundle:PlanBranch')->createQueryBuilder('p');
            /**@var Branch $data */
            $selectedBranches = $qb
                ->select('b.id as branchId')
                ->leftJoin('p.branch', 'b')
                ->where('p.plan = :id')
                ->setParameter('id', $id)
                ->orderBy('p.id', 'ASC')
                ->getQuery()
                ->getResult();

            // одоо сонгогдсон, өмнө дб дээр байхгүй буюу шинээр нэмэгдэх салбаруудыг ялгах
                foreach ($branches as $branch) {
                    $isExists = false;
                    foreach ($selectedBranches as $selectedBranch) {

                        if ($branch == $selectedBranch['branchId']) {
                            $isExists = true;
                        }
                    }
                    if ($isExists == false) {
                        array_push($insertBranches,  $branch);
                    }
                }


            // өмнө нь хадгалагдсан байсан, одоо сонгогдоогүй буюу устгагдах салбаруудыг ялгах
                foreach ($selectedBranches as $selectedBranch) {
                    $isExists = true;
                    foreach ($branches as $branch) {

                        if ($selectedBranch['branchId'] == $branch)
                        {
                            $isExists = false;
                        }
                    }
                    if ($isExists == true) {
                        array_push($deleteBranches, $selectedBranch['branchId']);
                    }
                }

            if(sizeof($insertBranches) > 0)
            {
                foreach ($insertBranches as $branch)
                {
                    $mplan = new PlanBranch();
                    $mplan->setBranch($em->getReference('incentiveAppBundle:Branch', $branch));
                    $mplan->setPlan($em->getReference('incentiveAppBundle:Plan', $id));
                    $em->persist($mplan);
                }
                $em->flush();
            }

            if(sizeof($deleteBranches) > 0) {
                $qb = $em->getRepository('incentiveAppBundle:PlanBranch')->createQueryBuilder('p');
                $qb
                    ->delete()
                    ->where($qb->expr()->in('p.branch', $deleteBranches))
                    ->getQuery()
                    ->getResult();
            }

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Амжилтай үүслээ!');
        }

        return $this->redirect($this->generateUrl('branch_to_plan_home', array('id' => $id)));
    }
}
