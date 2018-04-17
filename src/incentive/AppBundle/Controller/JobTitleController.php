<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\Branch;
use incentive\AppBundle\Entity\JobTitle;
use incentive\AppBundle\Entity\Plan;
use incentive\AppBundle\Entity\Role;
use incentive\AppBundle\Form\BranchType;
use incentive\AppBundle\Form\JobTitleType;
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
 * @Route("/job-title")
 */
class JobTitleController extends Controller
{
    /**
     * @Route("/", name="job_title_home")
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
                'path' => 'job_title_home',
                'name' => 'Албан тушаал',
                'isActive' => 1
            ),
        );

        $em = $this->getDoctrine()->getManager();


        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $qb = $em->getRepository('incentiveAppBundle:JobTitle')->createQueryBuilder('p');

        $data = $qb
            ->getQuery()
            ->getArrayResult();

        return $this->render('incentiveAppBundle:JobTitle:index.html.twig', array(
            'title' => 'Албан тушаал',
            'breadcrumbs' => $breadcrumbs,
            'data' => $data,
            'role' => $role,
            'pageRoute' => 'job_title_home',
        ));
    }


    /**
     *
     * @Route("/new", name="job_title_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        $jobTitle = new JobTitle();
        $form = $this->createForm(new JobTitleType($em, null), $jobTitle);
        $form->handleRequest($request);

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'job_title_home',
                'name' => 'Албан тушаал',
                'isActive' => 0
            ),

            array(
                'path' => 'job_title_new',
                'name' => 'Нэмэх',
                'isActive' => 1
            ),
        );

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($jobTitle);
            $em->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Салбар, нэгж үүслээ!');
            return $this->redirectToRoute('job_title_home');
        }
        return array(
            'form' => $form->createView(),
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Албан тушаал нэмэх',
            'role' => $role
        );
    }

    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/update/{id}", name="job_title_update")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function updateAction(Request $request, JobTitle $jobTitle)
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
                'path' => 'job_title_home',
                'name' => 'Албан тушаал',
                'isActive' => 0
            ),

            array(
                'path' => 'job_title_update',
                'name' => 'засах',
                'isActive' => 1
            ),
        );


        $editForm = $this->createForm(new JobTitleType($em, null), $jobTitle);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($jobTitle);

            $this->getDoctrine()->getManager()->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Албан тушаал засагдлаа!');

            return $this->redirectToRoute('job_title_home');
        }

        return array(
            'form' => $editForm->createView(),
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Албан тушаал засах',
            'role' => $role
        );
    }


    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/delete/{id}", name="job_title_delete")
     * @Method({"GET", "POST"})
     *
     */
    public function deleteAction(Request $request, JobTitle $jobTitle)
    {
        try {

            $em = $this->getDoctrine()->getManager();
            $em->remove($jobTitle);
            $em->flush();

            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Албан тушаал устлаа!');
        } catch (\Exception $e) {
            $request
                ->getSession()
                ->getFlashBag()
                ->add('danger', 'Алдаа гарлаа!');
        }

        return $this->redirectToRoute('job_title_home');
    }
}
