<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
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
        return $pagination;
    }

    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_DOMAIN_RESTAURANT_MANAGER,ROLE_DOMAIN")
     */

    public function salesSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $salesTotalTransactionOverview = $em->getRepository('RestaurantBundle:Invoice')->todaySalesOverview($user,$data);
        $salesTransactionOverview = $em->getRepository('RestaurantBundle:Invoice')->todaySalesOverview($user,$data,'true');
        $previousSalesTransactionOverview = $em->getRepository('RestaurantBundle:Invoice')->todaySalesOverview($user,$data,'false');
        $transactionOverview = $em->getRepository('RestaurantBundle:Invoice')->transactionBaseOverview($user,$data);
        $userSalesOverview = $em->getRepository('RestaurantBundle:Invoice')->userSalesOverview($user,$data);
        $categoryOverview = $em->getRepository('RestaurantBundle:InvoiceParticular')->findWithCategoryOverview($user,$data);
        $productGroupOverview = $em->getRepository('RestaurantBundle:InvoiceParticular')->findWithProductGroupOverview($user,$data);
        $productOverview = $em->getRepository('RestaurantBundle:InvoiceParticular')->findWithProductOverview($user,$data);


        if(empty($data['pdf'])){

            return $this->render('RestaurantBundle:Report:salesSummary.html.twig', array(

                'salesTotalTransactionOverview'      => $salesTotalTransactionOverview,
                'salesTransactionOverview'      => $salesTransactionOverview,
                'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
                'transactionOverview' => $transactionOverview,
                'userSalesOverview'            => $userSalesOverview,
                'categoryOverview'            => $categoryOverview,
                'productOverview'             => $productOverview,
                'productGroupOverview'             => $productGroupOverview,
                'searchForm'                    => $data,

            ));

        }else{

            $html = $this->renderView(
                'RestaurantBundle:Report:salesSummaryPdf.html.twig', array(
                    'option' => $user->getGlobalOption(),
                    'salesTotalTransactionOverview'      => $salesTotalTransactionOverview,
                    'salesTransactionOverview'      => $salesTransactionOverview,
                    'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
                    'transactionOverview' => $transactionOverview,
                    'categoryOverview'            => $categoryOverview,
                    'productOverview'             => $productOverview,
                    'productGroupOverview'             => $productGroupOverview,
                    'searchForm'                    => $data,
                )
            );
            $this->downloadPdf($html,'sales-summary.pdf');
        }

    }
    public function userBaseSalesSummaryAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $userSalesOverview = $em->getRepository('RestaurantBundle:Invoice')->userSalesOverview($user,$data);

        if(empty($data['pdf'])){

            return $this->render('RestaurantBundle:Report:userSummary.html.twig', array(
                'userSalesOverview'          => $userSalesOverview,
                'searchForm'            => $data,
            ));

        }else{

            $html = $this->renderView(
                'RestaurantBundle:Report:userSummaryPdf.html.twig', array(
                    'option' => $user->getGlobalOption(),
                    'userSalesOverview'          => $userSalesOverview,
                    'searchForm'            => $data,
                )
            );
            $this->downloadPdf($html,'user-sales-summary.pdf');
        }

    }

    public function categoryBaseSalesSummaryAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $categoryOverview = $em->getRepository('RestaurantBundle:InvoiceParticular')->findWithCategoryOverview($user,$data);
        if(empty($data['pdf'])){
            return $this->render('RestaurantBundle:Report:categorySummary.html.twig', array(
                'categoryOverview'          => $categoryOverview,
                'searchForm'            => $data,
            ));
        }else{
            $html = $this->renderView(
                'RestaurantBundle:Report:categorySummaryPdf.html.twig', array(
                    'option' => $user->getGlobalOption(),
                    'categoryOverview'          => $categoryOverview,
                    'searchForm'            => $data,
                )
            );
            $this->downloadPdf($html,'category-sales-summary.pdf');
        }

    }

    public function productGroupBaseSalesSummaryAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $categoryOverview = $em->getRepository('RestaurantBundle:InvoiceParticular')->findWithProductGroupOverview($user,$data);
        if(empty($data['pdf'])){
            return $this->render('RestaurantBundle:Report:ProductGroupSummaryPdf.html.twig', array(
                'categoryOverview'          => $categoryOverview,
                'searchForm'            => $data,
            ));
        }else{
            $html = $this->renderView(
                'RestaurantBundle:Report:ProductGroupSummaryPdf.html.twig', array(
                    'option' => $user->getGlobalOption(),
                    'categoryOverview'          => $categoryOverview,
                    'searchForm'            => $data,
                )
            );
            $this->downloadPdf($html,'group-sales-summary.pdf');
        }

    }

    public function productBaseSalesSummaryAction()
    {

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $productOverview = $em->getRepository('RestaurantBundle:InvoiceParticular')->findWithProductOverview($user,$data);
        if(empty($data['pdf'])){
            return $this->render('RestaurantBundle:Report:productSummary.html.twig', array(
                'productOverview'          => $productOverview,
                'searchForm'            => $data,
            ));
        }else{
            $html = $this->renderView(
                'RestaurantBundle:Report:productSummaryPdf.html.twig', array(
                    'option' => $user->getGlobalOption(),
                    'productOverview'          => $productOverview,
                    'searchForm'            => $data,
                )
            );
            $this->downloadPdf($html,'product-sales-summary.pdf');
        }

    }

    public function salesDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('RestaurantBundle:Invoice')->salesReport($user, $data);
        $pagination = $this->paginate($entities);
        $purchaseSalesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user, $data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        $banks = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption' => $user->getGlobalOption(),'status' => 1), array('name' => 'ASC'));
        $mobiles =  $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption' => $user->getGlobalOption() , 'status' => 1), array('name' => 'ASC'));

        $cashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user, $data);
        return $this->render('RestaurantBundle:Report:sales/sales.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $pagination,
            'banks' => $banks,
            'mobiles' => $mobiles,
            'cashOverview' => $cashOverview,
            'purchaseSalesPrice' => $purchaseSalesPrice,
            'transactionMethods' => $transactionMethods,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));
    }

    public function downloadPdf($html,$fileName = '')
    {
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename={$fileName}");
        echo $pdf;
        return new Response('');
    }

}

