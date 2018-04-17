<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\Plan;
use incentive\AppBundle\Form\PlanType;
use incentive\AppBundle\Functions\SpecialFunction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


/**
 * Plan controller.
 *
 * @Route("/plan")
 */
class PlanController extends Controller
{
    /**
     * @Route("/{page}", name="plan_home", requirements={"page" = "\d+"}, defaults={"page" = 1})
     */
    public function indexAction(Request $request, $page)
    {
        $pagesize = 20;
        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'plan_home',
                'name' => 'Төлөвлөгөө',
                'isActive' => 1
            ),

        );


        $em = $this->getDoctrine()->getManager();

        $search = false;
        $searchEntity = new Plan();
        $searchForm = $this->createForm('incentive\AppBundle\Form\SearchForm\PlanSearchType', $searchEntity, array(
            'em' => $em
        ));

        if ($request->get("submit") == 'submit') {
            $searchForm->bind($request);
            $search = true;
        }

        $qb = $em->getRepository('incentiveAppBundle:Plan')->createQueryBuilder('p');


        if ($search) {
            if ($searchEntity->getUsername() && $searchEntity->getUsername() != '') {
                $qb->andWhere('p.username like :username')
                    ->setParameter('username', '%' . $searchEntity->getUsername() . '%');
            }
        }
        $countQueryBuilder = clone $qb;
        $count = $countQueryBuilder->select('count(p.id)')->getQuery()->getSingleScalarResult();
        /**@var Plan $data */
        $data = $qb
            ->orderBy('p.id', 'DESC')
            ->setFirstResult(($page - 1) * $pagesize)
            ->setMaxResults($pagesize)
            ->getQuery()
            ->getArrayResult();

        $createdPlanIds = array();

        foreach ($data as $d) {
            array_push($createdPlanIds, $d['id']);
        }
        if ($createdPlanIds) {

            $qb = $em->getRepository('incentiveAppBundle:PlanBranch')->createQueryBuilder('p');
            $selectedBranchCount = $qb
                ->select('pl.id, count(pl.id) as cnt')
                ->leftJoin('p.plan', 'pl')
                ->where($qb->expr()->in('p.plan', $createdPlanIds))
                ->groupBy('pl.id')
                ->getQuery()
                ->getResult();


            foreach ($data as $key => $d) {
                $cnt = 0;
                foreach ($selectedBranchCount as $selCount) {
                    if ($selCount['id'] == $d['id']) {
                        $cnt = $selCount['cnt'];
                    }
                }

                $data[$key]['cntBranch'] = $cnt;
            }

            $qb = $em->getRepository('incentiveAppBundle:PlanProduct')->createQueryBuilder('p');
            $selectedProductCount = $qb
                ->select('pl.id, count(pl.id) as cnt')
                ->leftJoin('p.plan', 'pl')
                ->where($qb->expr()->in('p.plan', $createdPlanIds))
                ->groupBy('pl.id')
                ->getQuery()
                ->getResult();


            foreach ($data as $key => $d) {
                $cnt = 0;
                foreach ($selectedProductCount as $selCount) {
                    if ($selCount['id'] == $d['id']) {
                        $cnt = $selCount['cnt'];
                    }
                }

                $data[$key]['cntProduct'] = $cnt;
            }

            $em = $this->getDoctrine()->getManager();
            $sp = new SpecialFunction($this->container);
            $role = $sp->getRoles($this->getUser());
        } else {
            $role = null;
        }

        return $this->render('incentiveAppBundle:Plan:index.html.twig', array(
            'title' => 'Төлөвлөгөө',
            'breadcrumbs' => $breadcrumbs,
            'data' => $data,
            'role' => $role
        ));
    }


    /**
     * Creates a new banner entity.
     *
     * @Route("/new", name="plan_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $banner = new Plan();
        $form = $this->createForm('incentive\AppBundle\Form\PlanType', $banner);
        $form->handleRequest($request);

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
                'path' => 'plan_new',
                'name' => 'Нэмэх',
                'isActive' => 1
            ),

        );

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($banner);
            $em->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Төлөвлөгөө үүслээ!');

            return $this->redirectToRoute('plan_home');
        }

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        return array(
            'form' => $form->createView(),
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Төлөвлөгөө нэмэх',
            'role' => $role
        );
    }


    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/update/{id}", name="plan_update")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function updateAction(Request $request, Plan $plan)
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
                'path' => 'product_update',
                'name' => 'засах',
                'isActive' => 1
            ),
        );

        $editForm = $this->createForm(new PlanType(), $plan);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($plan);

            $this->getDoctrine()->getManager()->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Төлөвлөгөө засагдлаа!');

            return $this->redirectToRoute('plan_home');
        }


        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        return array(
            'form' => $editForm->createView(),
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Төлөвлөгөө засах',
            'role' => $role
        );
    }


    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/delete/{id}", name="plan_delete")
     * @Method({"GET", "POST"})
     *
     */
    public function deleteAction(Request $request, Plan $plan)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($plan);
            $em->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Төлөвлөгөө устлаа!');

        } catch (\Exception $e) {
            $request
                ->getSession()
                ->getFlashBag()
                ->add('danger', 'Салбар, нэгж болон бүтээгдэхүүн холбогдсон байгаа учир устгах боломжгүй!');

            return $this->redirectToRoute('plan_home');
        }
        return $this->redirectToRoute('plan_home');
    }
}
