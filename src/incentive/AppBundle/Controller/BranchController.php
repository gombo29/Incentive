<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\Branch;
use incentive\AppBundle\Entity\Plan;
use incentive\AppBundle\Entity\Role;
use incentive\AppBundle\Form\BranchType;
use incentive\AppBundle\Functions\SpecialFunction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


/**
 * Plan controller.
 *
 * @Route("/branch")
 */
class BranchController extends Controller
{
    /**
     * @Route("/{page}", name="branch_home", requirements={"page" = "\d+"}, defaults={"page" = 1})
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
                'path' => 'branch_home',
                'name' => 'Салбар нэгж',
                'isActive' => 1
            ),
        );

        $em = $this->getDoctrine()->getManager();

        $search = false;
        $searchEntity = new Branch();
        $searchForm = $this->createForm('incentive\AppBundle\Form\SearchForm\BranchSearchType', $searchEntity, array(
            'em' => $em
        ));

        if ($request->get("submit") == 'submit') {
            $searchForm->bind($request);
            $search = true;
        }

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $qb = $em->getRepository('incentiveAppBundle:Branch')->createQueryBuilder('p');


        if ($search) {
            if ($searchEntity->getName() && $searchEntity->getName() != '') {
                $qb->andWhere('p.name like :name')
                    ->setParameter('name', '%' . $searchEntity->getName() . '%');
            }
        }

        /**@var Branch $data */

        $qb->leftJoin('p.parent', 'pa')
            ->addSelect('pa')
            ->leftJoin('p.branchType', 'b')
            ->addSelect('b')
            ->orderBy('p.id', 'DESC');

        $countQueryBuilder = clone $qb;
        $count = $countQueryBuilder->select('count(p.id)')->getQuery()->getSingleScalarResult();

        $data = $qb
            ->getQuery()
            ->getArrayResult();

        $dataTree = array();

        if ($searchEntity->getName() != null) {
            foreach ($data as $entity) {
                $dataTree[] = $entity;
            }
        } else {
            foreach ($data as $entity) {
                if (!$entity['parent']) {
                    $entity['children'] = $this->makeTree($entity['id'], $data);
                    $dataTree[] = $entity;
                }
            }
        }

