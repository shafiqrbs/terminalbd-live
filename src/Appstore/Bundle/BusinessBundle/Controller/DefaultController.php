<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Knp\Snappy\Pdf;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();

        /* @var GlobalOption $globalOption */

        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
	    $data = $_REQUEST;
	    $datetime = new \DateTime("now");
	    $data['startDate'] = $datetime->format('Y-m-d');
	    $data['endDate'] = $datetime->format('Y-m-d');

        $user = $this->getUser();
        $salesCashOverview = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesOverview($user,$data);
        $purchaseCashOverview = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseOverview($user,$data);
	    $transactionMethods = array(1);
	    $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
	    $expenditureOverview = $em->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);

	    $startMonthDate = $datetime->format('Y-m-01 00:00:00');
	    $endMonthDate = $datetime->format('Y-m-t 23:59:59');
	    $monthlySales = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->monthlySales($user,$data = array( 'startDate' =>$startMonthDate, 'endDate' =>$endMonthDate));
	    $monthlyPurchase = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->monthlyPurchase($user,$data = array('startDate' => $startMonthDate,'endDate' => $endMonthDate));

	    $monthlySalesArr = array();
	    foreach($monthlySales as $row) {
		    $monthlySalesArr[$row['month']] = $row['total'];
	    }
	    $monthlyPurchaseArr = array();
	    foreach($monthlyPurchase as $row) {
		    $monthlyPurchaseArr[$row['month']] = $row['total'];
	    }
        $config = $globalOption->getBusinessConfig();
        $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'index';
        return $this->render("BusinessBundle:Default:index.html.twig", array(
            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'transactionCashOverviews'  => $transactionCashOverview,
            'expenditureOverview'       => $expenditureOverview ,
            'salesCashOverview'         => $salesCashOverview ,
            'purchaseCashOverview'      => $purchaseCashOverview ,
            'monthlyPurchase'           => $monthlyPurchaseArr ,
            'monthlySales'              => $monthlySalesArr ,
            'searchForm'                => $data ,
        ));
    }

    public function pdfTodaySalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();
        $salesTotalTransactionOverview = $em->getRepository('BusinessBundle:BusinessTreatmentPlan')->transactionOverview($config,$data);
        $html = $this->renderView(
            'BusinessBundle:Default:today-sales-overview.html.twig', array(
                'salesOverview' => $salesTotalTransactionOverview,
                'previousSalesTransactionOverview' => '',
                'assignDoctors' => '',
                'searchForm' => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='sales-overview-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
    }

    public function copyToBusinessParticularAction(GlobalOption $option)
    {
        $em = $this->getDoctrine()->getManager();
        set_time_limit(0);
        ignore_user_abort(true);
        $config = $option->getMedicineConfig();

        $globalOption = $this->getUser()->getGlobalOption();
        $existConfig = $globalOption->getBusinessConfig();

        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findBy(array('businessConfig'=> $config));

        /* @var $entity BusinessParticular */

        foreach ($entities as $entity){

            $newEntity = new BusinessParticular();
            $newEntity->setBusinessConfig($existConfig);
            $newEntity->setName($entity->getName());
            $newEntity->setStatus(1);
            $newEntity->setBusinessParticularType($entity->getBusinessParticularType());
            $newEntity->setCategory($entity->getCategory());
            $newEntity->setSlug($entity->getSlug());
            $newEntity->setUnit($entity->getUnit());
            $em->persist($newEntity);
            $em->flush();

        }
        return $this->redirect($this->generateUrl('homepage'));

    }

    public function updateRemainingQuantityAction(){

        set_time_limit(0);
        ignore_user_abort(true);
        $globalOption = $this->getUser()->getGlobalOption();
        $config = $globalOption->getBusinessConfig();
        $this->getDoctrine()->getRepository(BusinessParticular::class)->updateBusinessRemainingQuantityReset($config->getId());
        return $this->redirect($this->generateUrl('business_stock'));
    }




}
