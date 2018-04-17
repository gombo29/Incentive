<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\ProductType;
use incentive\AppBundle\Functions\SpecialFunction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 *
 * @Route("/product-type")
 */
class ProductTypeController extends Controller
{
    /**
     * @Route("/", name="product_type_home")
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
                'path' => 'product_type_home',
                'name' => 'Бүтээгдэхүүн ангилал',
                'isActive' => 1
            ),

        );

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('incentiveAppBundle:ProductType')->createQueryBuilder('p');


        /**@var ProductType $data */

        $data = $qb
            ->getQuery()
            ->getArrayResult();

        return $this->render('incentiveAppBundle:ProductType:index.html.twig', array(
            'title' => 'Бүтээгдэхүүн ангилал',
            'breadcrumbs' => $breadcrumbs,
            'data' => $data,
            'role'=> $role
        ));
    }


}
