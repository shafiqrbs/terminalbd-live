<?php


namespace Appstore\Bundle\DoctorPrescriptionBundle\Controller;


use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class ReportController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }


    public function salesSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $config = $user->getGlobalOption()->getDmsConfig();
        $salesTotalTransactionOverview = $em->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->transactionOverview($config,$data);
        $serviceOverview = $em->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->findWithServiceOverview($config,$data);
        return $this->render('DoctorPrescriptionBundle:Report:salesSummary.html.twig', array(

            'salesOverview'      => $salesTotalTransactionOverview,
            'serviceOverview'               => $serviceOverview,
            'searchForm'                    => $data,

        ));

    }


    public function salesSummaryPdfAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $config = $user->getGlobalOption()->getDmsConfig();
        $salesTotalTransactionOverview = $em->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->transactionOverview($config,$data);
        $serviceOverview = $em->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->findWithServiceOverview($config,$data);
        $html = $this->renderView(
            'DoctorPrescriptionBundle:Report:salesSummaryPdf.html.twig', array(
                'salesOverview'      => $salesTotalTransactionOverview,
                'serviceOverview'               => $serviceOverview,
                'searchForm'                    => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='sales-summary-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
    }



    public function serviceBaseSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $entity = '';
        if(!empty($data) and $data['service']){
            $entity = $em->getRepository('HospitalBundle:Service')->find($data['service']);
        }
        $services = $em->getRepository('HospitalBundle:Service')->findBy(array(),array('name'=>'ASC'));
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user,$data);
        $serviceGroup = $em->getRepository('HospitalBundle:InvoiceParticular')->serviceParticularDetails($user,$data);
        return $this->render('HospitalBundle:Report:serviceBaseSales.html.twig', array(
            'serviceOverview'       => $serviceOverview,
            'serviceGroup'          => $serviceGroup,
            'services'              => $services,
            'entity'                => $entity,
            'searchForm'            => $data,
        ));

    }

    public function allYearSalesAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->allYearlySales($dmsConfig,$data);
        return $this->render('DoctorPrescriptionBundle:Report:allYearlySales.html.twig', array(
            'entities' => $dailyReceive,
            'searchForm' => $data,
        ));

    }

    public function allYearSalesPdfAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->allYearlySales($dmsConfig,$data);
        $html = $this->renderView(
            'DoctorPrescriptionBundle:Report:allYearlySalesPdf.html.twig', array(
                'entities' => $dailyReceive,
                'searchForm' => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='sales-summary-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
    }


    public function yearlySalesAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->yearlySales($dmsConfig,$data);
        return $this->render('DoctorPrescriptionBundle:Report:yearlySales.html.twig', array(
            'entities' => $dailyReceive,
            'searchForm' => $data,
        ));

    }

    public function  yearlySalesPdfAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->monthlySales($dmsConfig,$data);
        $html = $this->renderView(
            'DoctorPrescriptionBundle:Report:yearlySalesPdf.html.twig', array(
                'entities' => $dailyReceive,
                'searchForm' => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='yearly-sales-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');

    }


    public function monthlySalesAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->monthlySales($dmsConfig,$data);
        return $this->render('DoctorPrescriptionBundle:Report:monthlySales.html.twig', array(
            'entities' => $dailyReceive,
            'searchForm' => $data,
        ));

    }

    public function monthlySalesPdfAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->monthlySales($dmsConfig,$data);
        $html = $this->renderView(
            'DoctorPrescriptionBundle:Report:monthlySalesPdf.html.twig', array(
                'entities' => $dailyReceive,
                'searchForm' => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='monthly-sales-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');

    }

    public function salesAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->salesDetails($config,$data);
        $assignDoctors = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsParticular')->getFindWithParticular($config,array('doctor'));
        $treatments = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsParticular')->getFindDentalServiceParticular($config,array('treatment'));

        return $this->render('DoctorPrescriptionBundle:Report:sales.html.twig', array(
            'entities' => $dailyReceive,
            'assignDoctors' => $assignDoctors,
            'treatments' => $treatments,
            'searchForm' => $data,
        ));

    }

    public function salesPdfAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->salesDetails($config,$data);
        $html = $this->renderView(
            'DoctorPrescriptionBundle:Report:salesPdf.html.twig', array(
                'entities' => $dailyReceive,
                'searchForm' => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='daily-sales-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');

    }


    public function treatmentWiseSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $config = $user->getGlobalOption()->getDmsConfig();
        $serviceOverview = $em->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->findWithServiceOverview($config,$data);

        return $this->render('DoctorPrescriptionBundle:Report:treatmentWiseSales.html.twig', array(
            'serviceOverview' => $serviceOverview,
            'searchForm' => $data,
        ));

    }

    public function treatmentWiseSalesPdfAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $config = $user->getGlobalOption()->getDmsConfig();
        $serviceOverview = $em->getRepository('DoctorPrescriptionBundle:DpsTreatmentPlan')->findWithServiceOverview($config,$data);
        $html = $this->renderView(
            'DoctorPrescriptionBundle:Report:treatmentWiseSalesPdf.html.twig', array(
                'serviceOverview' => $serviceOverview,
                'searchForm' => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='daily-sales-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');

    }


    public function cashAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $transactionMethods = array(1,4);
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionWiseOverview( $this->getUser(),$data);
        $transactionBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionBankCashOverview( $this->getUser(),$data);
        $transactionMobileBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionMobileBankCashOverview( $this->getUser(),$data);
        $transactionAccountHeadCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionAccountHeadCashOverview( $this->getUser(),$data);
        return $this->render('DoctorPrescriptionBundle:Report:cashoverview.html.twig', array(
            'transactionCashOverviews'               => $transactionCashOverview,
            'transactionBankCashOverviews'          => $transactionBankCashOverviews,
            'transactionMobileBankCashOverviews'    => $transactionMobileBankCashOverviews,
            'transactionAccountHeadCashOverviews'   => $transactionAccountHeadCashOverviews,
            'searchForm' => $data,
        ));

    }


    public function cashPdfAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $transactionMethods = array(1,4);
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionWiseOverview( $this->getUser(),$data);
        $transactionBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionBankCashOverview( $this->getUser(),$data);
        $transactionMobileBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionMobileBankCashOverview( $this->getUser(),$data);
        $transactionAccountHeadCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionAccountHeadCashOverview( $this->getUser(),$data);
        $html = $this->renderView('DoctorPrescriptionBundle:Report:cashoverviewPdf.html.twig', array(
            'transactionCashOverviews'               => $transactionCashOverview,
            'transactionBankCashOverviews'          => $transactionBankCashOverviews,
            'transactionMobileBankCashOverviews'    => $transactionMobileBankCashOverviews,
            'transactionAccountHeadCashOverviews'   => $transactionAccountHeadCashOverviews,
            'searchForm' => $data,
        ));

        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='cash-summary-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
        exit;
    }


    public function incomeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $sales = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->reportDebitTransactionIncome($user->getGlobalOption(), $accountHeads = array(3,10,30), $data);
        $expenditures = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(37), $data);
        $accessories = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsInvoiceAccessories')->reportAccessoriesOut($user->getGlobalOption()->getDmsConfig(), $data);
        return $this->render('DoctorPrescriptionBundle:Report:income.html.twig', array(
            'sales'             => $sales,
            'expenditures'      => $expenditures,
            'accessories'       => $accessories,
            'searchForm'        => $data,
        ));

    }

    public function incomePdfAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $sales = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->reportDebitTransactionIncome($user->getGlobalOption(), $accountHeads = array(3,10,30), $data);
        $expenditures = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(37), $data);
        $accessories = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsInvoiceAccessories')->reportAccessoriesOut($user->getGlobalOption()->getDmsConfig(), $data);
        $html = $this->renderView('DoctorPrescriptionBundle:Report:incomePdf.html.twig', array(
            'sales'             => $sales,
            'expenditures'      => $expenditures,
            'accessories'       => $accessories,
            'searchForm'        => $data,
        ));


        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='income-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
        exit;

    }

    public function expenditureAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $expenditures = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->reportForExpenditure($user->getGlobalOption(), $data);
        return $this->render('DoctorPrescriptionBundle:Report:expenditure.html.twig', array(
                'expenditures'      => $expenditures,
                'searchForm'        => $data,
        ));

    }

    public function expenditurePdfAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $expenditures = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->reportForExpenditure($user->getGlobalOption(), $data);
        $html = $this->renderView('DoctorPrescriptionBundle:Report:expenditurePdf.html.twig', array(
            'expenditures'      => $expenditures,
            'searchForm'        => $data,
        ));

        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='expense-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
        exit;

    }

    public function stockOutAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $stockOuts = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsInvoiceAccessories')->getAccessoriesItemOut($user->getGlobalOption()->getDmsConfig(), $data);
        return $this->render('DoctorPrescriptionBundle:Report:stockout.html.twig', array(
            'entities'      => $stockOuts,
            'searchForm'        => $data,
        ));

    }

    public function stockoutPdfAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $stockOuts = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsInvoiceAccessories')->getAccessoriesItemOut($user->getGlobalOption()->getDmsConfig(), $data);
        $html = $this->renderView('DoctorPrescriptionBundle:Report:stockoutPdf.html.twig', array(
            'entities'      => $stockOuts,
            'searchForm'        => $data,
        ));

        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='stock-out-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
        exit;

    }

    public function stockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getDmsConfig();
        $entities = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsParticular')->getMedicineParticular($config,array('accessories'),$data)->getArrayResult();
        return $this->render('DoctorPrescriptionBundle:Report:stock.html.twig', array(
            'pagination' => $entities,
            'searchForm'        => $data,
        ));
    }

    public function stockPdfAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getDmsConfig();
        $entities = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsParticular')->getMedicineParticular($config,array('accessories'),$data)->getArrayResult();
        $html =  $this->renderView('DoctorPrescriptionBundle:Report:stockPdf.html.twig', array(
            'pagination' => $entities,
            'searchForm'        => $data,
        ));

        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='stock-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
        exit;

    }

}

