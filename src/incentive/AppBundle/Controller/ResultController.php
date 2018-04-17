<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\Branch;
use incentive\AppBundle\Entity\BranchDraft;
use incentive\AppBundle\Entity\MonthlyPlan;
use incentive\AppBundle\Entity\MonthlyPlanStaff;
use incentive\AppBundle\Entity\PlanProductUser;
use incentive\AppBundle\Entity\Product;
use incentive\AppBundle\Entity\ProductDraft;
use incentive\AppBundle\Entity\Role;
use incentive\AppBundle\Form\BranchDataType;
use incentive\AppBundle\Functions\SpecialFunction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * Result controller.
 *
 * @Route("/result")
 */
class ResultController extends Controller
{

    var $branches = array();
    var $branchArr = array();

    /**
     * @Route("/{id}/{page}", name="result", requirements={"page" = "\d+","id" = "\d+"}, defaults={"page" = 1})
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
                'name' => 'Гүйцэт',
                'isActive' => 0
            ),
            array(
                'path' => 'monthly_plan_home',
                'name' => 'Төлөвлөгөө оруулах',
                'isActive' => 1
            ),

        );

        $isAdmin = false;

        if (in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles())) {
            $isAdmin = true;
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('incentiveAppBundle:CmsUser')->createQueryBuilder('p');
        $branchId = $qb
            ->select('b.id')
            ->leftJoin('p.branch', 'b')
            ->where('p.id = :userid')
            ->setParameter("userid", $this->getUser()->getId())
            ->getQuery()
            ->getArrayResult();

        // Бүх ажилтнуудыг авч байна
        $qb = $em->getRepository('incentiveAppBundle:CmsUser')->createQueryBuilder('p');
        $allStaffs = $qb
            ->select('p.id as staffId,  p.lastname, p.firstname, b.id as branchId, b.name as branchName')
            ->leftJoin('p.branch', 'b')
            ->getQuery()
            ->getArrayResult();


        $qb = $em->getRepository('incentiveAppBundle:Branch')->createQueryBuilder('p');
        /**@var Branch $data */
        $data = $qb->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getArrayResult();


        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        // сонгогдсон бүтээгдэхүүнийг авч байна
        $qb = $em->getRepository('incentiveAppBundle:PlanProduct')->createQueryBuilder('p');
        /**@var Product $data */
        $selectedProducts = $qb
            ->select('b.name as name', 'b.id as productId')
            ->leftJoin('p.product', 'b')
            ->where('p.plan = :id')
            ->setParameter('id', $id)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();

        $qb = $em->getRepository('incentiveAppBundle:PlanBranch')->createQueryBuilder('p');

        // сонгогдсон салбаруудыг авч байна
        $qb
            ->select('p.id', 'b.name as name', 'b.id as branchId', 'bt.id as branchTypeId', 'pa.id as parentId')
            ->leftJoin('p.branch', 'b')
            ->leftJoin('b.parent', 'pa')
            ->leftJoin('b.branchType', 'bt')
            ->where('p.plan = :id')
            ->setParameter('id', $id);

        $selectedBranches =
            $qb->orderBy('b.id', 'desc')
                ->getQuery()
                ->getResult();


        $branchDataTree = array();
        $branchList = array();


        // сонгогдсон салбаруудыг мод бүтэцтэй болгож байна
        foreach ($selectedBranches as $entity) {
            if (!$entity['parentId']) {
                $this->makeTree($entity, $selectedBranches);
                $branchDataTree[] = $entity;
                array_push($branchList, $entity);
            }
        }

        if (!$isAdmin) {
            $this->getBranchById($branchDataTree, $branchId[0]['id']);
            $this->getListBranchFromTree(array($this->branchArr));

            $accountSelectedBranches = array($this->branchArr);
        } else {
            $this->getListBranchFromTree($branchDataTree);

            $accountSelectedBranches = $branchDataTree;
        }


        $qb = $em->getRepository('incentiveAppBundle:MonthlyPlan')->createQueryBuilder('p');
        $mplan = $qb
            ->select('p.id as branchDataId', 'b.id as branchId', 'p.serviceValue', 'pr.id as productId')
            ->leftJoin('p.planBranch', 'bp')
            ->leftJoin('bp.branch', 'b')
            ->leftJoin('b.parent', 'pa')
            ->leftJoin('p.planProduct', 'pb')
            ->leftJoin('pb.product', 'pr')
            ->where('p.plan = :id')
            ->setParameter('id', $id)
            ->orderBy('branchId', 'asc')
            ->getQuery()
            ->getResult();

        $joinedBraches = array();

        foreach ($mplan as $plan) {
            if (array_key_exists($plan['branchId'], $joinedBraches)) {
                $joinedBraches[$plan['branchId']]['products'][] = array(
                    'productId' => $plan['productId'],
                    'branchDataId' => $plan['branchDataId'],
                    'value' => $plan['serviceValue']
                );
            } else {
                $joinedBraches[$plan['branchId']] = array(
                    'products' => array(
                        array(
                            'productId' => $plan['productId'],
                            'value' => $plan['serviceValue'],
                            'branchDataId' => $plan['branchDataId']
                        )
                    )
                );
            }
        }

        $allLastBranches = array();

        foreach ($allStaffs as $allStaff) {
            if ($allStaff['branchId']) {
                $allLastBranches += array($allStaff['branchId'] => array('branchId' => $allStaff['branchId'], 'branchName' => $allStaff['branchName']));
            }
        }
        ksort($allLastBranches);


        $qb = $em->getRepository('incentiveAppBundle:JobTitle')->createQueryBuilder('p');
        $jobtitle = $qb
            ->getQuery()
            ->getArrayResult();

        return $this->render('incentiveAppBundle:MonthlyPlan:index.html.twig', array(
            'title' => 'Төлөвлөгөө оруулах',
            'breadcrumbs' => $breadcrumbs,
            'role' => $role,
            'data' => $data,
            'selectedProducts' => $selectedProducts,
            'selectedBranches' => $accountSelectedBranches,
            'mplan' => $joinedBraches,
            'id' => $id,
            'allStaffs' => $allStaffs,
            'allLastBranches' => $allLastBranches,
            'jobtitle' => $jobtitle

        ));
    }
}
