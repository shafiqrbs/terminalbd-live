<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Knp\Snappy\Pdf;
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

    public function patientDownloadAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user,$mode ='admission', $data);
        $lists = $entities->getQuery()->getResult();
        $html = $this->renderView(
            'HospitalBundle:Report:admission/patient.html.twig', array(
            'entities' => $lists,
            'globalOption' => $user->getGlobalOption(),
            'searchForm' => $data,
        ));
        $date = date('d-m-Y');
        $date = "{$date}-patient.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');
    }

    public function salesSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        if (!empty($data['date'])) {
            $datetime = new \DateTime($data['date']);
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }

        $salesTotalTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user, $data);
        $salesTodayTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user, $data, 'false', array('diagnostic', 'admission','visit'));
        $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user, $data, 'true', array('diagnostic', 'admission','visit'));

        $diagnosticOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'diagnostic');
        $admissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'admission');
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user, $data);
        $transactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->findWithTransactionOverview($user, $data);
        $commissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithCommissionOverview($user, $data);

        $salesTodayUser      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesUsers($user);
        $salesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'false',array('diagnostic','admission','visit'));
        $previousSalesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'true',array('diagnostic','admission','visit'));
        $userSalesTodaySalesCommission      = $em->getRepository('HospitalBundle:DoctorInvoice')->userGroupCommissionSummary($user,$data);
        $userInvoiceReturn   = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->userGroupInvoiceReturnAmount($user,$data);


        return $this->render('HospitalBundle:Report:salesSummary.html.twig', array(

            'salesTodayUsers'     => $salesTodayUser,
            'salesTotalTransactionOverview' => $salesTotalTransactionOverview,
            'salesTodayTransactionOverview' => $salesTodayTransactionOverview,
            'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'diagnosticOverview' => $diagnosticOverview,
            'admissionOverview' => $admissionOverview,
            'serviceOverview' => $serviceOverview,
            'transactionOverview' => $transactionOverview,
            'commissionOverview' => $commissionOverview,
            'salesTodayUserTransactionOverview'     => $salesTodayUserTransactionOverview,
            'previousSalesTodayUserTransactionOverview'     => $previousSalesTodayUserTransactionOverview,
            'userSalesTodaySalesCommission'     => $userSalesTodaySalesCommission,
            'userInvoiceReturn'                 => $userInvoiceReturn ,
            'searchForm' => $data,

        ));

    }

    public function salesSummaryPdfAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        if (!empty($data['date'])) {
            $datetime = new \DateTime($data['date']);
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }

        $salesTotalTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user, $data);
        $salesTodayTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user, $data, 'false', array('diagnostic', 'admission'));
        $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user, $data, 'true', array('diagnostic', 'admission'));
        $diagnosticOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'diagnostic');
        $admissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'admission');
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user, $data);
        $transactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->findWithTransactionOverview($user, $data);
        $commissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithCommissionOverview($user, $data);

        $salesTodayUser      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesUsers($user);
        $salesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'false',array('diagnostic','admission','visit'));
        $previousSalesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'true',array('diagnostic','admission','visit'));
        $userSalesTodaySalesCommission      = $em->getRepository('HospitalBundle:DoctorInvoice')->userGroupCommissionSummary($user,$data);
        $userInvoiceReturn   = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->userGroupInvoiceReturnAmount($user,$data);


        $html = $this->renderView(
            'HospitalBundle:Report:salesSummaryPdf.html.twig', array(
            'salesTotalTransactionOverview' => $salesTotalTransactionOverview,
            'salesTodayTransactionOverview' => $salesTodayTransactionOverview,
            'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'diagnosticOverview' => $diagnosticOverview,
            'admissionOverview' => $admissionOverview,
            'serviceOverview' => $serviceOverview,
            'transactionOverview' => $transactionOverview,
            'commissionOverview' => $commissionOverview,
            'salesTodayUserTransactionOverview'     => $salesTodayUserTransactionOverview,
            'previousSalesTodayUserTransactionOverview'     => $previousSalesTodayUserTransactionOverview,
            'userSalesTodaySalesCommission'     => $userSalesTodaySalesCommission,
            'userInvoiceReturn'                 => $userInvoiceReturn ,
            'globalOption' => $user->getGlobalOption(),
            'searchForm' => $data,

        ));
        $date = isset($data['date'])? $data['date']:date('d-m-Y');
        $date = "{$date}-daily-sales.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');

    }

    public function serviceBaseSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $entity = '';
        if (!empty($data) and $data['service']) {
            $entity = $em->getRepository('HospitalBundle:Service')->find($data['service']);
        }
        $services = $em->getRepository('HospitalBundle:Service')->findBy(array(), array('name' => 'ASC'));
        $serviceGroup = $em->getRepository('HospitalBundle:InvoiceParticular')->serviceParticularDetails($user, $data);
        return $this->render('HospitalBundle:Report:serviceBaseSales.html.twig', array(
            'serviceGroup' => $serviceGroup,
            'services' => $services,
            'entity' => $entity,
            'searchForm' => $data,
        ));
    }

    public function serviceBaseSummaryPdfAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entity = '';
        if (!empty($data) and $data['service']) {
            $entity = $em->getRepository('HospitalBundle:Service')->find($data['service']);
        }
        $services = $em->getRepository('HospitalBundle:Service')->findBy(array(), array('name' => 'ASC'));
        $serviceGroup = $em->getRepository('HospitalBundle:InvoiceParticular')->serviceParticularDetails($user, $data);
        $html = $this->renderView(
            'HospitalBundle:Report:serviceBaseSalesPdf.html.twig', array(
            'serviceGroup' => $serviceGroup,
            'services' => $services,
            'entity' => $entity,
            'globalOption' => $user->getGlobalOption(),
            'searchForm' => $data,
        ));

        $date = isset($data['startDate'])? $data['startDate']:date('d-m-Y');
        $date = "{$date}-service-base-sales.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');
    }

    public function salesDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        if (empty($data['posted'])) {
            $datetime = new \DateTime("now");
            $data['posted'] = $datetime->format('d-m-Y');
        }
        $salesTodayUser      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesUsers($user);
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceDetailReporets($user, $mode = 'diagnostic', $data);
        $pagination = $entities->getQuery()->getResult();
        $commissionSummary = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommissionSummary($hospital, $entities);
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommission($hospital, $entities);
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital, array(5, 6));
        return $this->render('HospitalBundle:Report/Sales:details.html.twig', array(
            'entities' => $pagination,
            'commissions' => $commissions,
            'commissionSummary' => $commissionSummary,
            'referredDoctors' => $referredDoctors,
            'salesTodayUser' => $salesTodayUser,
            'searchForm' => $data,
        ));
    }

    public function salesDetailsPdfAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $datetime = new \DateTime("now");
        if (empty($data['posted'])) {
            $data['posted'] = $datetime->format('Y-m-d');
        }

        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceDetailReporets($user, $mode = 'diagnostic', $data);
        $pagination = $entities->getQuery()->getResult();
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommission($hospital, $entities);
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital, array(6));
        $commissionSummary = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommissionSummary($hospital, $entities);
        $html = $this->renderView(
            'HospitalBundle:Report/Sales:detailsPdf.html.twig', array(
                'globalOption' => $user->getGlobalOption(),
                'entities' => $pagination,
                'commissionSummary' => $commissionSummary,
                'commissions' => $commissions,
                'referredDoctors' => $referredDoctors,
                'searchForm' => $data,
            )
        );
        $date = $datetime->format('d-m-y');
        $date = "{$date}-daily-sales.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');
    }

    public function salesDetailsDiscountAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        if (empty($data['posted'])) {
            $datetime = new \DateTime("now");
            $data['posted'] = $datetime->format('d-m-Y');
        }
        $salesTodayUser      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesUsers($user);
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceDetailReporets($user, $mode = 'diagnostic', $data);
        $pagination = $entities->getQuery()->getResult();
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital, array(5, 6));
        return $this->render('HospitalBundle:Report/sales-discount:details.html.twig', array(
            'entities' => $pagination,
            'referredDoctors' => $referredDoctors,
            'salesTodayUser' => $salesTodayUser,
            'searchForm' => $data,
        ));
    }

    public function salesDetailsDiscountPdfAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $datetime = new \DateTime("now");
        if (empty($data['posted'])) {
            $data['posted'] = $datetime->format('Y-m-d');
        }
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceDetailReporets($user, $mode = 'diagnostic', $data);
        $pagination = $entities->getQuery()->getResult();
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital, array(6));
        $html = $this->renderView(
            'HospitalBundle:Report/sales-discount:detailsPdf.html.twig', array(
                'globalOption' => $user->getGlobalOption(),
                'entities' => $pagination,
                'referredDoctors' => $referredDoctors,
                'searchForm' => $data,
            )
        );
        $date = $datetime->format('d-m-y');
        $date = "{$date}-daily-sales.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');
    }

    public function monthlyCashAction()
    {
        $user = $this->getUser();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $monthlySales = $em->getRepository('HospitalBundle:InvoiceTransaction')->monthlySales($user, $data);
        $monthlyCommissionSales = $em->getRepository('HospitalBundle:DoctorInvoice')->monthlyCommission($user, $data);
        $monthlySalesReturns = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->monthlySalesReturn($user, $data);
        $monthlyExpenditures = $em->getRepository('AccountingBundle:Expenditure')->monthlyExpenditure($user, $data);
        return $this->render('HospitalBundle:Report:monthlyCash.html.twig', array(
            'monthlySales' => $monthlySales,
            'monthlyCommissionSales' => $monthlyCommissionSales,
            'monthlySalesReturns' => $monthlySalesReturns,
            'monthlyExpenditures' => $monthlyExpenditures,
            'searchForm' => $data
        ));
    }

    public function monthlyCashPdfAction()
    {
        $user = $this->getUser();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        if (empty($data['month']) and empty($data['year'])) {
            $data = array();
        }
        $monthlySales = $em->getRepository('HospitalBundle:InvoiceTransaction')->monthlySales($user, $data);
        $monthlyCommissionSales = $em->getRepository('HospitalBundle:DoctorInvoice')->monthlyCommission($user, $data);
        $monthlySalesReturns = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->monthlySalesReturn($user, $data);
        $monthlyExpenditures = $em->getRepository('AccountingBundle:Expenditure')->monthlyExpenditure($user, $data);
        $html = $this->renderView(
            'HospitalBundle:Report:monthlyCashPdf.html.twig', array(
                'globalOption' => $user->getGlobalOption(),
                'monthlySales' => $monthlySales,
                'monthlyCommissionSales' => $monthlyCommissionSales,
                'monthlySalesReturns' => $monthlySalesReturns,
                'monthlyExpenditures' => $monthlyExpenditures,
                'searchForm' => $data,
            )
        );
        $datetime = new \DateTime("now");
        $monthYear = $datetime->format('m-Y');
        $month = !empty($data['month']) ? $data['month'] : '';
        $year = !empty($data['year']) ? $data['year'] : '';
        if (!empty($month) and !empty($year)) {
            $monthYear = $month . '-' . $year;
        }
        $date = 'monthly-statement-' . $monthYear;
        $date = "{$date}.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');

    }

    public function monthlyCommissionAction()
    {
        $user = $this->getUser();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyCommissionGroup($user, $data);
        $commissionEntity = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyGroupBaseCommissionSummary($user, $data);
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($user->getGlobalOption()->getHospitalConfig(), array(5, 6));
        return $this->render('HospitalBundle:Report:monthlyCommission.html.twig', array(
            'referredDoctors' => $referredDoctors,
            'commissions' => $commissions,
            'commissionEntity' => $commissionEntity,
            'searchForm' => $data
        ));
    }

    public function monthlyCommissionPdfAction()
    {
        $user = $this->getUser();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        if (empty($data['month']) and empty($data['year'])) {
            $data = array();
        }
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyCommissionGroup($user, $data);
        $commissionEntity = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyGroupBaseCommissionSummary($user, $data);
        $referred = "";
        if(!empty($data['referred'])){
            $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->find($data['referred']);
        }
        $html = $this->renderView(
            'HospitalBundle:Report:monthlyCommissionPdf.html.twig', array(
                'globalOption' => $user->getGlobalOption(),
                'referredEntity' => $referred,
                'commissions' => $commissions,
                'commissionEntity' => $commissionEntity,
                'searchForm' => $data
            )
        );
        $datetime = new \DateTime("now");
        $monthYear = $datetime->format('m-Y');
        $month = !empty($data['month']) ? $data['month'] : '';
        $year = !empty($data['year']) ? $data['year'] : '';
        if (!empty($month) and !empty($year)) {
            $monthYear = $month . '-' . $year;
        }
        $date = 'monthly-commission-statement-' . $monthYear;
        $date = "{$date}.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');
    }

    public function monthlyCommissionDetailsAction()
    {
        $user = $this->getUser();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyCommissionDetails($user, $data);
        $monthlyReferredCommissionInvoice = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyReferredCommissionInvoice($user, $data);
        $commissionSummary = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyGroupBaseCommissionSummary($user, $data);
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($user->getGlobalOption()->getHospitalConfig(), array(5, 6));
        return $this->render('HospitalBundle:Report:monthlyCommissionDetails.html.twig', array(
            'referredInvoice' => $monthlyReferredCommissionInvoice,
            'referredDoctors' => $referredDoctors,
            'commissions' => $commissions,
            'commissionSummary' => $commissionSummary,
            'searchForm' => $data
        ));
    }

    public function monthlyCommissionDetailsPdfAction()
    {
        $user = $this->getUser();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyCommissionDetails($user, $data);
        $monthlyReferredCommissionInvoice = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyReferredCommissionInvoice($user, $data);
        $commissionSummary = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyGroupBaseCommissionSummary($user, $data);
        $referred = "";
        if(!empty($data['referred'])){
            $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->find($data['referred']);
        }
        $html = $this->renderView(
            'HospitalBundle:Report:monthlyCommissionDetailsPdf.html.twig', array(
            'referredInvoice' => $monthlyReferredCommissionInvoice,
            'referredEntity' => $referred,
            'globalOption' => $user->getGlobalOption(),
            'commissions' => $commissions,
            'commissionSummary' => $commissionSummary,
            'searchForm' => $data
        ));

        $datetime = new \DateTime("now");
        $monthYear = $datetime->format('m-Y');
        $month = !empty($data['month']) ? $data['month'] : '';
        $year = !empty($data['year']) ? $data['year'] : '';
        if (!empty($month) and !empty($year)) {
            $monthYear = $month . '-' . $year;
        }
        $date = 'monthly-commission-statement-' . $monthYear;
        $date = "{$date}.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');
    }

    public function commissionGroupAction()
    {
        $user = $this->getUser();
        $data = $_REQUEST;
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->commissionGroup($user, $data);
        $referreds = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->commissionReferred($user, $data);
        return $this->render('HospitalBundle:Report:commissionSummary.html.twig', array(
            'commissions' => $commissions,
            'referreds' => $referreds,
            'searchForm' => $data
        ));
    }

    public function commissionGroupPdfAction()
    {
        $user = $this->getUser();
        $data = $_REQUEST;
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->commissionGroup($user, $data);
        $referreds = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->commissionReferred($user, $data);
        $html = $this->renderView('HospitalBundle:Report:commissionSummaryPdf.html.twig', array(
            'globalOption' => $user->getGlobalOption(),
            'commissions' => $commissions,
            'referreds' => $referreds,
            'searchForm' => $data
        ));
        $datetime = new \DateTime("now");
        $monthYear = $datetime->format('d-m-Y');
        $date = 'commission-summary-report'. $monthYear;
        $date = "{$date}.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');
    }
}

