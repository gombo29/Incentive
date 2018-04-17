<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\Branch;
use incentive\AppBundle\Entity\CmsUser;
use incentive\AppBundle\Entity\Role;
use incentive\AppBundle\Form\CmsUserType;
use incentive\AppBundle\Form\CmsUserUpdateType;
use incentive\AppBundle\Functions\SpecialFunction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


/**
 * CmsUser controller.
 *
 * @Route("/cms-user")
 */
class CmsUserController extends Controller
{
    /**
     * @Route("/{page}", name="cms_user_home", requirements={"page" = "\d+"}, defaults={"page" = 1})
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
                'path' => 'cms_user_home',
                'name' => 'Ажилтан',
                'isActive' => 1
            ),

        );

        $em = $this->getDoctrine()->getManager();

        $search = false;
        $searchEntity = new CmsUser();
        $searchForm = $this->createForm('incentive\AppBundle\Form\SearchForm\UserSearchType', $searchEntity, array(
            'em' => $em
        ));

        if ($request->get("submit") == 'submit') {
            $searchForm->bind($request);
            $search = true;
        }

        $qb = $em->getRepository('incentiveAppBundle:CmsUser')->createQueryBuilder('p');


        if ($search) {
            if ($searchEntity->getUsername() && $searchEntity->getUsername() != '') {
                $qb->andWhere('p.username like :name')
                    ->setParameter('name', '%' . $searchEntity->getUsername() . '%');
            }
        }
        $countQueryBuilder = clone $qb;
        $count = $countQueryBuilder->select('count(p.id)')->getQuery()->getSingleScalarResult();
        /**@var CmsUser $data */

        $data = $qb
            ->leftJoin('p.branch', 'b')
            ->addSelect('b')
            ->orderBy('p.id', 'DESC')
            ->setFirstResult(($page - 1) * $pagesize)
            ->setMaxResults($pagesize)
            ->getQuery()
            ->getArrayResult();

        $roles = $em->getRepository('incentiveAppBundle:Role')->findAll();