        return $this->render('incentiveAppBundle:Branch:index.html.twig', array(
            'title' => 'Салбар, нэгж',
            'breadcrumbs' => $breadcrumbs,
            'data' => $dataTree,
            'role' => $role,
            'pagecount' => ($count % $pagesize) > 0 ? intval($count / $pagesize) + 1 : intval($count / $pagesize),
            'count' => $count,
            'page' => $page,
            'search' => $search,
            'searchform' => $searchForm->createView(),
            'pageRoute' => 'branch_home',
            'pagesize' => $pagesize
        ));
    }

    private
    function makeTree($id, $entities)
    {
        $children = array();
        foreach ($entities as $entity) {
            if ($entity['parent'] && $entity['parent']['id'] == $id) {
                $entity['children'] = $this->makeTree($entity['id'], $entities);
                $children[] = $entity;
            }
        }
        return $children;
    }


    /**
     *
     * @Route("/new", name="branch_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public
    function newAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $branch = new Branch();
        $form = $this->createForm(new BranchType($em, null), $branch);
        $form->handleRequest($request);

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'branch_home',
                'name' => 'Салбар, нэгж',
                'isActive' => 0
            ),

            array(
                'path' => 'branch_new',
                'name' => 'Нэмэх',
                'isActive' => 1
            ),
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->getRepository('incentiveAppBundle:Branch')->createQueryBuilder('p');
            /**@var Branch $lastBranch */
            $lastBranch = $qb
                ->select('p.id')
                ->orderBy('p.id', 'desc')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            if ($lastBranch) {
                $em = $this->getDoctrine()->getManager();
                $branch->setId($lastBranch['id'] + 1);
                $branch->setParent($em->getReference('incentiveAppBundle:Branch', $branch->cattmp));
                $branch->setIsHRM(false);
                $em->persist($branch);
                $em->flush();
            }
            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Салбар, нэгж үүслээ!');
            return $this->redirectToRoute('branch_home');
        }
        return array(
            'form' => $form->createView(),
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Салбар, нэгж нэмэх',
            'role' => $role
        );
    }


    /**
     *
     * @Route("/hrm-new", name="branch_hrm_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public
    function hrmAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $form = $this->createFormBuilder()
            ->add('typeId', 'choice',
                array(
                    'label' => 'Төрөл',
                    'choices' => array(
                        '1' => 'Газар',
                        '2' => 'Алба',
                        '3' => 'Хэлтэс',
                        '7' => 'Хэсэг',

                    ),
                    'required' => true,
                    'attr' => array(
                        "class" => "form-control",
                    )
                )
            )
            ->getForm();
        $form->handleRequest($request);

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'branch_home',
                'name' => 'Салбар, нэгж',
                'isActive' => 0
            ),

            array(
                'path' => 'branch_new',
                'name' => 'Нэмэх',
                'isActive' => 1
            ),
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $typeId = $form->getData()['typeId'];

            if ($typeId == 0) {
                $url = 'http://dev.incentive/test/';
            } else {
                $url = 'http://dev.incentive/test/?level=' . $typeId;
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $values = json_decode($response);

            if ($values->code == 200) {

                try {
                    foreach ($values->data as $val) {
                        $branchVal = $em->getRepository('incentiveAppBundle:Branch')->find($val->id);
                        if ($branchVal) {
                            if ($val->parent_id) {
                                $branchParent = $em->getRepository('incentiveAppBundle:Branch')->find($val->parent_id);
                                if ($branchParent) {
                                    if ($branchParent->getIsHRM() === true) {
                                        $branchVal->setId($val->id);
                                        $branchVal->setBranchType($em->getReference('incentiveAppBundle:BranchType', $val->level));
                                        $branchVal->setName($val->name);
                                        $branchVal->setParent($branchParent);
                                    }
                                }
                            }
                        } else {

                            if ($val->parent_id) {
                                $branchParent = $em->getRepository('incentiveAppBundle:Branch')->find($val->parent_id);
                                if ($branchParent) {
                                    $branch = new Branch();
                                    $branch->setId($val->id);
                                    $branch->setBranchType($em->getReference('incentiveAppBundle:BranchType', $val->level));
                                    $branch->setName($val->name);
                                    $branch->setIsHRM(true);
                                    $branch->setParent($branchParent);
                                    $em->persist($branch);
                                }
                            }
                        }
                    }

                    $request
                        ->getSession()
                        ->getFlashBag()
                        ->add('success', 'Салбар, нэгж амжилттай татлаа!');

                    return $this->redirectToRoute('branch_hrm_new');

                } catch (\Exception $e) {
                    $request
                        ->getSession()
                        ->getFlashBag()
                        ->add('danger', 'Дээд түвшиний салбар нэгж бүртгэгдээгүй байна! Дээд түвшиний салбар нэгж оруулна уу!');

                    return $this->redirectToRoute('branch_hrm_new');
                }
            }
        }
        return array(
            'form' => $form->createView(),
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Салбар, нэгж HRM-с татах',
            'role' => $role
        );
    }


    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/update/{id}", name="branch_update")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public
    function updateAction(Request $request, Branch $branch)
    {

        $em = $this->getDoctrine()->getManager();
        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'branch_home',
                'name' => 'Салбар, нэгж',
                'isActive' => 0
            ),

            array(
                'path' => 'product_update',
                'name' => 'засах',
                'isActive' => 1
            ),
        );
        $parentId = null;
        $branch->cattmp = null;

        if ($branch->getParent()) {
            $branch->cattmp = $branch->getParent()->getId();
            $parentId = $branch->getParent()->getId();
        }

        $editForm = $this->createForm(new BranchType($em, $parentId), $branch);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($branch);

            $this->getDoctrine()->getManager()->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Салбар засагдлаа!');

            return $this->redirectToRoute('branch_home');
        }

        return array(
            'form' => $editForm->createView(),
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Салбар засах',
            'role' => $role
        );
    }


    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/delete/{id}", name="branch_delete")
     * @Method({"GET", "POST"})
     *
     */
    public
    function deleteAction(Request $request, Branch $branch)
    {
        try {

            $em = $this->getDoctrine()->getManager();
            $em->remove($branch);
            $em->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Салбар устлаа!');
        } catch (\Exception $e) {
            $request
                ->getSession()
                ->getFlashBag()
                ->add('danger', 'Энэ нэгжид доод нэгж эсвэл ажилтан бүртгэлтэй байгаа учир устгах боломжгүй байна!');

            return $this->redirectToRoute('branch_home');
        }

        return $this->redirectToRoute('branch_home');
    }
}
