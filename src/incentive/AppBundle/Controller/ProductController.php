<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\Product;
use incentive\AppBundle\Form\ProductType;
use incentive\AppBundle\Functions\SpecialFunction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


/**
 * Plan controller.
 *
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/{page}", name="product_home", requirements={"page" = "\d+"}, defaults={"page" = 1})
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
                'path' => 'product_home',
                'name' => 'Бүтээгдэхүүн',
                'isActive' => 1
            ),

        );

        $em = $this->getDoctrine()->getManager();

        $search = false;
        $searchEntity = new Product();
        $searchForm = $this->createForm('incentive\AppBundle\Form\SearchForm\ProductSearchType', $searchEntity, array(
            'em' => $em
        ));

        if ($request->get("submit") == 'submit') {
            $searchForm->bind($request);
            $search = true;
        }

        $qb = $em->getRepository('incentiveAppBundle:Product')->createQueryBuilder('p');


        if ($search) {
            if ($searchEntity->getName() && $searchEntity->getName() != '') {
                $qb->andWhere('p.name like :name')
                    ->setParameter('name', '%' . $searchEntity->getName() . '%');
            }
        }
        $countQueryBuilder = clone $qb;
        $count = $countQueryBuilder->select('count(p.id)')->getQuery()->getSingleScalarResult();
        /**@var Product $data */

        $data = $qb
            ->orderBy('p.id', 'DESC')
            ->setFirstResult(($page - 1) * $pagesize)
            ->setMaxResults($pagesize)
            ->getQuery()
            ->getArrayResult();

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        return $this->render('incentiveAppBundle:Product:index.html.twig', array(
            'title' => 'Бүтээгдэхүүн',
            'breadcrumbs' => $breadcrumbs,
            'data' => $data,
            'role' => $role
        ));
    }


    /**
     *
     * @Route("/new", name="product_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('incentive\AppBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'product_home',
                'name' => 'Бүтээгдэхүүн',
                'isActive' => 0
            ),

            array(
                'path' => 'product_new',
                'name' => 'Нэмэх',
                'isActive' => 1
            ),
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Бүтээгдэхүүн үүслээ!');

            return $this->redirectToRoute('product_home');
        }


        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        return array(
            'form' => $form->createView(),
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Бүтээгдэхүүн нэмэх',
            'role' => $role
        );
    }

    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/update/{id}", name="product_update")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function updateAction(Request $request, Product $product)
    {
        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'product_home',
                'name' => 'Бүтээгдэхүүн',
                'isActive' => 0
            ),

            array(
                'path' => 'product_update',
                'name' => 'засах',
                'isActive' => 1
            ),
        );


        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $editForm = $this->createForm(new ProductType, $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);

            $this->getDoctrine()->getManager()->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Бүтээгдэхүүн засагдлаа!');

            return $this->redirectToRoute('product_home');
        }

        return array(
            'form' => $editForm->createView(),
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Бүтээгдэхүүн засах',
            'role' => $role
        );
    }
}
