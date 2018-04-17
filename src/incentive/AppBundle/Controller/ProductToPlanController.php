<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\PlanProduct;
use incentive\AppBundle\Entity\Product;
use incentive\AppBundle\Functions\SpecialFunction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


/**
 *
 * @Route("/product-to-plan")
 */
class ProductToPlanController extends Controller
{
    /**
     * @Route("/{id}", name="product_to_plan_home")
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
                'name' => 'Төлөвлөгөө дээр бүтээгдэхүүн нэмэх',
                'isActive' => 1
            ),

        );

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('incentiveAppBundle:Product')->createQueryBuilder('p');
        /**@var Product $data */
        $data = $qb
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $qb = $em->getRepository('incentiveAppBundle:PlanProduct')->createQueryBuilder('p');
        /**@var Product $selectedProduct */
        $selectedProduct = $qb
            ->select('pro.id as productId')
            ->leftJoin('p.product', 'pro')
            ->where('p.plan = :id')
            ->setParameter('id', $id)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($data as $key => $d) {
            $isSelecter = false;
            foreach ($selectedProduct as $sb) {
                if ($d['id'] == $sb['productId'])
                    $isSelecter = true;
            }
            $data[$key]['selected'] = $isSelecter;
        }

        return $this->render('incentiveAppBundle:ProductToPlan:index.html.twig', array(
            'title' => 'Төлөвлөгөө дээр бүтээгдэхүүн нэмэх',
            'breadcrumbs' => $breadcrumbs,
            'data' => $data,
            'planId' => $id,
            'role' => $role
        ));
    }


    /**
     *
     * @Route("/create/{id}", name="product_add_to_plan", requirements={"id" = "\d+"})
     * @Method({"POST"})
     *
     */
    public function createAction(Request $request, $id)
    {
        if (strtolower($request->getMethod()) === 'post')
        {

            try {
                $insertProducts = array();
                $deleteProducts = array();

                $products = $request->request->get('questions');


                $em = $this->getDoctrine()->getManager();

                // өмнө нь хадгалагдсан байгаа бүтээгдэхүүнүүд
                $qb = $em->getRepository('incentiveAppBundle:PlanProduct')->createQueryBuilder('p');
                /**@var Product $data */
                $selectedProducts = $qb
                    ->select('b.id as productId')
                    ->leftJoin('p.product', 'b')
                    ->where('p.plan = :id')
                    ->setParameter('id', $id)
                    ->orderBy('p.id', 'ASC')
                    ->getQuery()
                    ->getResult();


                // одоо сонгогдсон, өмнө дб дээр байхгүй бүтээгдэхүүнүүдийг ялгах
                foreach ($products as $product) {
                    $isExists = false;
                    foreach ($selectedProducts as $selectedProduct) {

                        if ($product == $selectedProduct['productId']) {
                            $isExists = true;
                        }
                    }
                    if ($isExists == false) {
                        array_push($insertProducts, $product);
                    }
                }

                // өмнө нь хадгалагдсан байсан, одоо сонгогдоогүй бүтээгдэхүүнүүдийг ялгах
                foreach ($selectedProducts as $selectedProduct) {
                    $isExists = true;
                    foreach ($products as $product) {

                        if ($selectedProduct['productId'] == $product) {
                            $isExists = false;
                        }
                    }
                    if ($isExists == true) {
                        array_push($deleteProducts, $selectedProduct['productId']);
                    }
                }


                if (sizeof($insertProducts) > 0) {
                    foreach ($insertProducts as $product) {
                        $mplan = new PlanProduct();
                        $mplan->setProduct($em->getReference('incentiveAppBundle:Product', $product));
                        $mplan->setPlan($em->getReference('incentiveAppBundle:Plan', $id));
                        $em->persist($mplan);
                    }
                    $em->flush();
                }


                if (sizeof($deleteProducts) > 0) {
                    $qb = $em->getRepository('incentiveAppBundle:PlanProduct')->createQueryBuilder('p');
                    $qb
                        ->delete()
                        ->where($qb->expr()->in('p.product', $deleteProducts))
                        ->getQuery()
                        ->getResult();
                }

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Амжилтай үүслээ!');
            }
            catch (\Exception $e) {
                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('danger', 'Алдаа гарлаа');

                return $this->redirectToRoute('branch_home');
            }
        }

        return $this->redirect($this->generateUrl('product_to_plan_home', array('id' => $id)));
    }

}