        foreach ($data as $keydata => $datum) {

            if ($datum['roles']) {
                foreach ($datum['roles'] as $key => $drole) {

                    foreach ($roles as $rolem) {
                        if ($drole == $rolem->getName()) {
                            $data[$keydata]['roles'][$key] = $rolem->getDisname();
                        }
                    }


                }
            }
        }

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        return $this->render('incentiveAppBundle:CmsUser:index.html.twig', array(
            'title' => 'Ажилтан',
            'breadcrumbs' => $breadcrumbs,
            'data' => $data,
            'role' => $role,
            'pagecount' => ($count % $pagesize) > 0 ? intval($count / $pagesize) + 1 : intval($count / $pagesize),
            'count' => $count,
            'page' => $page,
            'search' => $search,
            'searchform' => $searchForm->createView(),
            'pageRoute' => 'cms_user_home',
            'pagesize' => $pagesize
        ));
    }


    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="cms_user_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),
            array(
                'path' => 'cms_user_home',
                'name' => 'Ажилтан нэмэх',
                'isActive' => 1
            ),
        );

        $user = new CmsUser();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new CmsUserType($em, null), $user, array(
            'em' => $em
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isMail = $em->getRepository("incentiveAppBundle:CmsUser")->findOneBy(array(
                'email' => $user->getEmail(),
            ));

            $isUsername = $em->getRepository("incentiveAppBundle:CmsUser")->findOneBy(array(
                'username' => $user->getUsername(),
            ));

            if ($isMail == null) {
                if ($isUsername == null) {


                    $qb = $em->getRepository('incentiveAppBundle:CmsUser')->createQueryBuilder('p');
                    $lastUserId = $qb
                        ->select('p.id')
                        ->orderBy('p.id', 'desc')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

                    if ($lastUserId) {
                        $user->setId($lastUserId['id'] + 1);
                        $user->setBranch($em->getReference('incentiveAppBundle:Branch', $user->cattmp));
                        $user->setEnabled(true);
                        $user->addRole('ROLE_CMS_USER');
                        $user->setIsHRM(false);
                        $em->persist($user);
                    }
                    $em->flush();

                    $request
                        ->getSession()
                        ->getFlashBag()
                        ->add('success', 'Ажилтан үүслээ !');
                    return $this->redirectToRoute('cms_user_home');
                } else {
                    $request
                        ->getSession()
                        ->getFlashBag()
                        ->add('danger', 'Ажилтаны нэр бүртгэлтэй байна');
                    return $this->redirectToRoute('cms_user_home');
                }
            } else {
                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('danger', 'Ажилтан бүртгэлтэй байна');

                return $this->redirectToRoute('cms_user_home');
            }
        }


        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        return array(
            'user' => $user,
            'title' => 'Ажилтан нэмэх',
            'breadcrumbs' => $breadcrumbs,
            'form' => $form->createView(),
            'role' => $role
        );
    }


    /**
     * @Route("/edit/{id}", name="cms_user_edit", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function updateAction(Request $request, CmsUser $user)
    {

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),

            array(
                'path' => 'cms_user_home',
                'name' => 'Ажилтан',
                'isActive' => 0
            ),

            array(
                'path' => 'cms_user_edit',
                'name' => 'Засах',
                'isActive' => 1
            ),
        );
        $user->cattmp = null;
        $branchId = null;
        if ($user->getBranch()) {
            $user->cattmp = $user->getBranch()->getId();
            $branchId = $user->getBranch()->getId();
        }

        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(new CmsUserUpdateType($em, $branchId), $user, array(
            'em' => $em
        ));

        if ($request->isMethod('POST')) {
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $user->setBranch($em->getReference('incentiveAppBundle:Branch', $user->cattmp));
                $em->persist($user);
                $em->flush();
                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Засагдлаа!');
                return $this->redirectToRoute('cms_user_home');
            }
        }


        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());


        return array(
            'user' => $user,
            'title' => 'Засах',
            'breadcrumbs' => $breadcrumbs,
            'form' => $editForm->createView(),
            'role' => $role
        );
    }

    /**
     * @Route("/edit/enable/{id}", name="cms_user_edit_enable", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editEnableAction(Request $request, CmsUser $user)
    {

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),

            array(
                'path' => 'cms_user_home',
                'name' => 'Ажилтан',
                'isActive' => 0
            ),

            array(
                'path' => 'cms_user_edit_enable',
                'name' => 'Төлөв өөрчлөх',
                'isActive' => 1
            ),
        );

        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm('incentive\AppBundle\Form\UserEnableType', $user, array(
            'em' => $em
        ));

        if ($request->isMethod('POST')) {
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {

                $em->flush();
                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Төлөв өөрчлөх!');
                return $this->redirectToRoute('cms_user_home');
            }
        }


        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        return array(
            'user' => $user,
            'title' => 'Төлөв өөрчлөх',
            'breadcrumbs' => $breadcrumbs,
            'form' => $editForm->createView(),
            'role' => $role
        );
    }

    /**
     * @Route("/edit/role/{id}", name="cms_user_edit_role",  requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editRoleAction(Request $request, CmsUser $user)
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),

            array(
                'path' => 'cms_user_home',
                'name' => 'Ажилтан',
                'isActive' => 0
            ),

            array(
                'path' => 'cms_user_edit_enable',
                'name' => 'Хандах эрх өөрчлөх',
                'isActive' => 1
            ),
        );


        $editForm = $this->createForm('incentive\AppBundle\Form\UserRoleType', $user, array(
            'em' => $em
        ));

        if ($request->isMethod('POST')) {
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {

                $userManager = $this->get('fos_user.user_manager');
                $userManager->updateUser($user);
                $user->addRole('ROLE_CMS_USER');

                $em->flush();
                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Хандах эрх өөрчлөгддөг!');
                return $this->redirectToRoute('cms_user_home');
            }
        }

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        return array(
            'user' => $user,
            'form' => $editForm->createView(),
            'title' => 'Хандах эрх өөрчлөх',
            'breadcrumbs' => $breadcrumbs,
            'role' => $role
        );
    }

    /**
     * @Route("/edit/password/{id}", name="cms_user_edit_password")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editPasswordAction(Request $request, CmsUser $user)
    {

        $breadcrumbs = array(
            array(
                'path' => 'home_page',
                'name' => 'Нүүр',
                'isActive' => 0
            ),

            array(
                'path' => 'cms_user_home',
                'name' => 'Ажилтан',
                'isActive' => 0
            ),

            array(
                'path' => 'cms_user_edit_enable',
                'name' => 'Нүүг үг өөрчлөх',
                'isActive' => 1
            ),
        );
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm('incentive\AppBundle\Form\UserPasswordType', $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updatePassword($user);
            $em->flush();
            $this->getDoctrine()->getManager()->flush();
            $request
                ->getSession()
                ->getFlashBag()
                ->add('success', 'Нууц үг амжилттай хадгалагдлаа!');

            return $this->redirectToRoute('cms_user_home');
        }

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());

        return array(
            'user' => $user,
            'form' => $editForm->createView(),
            'title' => 'Нүүг үг өөрчлөх',
            'breadcrumbs' => $breadcrumbs,
            'role' => $role
        );
    }


    /**
     * Deletes a User entity.
     *
     * @Route("/delete/{id}", name="cms_user_delete", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function deleteAction(Request $request, CmsUser $user)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->getDoctrine()->getManager()->flush();
        $request
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Амжилттай устлаа!');
        return $this->redirectToRoute('cms_user_home');
    }


    /**
     *
     * @Route("/hrm-new", name="cms_user_hrm_new")
     * @Method({"GET"})
     * @Template()
     */
    public function hrmAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $url = 'http://dev.incentive/test/user';


        // Хамгийн доод түвшны салбар нэгжүүдийг авч байна.

        $qb = $em->getRepository('incentiveAppBundle:Branch')->createQueryBuilder('p');
        /**@var Branch $sections */
        $sections = $qb
            ->where('p.branchType = 7')
            ->getQuery()
            ->getArrayResult();

        $sectionIds = array();

        foreach ($sections as $section) {
            array_push($sectionIds, $section['id']);
        }
        foreach ($sectionIds as $sectionId) {
            $ch = curl_init($url . '?' . $sectionId);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $values = json_decode($response);
            if ($values->code == 200) {
                foreach ($values->data as $val) {
                    if ($val->sectionId) {
                        $branch = $em->getRepository('incentiveAppBundle:Branch')->find($val->sectionId);
                        if ($branch) {
                            if ($branch->getIsHRM() == true) {
                                $user = $em->getRepository('incentiveAppBundle:CmsUser')->find($val->id);
                                if (!$user) {
                                    $user = new CmsUser();
                                    $userName = $em->getRepository('incentiveAppBundle:CmsUser')->findBy(array('username' => $val->userName));
                                    if (!$userName) {
                                        $user->setId($val->id);
                                        $user->setBranch($branch);
                                        $user->setEmail('test@mail' . $val->id . '.com');
                                        $user->setMail($val->email);
                                        $user->setUsername($val->userName);
                                        $user->setLastname($val->lastName);
                                        $user->setFirstname($val->firstName);
                                        $user->setEnabled(true);
                                        $user->setIsHRM(true);
                                        $user->setMobile($val->mobile);
                                        $user->setPassword($val->password);
                                        $userManager = $this->get('fos_user.user_manager');
                                        $userManager->updateUser($user);
                                        $user->addRole('ROLE_CMS_STAFF');
                                        $em->persist($user);
                                    }
                                } else {
                                    if ($user->getIsHRM() == true) {
                                        $user->setBranch($branch);
                                        $user->setEmail('test@mail' . $val->id . '.com');
                                        $user->setMail($val->email);
                                        $user->setUsername($val->userName);
                                        $user->setLastname($val->lastName);
                                        $user->setFirstname($val->firstName);
                                        $user->setEnabled(true);
                                        $user->setMobile($val->mobile);
                                        $user->setPassword($val->password);
                                        $userManager = $this->get('fos_user.user_manager');
                                        $userManager->updateUser($user);
                                        $user->addRole('ROLE_CMS_STAFF');
                                        $user->addRole('ROLE_CMS_USER');
                                    }
                                }
                            }
                        }
                    }
                }
                $em->flush();
                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Ажилтан амжилттай татлаа!');
            }
            return $this->redirectToRoute('cms_user_home');
        }
    }
}
