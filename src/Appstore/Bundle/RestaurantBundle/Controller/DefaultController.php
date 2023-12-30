<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $option = $user->getGlobalOption();
        $data = $_REQUEST;
        $datetime = new \DateTime("now");
        $data['startDate'] = $datetime->format('Y-m-d');
        $data['endDate'] = $datetime->format('Y-m-d');
        $income = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMedicineIncome($this->getUser(),$data);
        $user = $this->getUser();
        $salesCashOverview                  = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data,array('restaurant'));
        $purchaseCashOverview               = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($user,$data);
        $transactionMethods                 = array(1);
        $transactionCashOverview            = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
        $expenditureOverview                = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
        $salesOverview                      = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->findWithSalesOverview($this->getUser(),$data);
        $salesTransactionOverview           = $em->getRepository('RestaurantBundle:Invoice')->todaySalesOverview($user,$data,'true');
        $previousSalesTransactionOverview   = $em->getRepository('RestaurantBundle:Invoice')->todaySalesOverview($user,$data,'false');

        return $this->render('RestaurantBundle:Default:dashboard.html.twig', array(
            'config'                            => $option->getRestaurantConfig(),
            'salesOverview'                     => $salesOverview,
            'salesTransactionOverview'          => $salesTransactionOverview,
            'previousSalesTransactionOverview'  => $previousSalesTransactionOverview,
            'option'                            => $user->getGlobalOption() ,
            'transactionCashOverview'           => $transactionCashOverview,
            'expenditureOverview'               => $expenditureOverview ,
            'salesCashOverview'                 => $salesCashOverview ,
            'purchaseCashOverview'              => $purchaseCashOverview ,
            'searchForm'                        => $data ,
        ));

    }

}
