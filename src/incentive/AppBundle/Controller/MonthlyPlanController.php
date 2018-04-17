<?php

namespace incentive\AppBundle\Controller;

use incentive\AppBundle\Entity\Branch;
use incentive\AppBundle\Entity\BranchData;
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
 * MonthlyPlan controller.
 *
 * @Route("/monthly-plan")
 */
class MonthlyPlanController extends Controller
{

    var $branches = array();
    var $branchArr = array();

    /**
     * @Route("/{id}/{page}", name="monthly_plan_home", requirements={"page" = "\d+","id" = "\d+"}, defaults={"page" = 1})
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

    /**
     * @Route("/create-excel/{id}", name="create_excel", requirements={"id" = "\d+"})
     */
    public function createExcelAction(Request $request, $id)
    {
        /*
         * User Role-s хамаарч branch ялгаатай гарна
         */

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('incentiveAppBundle:PlanBranch')->createQueryBuilder('p');
        /**@var Branch $data */

        // сонгогдсон салбаруудыг авч байна
        $selectedBranches = $qb
            ->select('p.id', 'b.name as name', 'b.id as branchId', 'bt.id as branchTypeId', 'pa.id as parentId')
            ->leftJoin('p.branch', 'b')
            ->leftJoin('b.parent', 'pa')
            ->leftJoin('b.branchType', 'bt')
            ->where('p.plan = :id')
            ->setParameter('id', $id)
            ->orderBy('b.id', 'desc')
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

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('incentiveAppBundle:CmsUser')->createQueryBuilder('p');
        $branchId = $qb
            ->select('b.id')
            ->leftJoin('p.branch', 'b')
            ->where('p.id = :userid')
            ->setParameter("userid", $this->getUser()->getId())
            ->getQuery()
            ->getArrayResult();


        $isAdmin = false;


        if (in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles())) {
            $isAdmin = true;
        }

        if (!$isAdmin) {
            $this->getBranchById($branchDataTree, $branchId[0]['id']);
            $this->getListBranchFromTree(array($this->branchArr));
        } else {
            $this->getListBranchFromTree($branchDataTree);
        }


        // мод бүтэцтэй болгосон салбаруудыг жагсаалт болгож байна

        // сонгогдсон бүтээгдэхүүнийг авч байна
        $qb = $em->getRepository('incentiveAppBundle:PlanProduct')->createQueryBuilder('p');
        /**@var Product $data */
        $selectedProducts = $qb
            ->select('b.name as name', 'b.id as productId', 'p.id as planProductId')
            ->leftJoin('p.product', 'b')
            ->where('p.plan = :id')
            ->setParameter('id', $id)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();


        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("E-Tuslakh")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");

