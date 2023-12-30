<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {

        /* @var GlobalOption $globalOption */

        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine();
        $data = $_REQUEST;
        $datetime = new \DateTime("now");
        $data['startDate'] = $datetime->format('Y-m-d');
        $data['endDate'] = $datetime->format('Y-m-d');
        $user = $this->getUser();
        $salesCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->userBaseSalesOverview($user,$data,array('diagnostic','visit'));
        $salesCashAdmission = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data,array('admission'));
        $purchaseCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($user,$data);
        $transactionMethods                 = array(1);
        $transactionCashOverview            = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
        $expenditureOverview                = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);

        $salesTodayTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserSalesOverview($user,$data,'false',array('diagnostic','admission','visit'));
        $previousSalesTransactionOverview   = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserSalesOverview($user,$data,'true',array('diagnostic','admission','visit'));

        $salesTodaySalesCommission          = $em->getRepository('HospitalBundle:DoctorInvoice')->commissionUserSummary($user,$data);
        $invoiceReturn   = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->userInvoiceReturnAmount($user,$data);


        $salesTodayUser      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesUsers($user);
        $salesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'false',array('diagnostic','admission','visit'));
        $previousSalesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'true',array('diagnostic','admission','visit'));
        $userSalesTodaySalesCommission      = $em->getRepository('HospitalBundle:DoctorInvoice')->userGroupCommissionSummary($user,$data);
        $userInvoiceReturn   = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->userGroupInvoiceReturnAmount($user,$data);
        $hospital = $globalOption->getHospitalConfig()->getId();
        $diagonesticProcesses = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAdmissionProcess($hospital,'diagnostic',$data);
        $admissionProcesses = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAdmissionProcess($hospital,'admission',$data);
        $visitProcesses = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAdmissionProcess($hospital,'visit',$data);

        return $this->render('HospitalBundle:Default:dashboard.html.twig', array(
            'option'                            => $user->getGlobalOption() ,
            'globalOption'                      => $globalOption,
            'salesTodayUsers'                   => $salesTodayUser,
            'salesTodayTransactionOverview'     => $salesTodayTransactionOverview,
            'previousSalesTransactionOverview'  => $previousSalesTransactionOverview,
            'salesTodayUserTransactionOverview'     => $salesTodayUserTransactionOverview,
            'previousSalesTodayUserTransactionOverview'     => $previousSalesTodayUserTransactionOverview,
            'salesTodaySalesCommission'         => $salesTodaySalesCommission,
            'userSalesTodaySalesCommission'     => $userSalesTodaySalesCommission,
            'transactionCashOverviews'          => $transactionCashOverview,
            'expenditureOverview'               => $expenditureOverview ,
            'salesCashOverview'                 => $salesCashOverview ,
            'salesCashAdmission'                => $salesCashAdmission ,
            'invoiceReturn'                     => $invoiceReturn ,
            'userInvoiceReturn'                 => $userInvoiceReturn ,
            'purchaseCashOverview'              => $purchaseCashOverview ,
            'diagonesticProcesses'              => $diagonesticProcesses ,
            'admissionProcesses'                => $admissionProcesses ,
            'visitProcesses'                    => $visitProcesses ,
            'searchForm'                        => $data ,
        ));

    }


    public function getBarcode($value)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($value);
        $barcode->setType(BarcodeGenerator::Code39Extended);
        $barcode->setScale(1);
        $barcode->setThickness(25);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,'.$code .'" />';
        return $data;
    }

    /**
     * @Secure(roles="ROLE_HOSPITAL,ROLE_DOMAIN");
     */
    public function idcardAction(Customer $customer)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $barcode = $this->getBarcode($customer->getCustomerId());
        return $this->render('HospitalBundle:Default:patient-idcard.html.twig', array(
            'hospital' => $hospital,
            'entity' => $customer,
            'barcode' => $barcode
        ));
    }


}