        // Default header загвар
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 16,
                'name' => 'Calibri'
            ),
            'size' => array(
                'width' => 120
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleListArray = array(
            'font' => array(
                'color' => array('rgb' => '000000'),
                'size' => 14,
                'name' => 'Calibri'
            ),
            'size' => array(
                'width' => 120
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );


        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', '#');

        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()
            ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('656565');

        // Өмнө бүртгэлтэй байсан салбар draft устгаж байна

        $oldBranchDraftArray = array();
        $qb = $em->getRepository('incentiveAppBundle:BranchDraft')->createQueryBuilder('p');
        $oldBranchDrafts = $qb
            ->select('p.id')
            ->where('p.plan = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();


        foreach ($oldBranchDrafts as $oldBranchDraft) {
            array_push($oldBranchDraftArray, $oldBranchDraft['id']);
        }


        if (sizeof($oldBranchDraftArray) > 0) {
            $qb = $em->getRepository('incentiveAppBundle:BranchDraft')->createQueryBuilder('p');
            $qb
                ->delete()
                ->where($qb->expr()->in('p.id', $oldBranchDraftArray))
                ->getQuery()
                ->getResult();
        }


        // Салбаруудыг A багана дагуу өрж байна
        foreach ($this->branches as $key => $selBranch) {
//            if ($selBranch['branchId'] != $branchId[0]['id']) {
            $sheetNum = $key + 2; // 2 mornoos
            $line = '';
            if ($selBranch['branchTypeId'] != 1) {
                for ($i = 0; $i <= $selBranch['branchTypeId']; $i++) {
                    $line = $line . '   ';
                }
            }

            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A' . $sheetNum, $line . $selBranch['name']);

            if ($selBranch['branchTypeId'] == 1) {
                $phpExcelObject->getActiveSheet()->getStyle('A' . $sheetNum)->getFont()->setBold(true);
            }


            // Салбар драфт болгон хадгалаж байна.
            $branchDraft = new BranchDraft();
            $branchDraft->setPlanBranch($em->getReference('incentiveAppBundle:PlanBranch', $selBranch['planBranchId']));
            $branchDraft->setPlan($em->getReference('incentiveAppBundle:Plan', $id));
            $branchDraft->setRowNumber($sheetNum);
            $em->persist($branchDraft);

            $phpExcelObject->getActiveSheet()->getStyle('A' . $sheetNum)->applyFromArray($styleListArray);
//            }
        }

        // ============= Баганууд өрж эхэлж байна ==================

        // Өмнө бүртгэлтэй байсан product draft устгаж байна
        $oldProductDraftArray = array();
        $qb = $em->getRepository('incentiveAppBundle:ProductDraft')->createQueryBuilder('p');
        $oldProductDrafts = $qb
            ->select('p.id')
            ->where('p.plan = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

        foreach ($oldProductDrafts as $oldProductDraft) {
            array_push($oldProductDraftArray, $oldProductDraft['id']);
        }

        if (sizeof($oldProductDraftArray) > 0) {
            $qb = $em->getRepository('incentiveAppBundle:ProductDraft')->createQueryBuilder('p');
            $qb
                ->delete()
                ->where($qb->expr()->in('p.id', $oldProductDraftArray))
                ->getQuery()
                ->getResult();
        }

        // Бүтээгдэхүүн A-с хойших дагуу баганад өрж байна
        foreach ($selectedProducts as $key => $selProduct) {

            $sheetString = $this->getColumnLetter($key + 1);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue($sheetString . 1, $selProduct['name']);

            $phpExcelObject->getActiveSheet()->getColumnDimension($sheetString)->setWidth(20);
            $phpExcelObject->getActiveSheet()->getStyle($sheetString . 1)->applyFromArray($styleArray);
            $phpExcelObject->getActiveSheet()->getStyle($sheetString . 1)->getFill()
                ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('656565');

            // Салбар драфт болгон хадгалаж байна.
            $productDraft = new ProductDraft();
            $productDraft->setPlanProduct($em->getReference('incentiveAppBundle:PlanProduct', $selProduct['planProductId']));
            $productDraft->setPlan($em->getReference('incentiveAppBundle:Plan', $id));
            $productDraft->setColNumber($sheetString);
            $em->persist($productDraft);
        }

        $em->flush();


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

        // Утга болон Border нэмж байна
        foreach ($selectedProducts as $key => $selProduct) {
            $sheetString = $this->getColumnLetter($key + 1);
            foreach ($this->branches as $keym => $selBranch) {
                $sheetNum = $keym + 2;
                $phpExcelObject->getActiveSheet()->getStyle($sheetString . $sheetNum)->applyFromArray($styleListArray);

                foreach ($mplan as $mp) {
                    if ($mp['productId'] == $selProduct['productId'] && $mp['branchId'] == $selBranch['branchId']) {
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValue($sheetString . $sheetNum, $mp['serviceValue']);
                    }
                }


            }
        }


        // Sheet нэр
        $phpExcelObject->getActiveSheet()->setTitle('Төлөвлөгөө');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);


        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');

        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'tuluvluguu.xls'
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }


    /**
     * @Route("/upload-excel/{id}", name="upload_excel", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function excelParseAction(Request $request, $id)
    {
        $monthlyPlan = new MonthlyPlan();
        $form = $this->createForm('incentive\AppBundle\Form\ExcelUploadType', $monthlyPlan);
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
                'name' => 'Төлөвлөгөө оруулах',
                'isActive' => 1
            ),

        );

        $sp = new SpecialFunction($this->container);
        $role = $sp->getRoles($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $monthlyPlan->uploadExcel($this->container);

            $resources = $this->container->getParameter('statfolder');
            $dir = 'excel/draft';
            $path = $resources . '/' . $dir . "/excel.xls";
            $file = $path;
            if (!file_exists($file)) {
                exit("file not found");
            }

            $objPHPExcel = \PHPExcel_IOFactory::load($file);
            $arr = array();


            $qb = $em->getRepository('incentiveAppBundle:ProductDraft')->createQueryBuilder('p');
            /**@var ProductDraft $datas */

            // сонгогдсон бүтээгдэхүүн авч байна
            $productDrafts = $qb
                ->select('pr.id', 'p.colNumber')
                ->leftJoin('p.planProduct', 'pr')
                ->where('p.plan = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();


            // Excel файл parse хийж утгуудийг array болгон $arr-д хадгалав
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                foreach ($worksheet->getRowIterator() as $key => $row) {
                    if ($key > 1) {
                        foreach ($productDrafts as $productDraft) {
                            $productDraft['colNumber'];
                            array_push($arr
                                , array(
                                    'rowNumber' => $key,
                                    'productPlanData' => array(
                                        'id' => $productDraft['id'],
                                        'value' => $worksheet->getCell($productDraft['colNumber'] . $key)->getCalculatedValue()),
                                ));
                        }
                    }
                }
            }

            $em = $this->getDoctrine()->getManager();
            $qb = $em->getRepository('incentiveAppBundle:BranchDraft')->createQueryBuilder('p');
            /**@var BranchDraft $datas */
            // сонгогдсон салбаруудыг авч байна
            $datas = $qb
                ->select('pb.id as planBranchId', 'p.rowNumber', 'b.id as branchId')
                ->leftJoin('p.planBranch', 'pb')
                ->leftJoin('pb.branch', 'b')
                ->where('p.plan = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();

            $branchIds = array();

            foreach ($datas as $data) {
                array_push($branchIds, $data['branchId']);
            }

            // Өмнө бүртгэлтэй байсан MonthlyPlan draft устгаж байна
            $oldMonthlyPlanDraftArray = array();

            $qb = $em->getRepository('incentiveAppBundle:MonthlyPlan')->createQueryBuilder('p');
            $oldMonthlyPlanDrafts = $qb
                ->select('p.id')
                ->leftJoin('p.planBranch', 'pb')
                ->andWhere($qb->expr()->in('pb.branch', ':branchIds'))
                ->setParameter("branchIds", $branchIds)
                ->getQuery()
                ->getArrayResult();

            foreach ($oldMonthlyPlanDrafts as $oldMonthlyPlanDraft) {
                array_push($oldMonthlyPlanDraftArray, $oldMonthlyPlanDraft['id']);
            }

            if (sizeof($oldMonthlyPlanDraftArray) > 0) {
                $qb = $em->getRepository('incentiveAppBundle:MonthlyPlan')->createQueryBuilder('p');
                $qb
                    ->delete()
                    ->where($qb->expr()->in('p.id', $oldMonthlyPlanDraftArray))
                    ->getQuery()
                    ->getResult();
            }

            foreach ($datas as $data) {
                foreach ($arr as $item) {
                    if ($data['rowNumber'] == $item['rowNumber']) {
                        $mplan = new MonthlyPlan();
                        $mplan->setPlanBranch($em->getReference('incentiveAppBundle:PlanBranch', $data['planBranchId']));
                        $mplan->setPlanProduct($em->getReference('incentiveAppBundle:PlanProduct', $item['productPlanData']['id']));
                        $mplan->setPlan($em->getReference('incentiveAppBundle:Plan', $id));
                        $mplan->setServiceValue($item['productPlanData']['value']);
                        $em->persist($mplan);
                    }
                }

            }
            $em->flush();
            return $this->redirectToRoute('monthly_plan_home', array(
                'id' => $id
            ));
        }

        return array(
            'monthlyplan' => $monthlyPlan,
            'form' => $form->createView(),
            'title' => 'Төлөвлөгөө оруулах',
            'breadcrumbs' => $breadcrumbs,
            'role' => $role,
        );
    }


    // Бүтээгдэхүүн E-с хойших дагуу баганад өрөхөд латин цагаан толгой өгдөг функц
    function getColumnLetter($number)
    {
        $prefix = '';
        $suffix = '';
        $prefNum = intval($number / 26);
        if ($number > 25) {
            $prefix = $this->getColumnLetter($prefNum - 1);
        }
        $suffix = chr(fmod($number, 26) + 65);
        return $prefix . $suffix;
    }


    // Role хамаарч салбар өгөх
    public function getBranchById($branchesTemp, $id)
    {
        foreach ($branchesTemp as $branch) {

            if ($branch['branchId'] == $id) {
                $this->branchArr = $branch;

            } else {
                if ($branch['children']) {
                    $this->getBranchById($branch['children'], $id);
                }
            }
        }
    }


    // Жагсаалт болгож буй функц
    public function getListBranchFromTree($branchesTemp)
    {
        $this->setLineBranch($branchesTemp);
        return $this->branches;
    }


    // Жагсаалт болгож буй функц
    public function setLineBranch($branchesTemp)
    {
        foreach ($branchesTemp as $branch) {
            $branchTemp = array(
                'name' => $branch['name'],
                'branchId' => $branch['branchId'],
                'branchTypeId' => $branch['branchTypeId'],
                'parentId' => $branch['parentId'],
                'planBranchId' => $branch['id'],
            );

            array_push($this->branches, $branchTemp);

            if ($branch['children']) {
                $this->setLineBranch($branch['children']);
            }
        }
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


    // Оруулсан утга засаж байна
    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/branch-data-update", name="branch_data_update")
     * @Method({"POST"})
     * @Template()
     */
    public function updateAction(Request $request)
    {
        $values = $request->request->get('data');

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('incentiveAppBundle:MonthlyPlan')->createQueryBuilder('p');

        foreach ($values as $value) {

            if ($value['planvalue'] != null) {
                $qb
                    ->update()
                    ->set('p.serviceValue', $value['planvalue'])
                    ->where('p.id = :id')
                    ->setParameter('id', $value['planId'])
                    ->getQuery()
                    ->getResult();
            }
        }

        return new JsonResponse(array('status' => 'success'));
    }


    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/add-staffs", name="add_staffs")
     * @Method({"POST"})
     * @Template()
     */
    public function addStaffsAction(Request $request)
    {


        return new JsonResponse(
            array(
                'code' => 0,
                'result' => 'Амжилттай',
            ));
    }


    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/get-staffs", name="get_staffs")
     * @Method({"POST"})
     * @Template()
     */
    public function getStaffsAction(Request $request)
    {

        $id = $request->request->get('id');
        $planiId = $request->request->get('planId');

        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('incentiveAppBundle:MonthlyPlanStaff')->createQueryBuilder('p');
        $staffs = $qb
            ->select('p.id, b.id as branchId, p.timeFund, p.relaxTime, p.valuePercent , u.id as staffId, u.lastname, u.firstname , pl.id as planId')
            ->leftJoin('p.user', 'u')
            ->leftJoin('p.branch', 'b')
            ->leftJoin('p.plan', 'pl')
            ->where('pl.id = :planId')
            ->setParameter('planId', $planiId)
            ->andWhere('b.id= :branchId')
            ->setParameter('branchId', $id)
            ->getQuery()
            ->getResult();

        $qb = $em->getRepository('incentiveAppBundle:PlanProductUser')->createQueryBuilder('p');
        $staffsProductValues = $qb
            ->select('pro.id as productId', 'pb.id as staffId', 'p.value as productValue')
            ->leftJoin('p.product', 'pro')
            ->leftJoin('p.staff', 'pb')
            ->where('p.plan = :planId')
            ->setParameter('planId', $planiId)
            ->getQuery()
            ->getResult();


        foreach ($staffs as $key => $staff) {
            $productData = array();
            foreach ($staffsProductValues as $staffsProductValue) {
                if ($staff['staffId'] == $staffsProductValue['staffId']) {
                    $productData += array($staffsProductValue['productId'] => $staffsProductValue['productValue']);
                }
            }
            $staffs[$key]['productValues'] = $productData;
        }

        return new JsonResponse(
            array(
                'code' => 0,
                'result' => 'Амжилттай',
                'data' => $staffs
            ));
    }


    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/delete-staffs", name="delete_staffs")
     * @Method({"DELETE"})
     * @Template()
     */
    public function deleteStaffsAction(Request $request)
    {

        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($id) {
            $monthlyPlanStaff = $em->getRepository('incentiveAppBundle:MonthlyPlanStaff')->find($id);
            $em->remove($monthlyPlanStaff);
            $em->flush();

            return new JsonResponse(
                array(
                    'code' => 0,
                    'result' => 'Амжилттай',
                ));

        } else {
            return new JsonResponse(
                array(
                    'code' => 1,
                    'result' => 'Алдаа гарлаа',
                ));
        }

    }

    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/insert-staff", name="insert_staffs")
     * @Method({"POST"})
     * @Template()
     */
    public function insertStaffAction(Request $request)
    {
        $timeFund = $request->request->get('timeFund');
        $relaxTime = $request->request->get('relaxTime');
        $valuePercent = $request->request->get('valuePercent');
        $planId = $request->request->get('planId');
        $staffId = $request->request->get('staffId');
        $branchId = $request->request->get('branchId');

        $em = $this->getDoctrine()->getManager();

        // Салбарын ажилтаны гүйцэтгэлийн % өмнө үүссэн эсэхийг шалгаж байна
        $qb = $em->getRepository('incentiveAppBundle:MonthlyPlanStaff')->createQueryBuilder('p');
        $isSelectedStaff = $qb
            ->select('p.id')
            ->where('p.plan = :planId')
            ->setParameter('planId', $planId)
            ->andWhere('p.user = :userId')
            ->setParameter('userId', $staffId)
            ->andWhere('p.branch = :branchId')
            ->setParameter('branchId', $branchId)
            ->getQuery()
            ->getResult();


        // Салбарын ажилтаны гүйцэтгэлийн %-г insert хийж байна
        if (sizeof($isSelectedStaff) > 0) {
            $mplanStaff = $em->getRepository('incentiveAppBundle:MonthlyPlanStaff')->find($isSelectedStaff[0]['id']);
        } else {
            $mplanStaff = new MonthlyPlanStaff();
        }

        $mplanStaff->setPlan($em->getReference('incentiveAppBundle:Plan', $planId));
        $mplanStaff->setRelaxTime($relaxTime);
        $mplanStaff->setTimeFund($timeFund);
        $mplanStaff->setValuePercent($valuePercent);
        $mplanStaff->setUser($em->getReference('incentiveAppBundle:CmsUser', $staffId));
        $em->persist($mplanStaff);
        $em->flush();

        //************ Эндээс эргээд view дээр харуулах дата-г баазаас авч байна

        // Ажилтан сонголоо
        $qb = $em->getRepository('incentiveAppBundle:CmsUser')->createQueryBuilder('p');
        $Staff = $qb
            ->select('b.id as branchId')
            ->leftJoin('p.branch', 'b')
            ->where('p.id= :id')
            ->setParameter('id', $staffId)
            ->getQuery()
            ->getResult();


        //Тухайн салбарын ажилчдын тоог авч байна үүнийг авч байгаа шалтгаан нь гүйцэтгэлийн үнэлгээг бодоход нийт ажилтаны тоонд хуваадаг юм
        $qb = $em->getRepository('incentiveAppBundle:MonthlyPlanStaff')->createQueryBuilder('p');
        $staffsCount = $qb
            ->select('count(p.id)')
            ->where('p.plan = :planId')
            ->setParameter('planId', $planId)
            ->andWhere('p.branch= :branchId')
            ->setParameter('branchId', $branchId)
            ->getQuery()
            ->getSingleScalarResult();

        //Тухайн салбарын бүтээгдэхүүд дээрх төлөвлөгөөний тоог авч байна
        $qb = $em->getRepository('incentiveAppBundle:MonthlyPlan')->createQueryBuilder('p');
        $staffsProductValues = $qb
            ->select('pro.id as productId', 'p.serviceValue as productValue', 'b.id as branchId')
            ->leftJoin('p.planProduct', 'pp')
            ->leftJoin('pp.product', 'pro')
            ->leftJoin('p.planBranch', 'pb')
            ->leftJoin('pb.branch', 'b')
            ->where('p.plan = :planId')
            ->setParameter('planId', $planId)
            ->andWhere('b.id = :branchId')
            ->setParameter('branchId', $Staff[0]['branchId'])
            ->getQuery()
            ->getResult();

        $proIds = array();

        foreach ($staffsProductValues as $productValue) {
            array_push($proIds, $productValue['productId']);
        }


        //*************** Салбарын ажилтаны гүйцэтгэлийн үнэлгээ бүтээгдэхүүн тус бүр дээр өмнө үүссэн эсэхийг шалгаж байна
        $qb = $em->getRepository('incentiveAppBundle:PlanProductUser')->createQueryBuilder('p');
        $isSelectedStaffs = $qb
            ->select('p.id', 'st.id as staffId', 'pro.id as productId')
            ->leftJoin('p.staff', 'st')
            ->leftJoin('p.product', 'pro')
            ->where('p.plan = :planId')
            ->setParameter('planId', $planId)
            ->andWhere('p.staff = :staffId')
            ->setParameter('staffId', $staffId)
            ->andWhere($qb->expr()->in('p.product ', $proIds))
            ->getQuery()
            ->getResult();

        $productData = array();

        foreach ($staffsProductValues as $staffsProductValue) {
            $isData = false;
            $prouserId = null;
            foreach ($isSelectedStaffs as $isSelectedStaff) {
                if ($isSelectedStaff['productId'] == $staffsProductValue['productId']) {
                    $isData = true;
                    $prouserId = $isSelectedStaff['id'];
                }
            }

            if ($isData == true) {
                $productUser = $em->getRepository('incentiveAppBundle:PlanProductUser')->find($prouserId);
            } else {
                $productUser = new PlanProductUser();
            }
            $val = ($valuePercent / 100) * ($staffsProductValue['productValue'] / $staffsCount);

            $productUser->setValue($val);
            $productUser->setProduct($em->getReference('incentiveAppBundle:Product', $staffsProductValue['productId']));
            $productUser->setStaff($em->getReference('incentiveAppBundle:CmsUser', $staffId));
            $productUser->setPlan($em->getReference('incentiveAppBundle:Plan', $planId));
            $em->persist($productUser);

            array_push($productData, array(
                'productId' => $staffsProductValue['productId'],
                'productValue' => $val,
                'branchId' => $staffsProductValue['branchId']

            ));
        }
        $em->flush();

        foreach ($Staff as $key => $staf) {
            $productD = array();

            foreach ($productData as $productDatum) {
                if ($staf['branchId'] == $productDatum['branchId']) {
                    array_push($productD, array(
                        'productId' => $productDatum['productId'],
                        'productValue' => $productDatum['productValue']
                    ));


                }
            }
            $Staff[$key]['productValues'] = $productD;
        }

        return new JsonResponse(
            array(
                'code' => 0,
                'result' => 'Амжилттай',
                'data' => $Staff
            ));
    }


    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/insert-staff-all", name="insert_staffs_all")
     * @Method({"POST"})
     * @Template()
     */
    public
    function insertStaffAllAction(Request $request)
    {
        $staffIds = $request->request->get('staffIds');
        $planId = $request->request->get('planId');
        $branchId = $request->request->get('branchId');

        if ($staffIds && $planId && $branchId) {
            $em = $this->getDoctrine()->getManager();
            foreach ($staffIds as $staffId) {
                $qb = $em->getRepository('incentiveAppBundle:MonthlyPlanStaff')->createQueryBuilder('p');
                $isSelectedStaff = $qb
                    ->select('p.id')
                    ->where('p.plan = :planId')
                    ->setParameter('planId', $planId)
                    ->andWhere('p.user = :userId')
                    ->setParameter('userId', $staffId)
                    ->andWhere('p.branch = :branchId')
                    ->setParameter('branchId', $branchId)
                    ->getQuery()
                    ->getResult();

                if (sizeof($isSelectedStaff) > 0) {
                    $mplanStaff = $em->getRepository('incentiveAppBundle:MonthlyPlanStaff')->find($isSelectedStaff[0]['id']);
                } else {
                    $mplanStaff = new MonthlyPlanStaff();
                }
                $mplanStaff->setPlan($em->getReference('incentiveAppBundle:Plan', $planId));
                $mplanStaff->setRelaxTime(0);
                $mplanStaff->setTimeFund(0);
                $mplanStaff->setValuePercent(0);
                $mplanStaff->setBranch($em->getReference('incentiveAppBundle:Branch', $branchId));
                $mplanStaff->setUser($em->getReference('incentiveAppBundle:CmsUser', $staffId));
                $em->persist($mplanStaff);
            }
            $em->flush();

            $alertArr = array(
                'code' => 0,
                'result' => 'Амжилттай',
            );
        } else {
            $alertArr = array(
                'code' => 1,
                'result' => 'Алдаа гарлаа',
            );
        }

        return new JsonResponse($alertArr);
    }
}
