<?php

namespace Terminalbd\ReportBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturnItem;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturnInvoice;
use Core\UserBundle\Entity\User;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mis")
 * @Security("is_granted('ROLE_DOMAIN') or is_granted('ROLE_REPORT') or is_granted('ROLE_REPORT_ADMIN')")
 */
class ReportController extends Controller
{


    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * @Route("/dashboard", methods={"GET", "POST"}, name="report_dashboard")
     * @Secure(roles="ROLE_REPORT,ROLE_DOMAIN")
     */
    public function indexAction()
    {

        $data = $_REQUEST;
        if(empty($data)){
            $date = new \DateTime("now");
            $start = $date->format('d-m-Y');
            $end = $date->format('d-m-Y');
            $data = array('startDate'=> $start , 'endDate' => $end);
        }
        $todayCustomerSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->dailySalesReceive($this->getUser(),$data);
        $todayVendorSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->dailyPurchasePayment($this->getUser(),$data);
        $todayExpense = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->dailyPurchasePayment($this->getUser(),$data);
        $todayJournal = $this->getDoctrine()->getRepository('AccountingBundle:AccountJournalItem')->dailyJournal($this->getUser(),$data);
        $todayLoan = $this->getDoctrine()->getRepository('AccountingBundle:AccountLoan')->dailyLoan($this->getUser(),$data);
        $transactionMethods = array(1,4);
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionWiseOverview( $this->getUser(),$data);
        $transactionBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionBankCashOverview( $this->getUser(),$data);
        $transactionMobileBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionMobileBankCashOverview( $this->getUser(),$data);
        $transactionAccountHeadCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionAccountHeadCashOverview( $this->getUser(),$data);
        $employees = $this->getDoctrine()->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Default:index.html.twig', array(
            'transactionCashOverviews'                  => $transactionCashOverview,
            'transactionBankCashOverviews'              => $transactionBankCashOverviews,
            'transactionMobileBankCashOverviews'        => $transactionMobileBankCashOverviews,
            'transactionAccountHeadCashOverviews'       => $transactionAccountHeadCashOverviews,
            'todayCustomerSales'       => $todayCustomerSales,
            'todayVendorSales'       => $todayVendorSales,
            'todayExpense'       => $todayExpense,
            'todayJournal'       => $todayJournal,
            'todayLoan'       => $todayLoan,
            'employees'       => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));

    }

    /**
     * @Route("/monthly-statement", methods={"GET", "POST"}, name="accounting_report_monthly_statement")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */
    public function monthlyStatementAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $ajaxPath = $this->generateUrl('accounting_report_monthly_statement_ajax');
        return $this->render('ReportBundle:Accounting/Financial:monthly-statement.html.twig', array(
            'option' => $globalOption,
            'ajaxPath' => $ajaxPath,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/monthly-statement-ajax", methods={"GET", "POST"}, name="accounting_report_monthly_statement_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function monthlyStatementAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        if(isset($data['month']) and $data['month'] and isset($data['year']) and $data['year'] ) {
            $user = $this->getUser();
            if(empty($data)){
                $compare = new \DateTime();
                $end =  $compare->format('j');
                $data['monthYear'] = $compare->format('Y-m-d');
                $data['month'] =  $compare->format('F');
                $data['year'] = $compare->format('Y');
            }else{
                $month = $data['month'];
                $year = $data['year'];
                $compare = new \DateTime("{$year}-{$month}-01");
                $end =  $compare->format('t');
                $data['monthYear'] = $compare->format('Y-m-d');
            }
            $openingBalance = [];
            for ($i = 1; $end >= $i ; $i++ ){
                $no = sprintf("%s", str_pad($i,2, '0', STR_PAD_LEFT));
                $start =  $compare->format("Y-m-{$no}");
                $day =  $compare->format("{$no}-m-Y");
                $data['startDate'] = $start;
                $openingBalance[$day] = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->openingBalanceGroup($user,'',$data);
            }
            $sales = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Sales',$data);
            $purchase = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Purchase',$data);
            $purchaseCommission = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Purchase-Commission',$data);
            $expenditure = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Expenditure',$data);
            $journal = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Journal',$data);

            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Financial:monthly-statement-ajax.html.twig', array(
                    'searchForm' => $data,
                    'globalOption'                  => $this->getUser()->getGlobalOption(),
                    'openingBalanceTrans'           => $openingBalance,
                    'salesTrans'                    => $sales,
                    'purchaseTrans'                 => $purchase,
                    'purchaseCommissionTrans'       => $purchaseCommission,
                    'expenditureTrans'              => $expenditure,
                    'journalTrans'                  => $journal,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }


    /**
     * @Route("/customer-outstanding", methods={"GET", "POST"}, name="accounting_report_sales_outstanding")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */
    public function customerOutstandingAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption,$data);
        return $this->render('ReportBundle:Accounting/Sales:customerOutstanding.html.twig', array(
            'option' => $globalOption,
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }


    /**
     * @Route("/customer-summary", methods={"GET", "POST"}, name="accounting_report_sales_summary")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function customerSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSummary($globalOption,$data);
        return $this->render('ReportBundle:Accounting/Sales:customerSummary.html.twig', array(
            'entities' => $entities,
            'option' => $globalOption,
            'searchForm' => $data,
        ));

    }

    /**
     * @Route("/cash-flow", methods={"GET", "POST"}, name="accounting_report_cash_flow")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function cashFlowAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/balanch-sheet", methods={"GET", "POST"}, name="accounting_report_balanch_sheet")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function balanchSheetAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/trail-balanch", methods={"GET", "POST"}, name="accounting_report_trail_balanch")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function trailBalanchAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/account-head", methods={"GET", "POST"}, name="accounting_report_head")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function journalHeadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());
        return $this->render('ReportBundle:Accounting/Financial:account-head.html.twig', array(
            'accountHead' => $accountHead,
            'heads' => $heads,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/account-head-ajax", methods={"GET", "POST"}, name="accounting_report_head_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function journalHeadAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = "";
        if(isset($data['startDate']) and $data['startDate'] and isset($data['endDate']) and $data['endDate'] ) {
            $entities = $em->getRepository(Transaction::class)->reportAccountHead($globalOption,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Financial:account-head-ajax.html.twig', array(
                    'entities' => $entities,
                )
            );
            return new Response($htmlProcess);
        }
        return new Response('Record Does not found');
    }

   /**
     * @Route("/account-head-subhead", methods={"GET", "POST"}, name="accounting_report_subhead")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function journalSubheadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());
        return $this->render('ReportBundle:Accounting/Financial:account-subhead.html.twig', array(
            'accountHead' => $accountHead,
            'heads' => $heads,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/account-subhead-ajax", methods={"GET", "POST"}, name="accounting_report_subhead_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function journalSubHeadAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $entities = "";
        if(isset($data['startDate']) and $data['startDate'] and isset($data['endDate']) and $data['endDate'] ) {
            $entities = $em->getRepository(Transaction::class)->reportAccountHead($globalOption,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Financial:account-head-ajax.html.twig', array(
                    'entities' => $entities,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }

    /**
     * @Route("/daily-profit", methods={"GET", "POST"}, name="accounting_report_daily_profit")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function dailyProfitAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $ajaxPath = $this->generateUrl('accounting_report_daily_profit_ajax');
        return $this->render('ReportBundle:Accounting/Financial:income.html.twig', array(
            'option' => $globalOption,
            'ajaxPath' => $ajaxPath,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/daily-profit-ajax", methods={"GET", "POST"}, name="accounting_report_daily_profit_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function dailyProfitAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        if(isset($data['startDate']) and $data['startDate'] and isset($data['endDate']) and $data['endDate'] ) {
            $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportSalesIncome($this->getUser(),$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Financial:income-ajax.html.twig', array(
                    'overview' => $overview,
                    'searchForm' => $data,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }

    /**
     * @Route("/monthly-profit", methods={"GET", "POST"}, name="accounting_report_monthly_profit")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function monthlyProfitAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/yearly-profit", methods={"GET", "POST"}, name="accounting_report_yearly_profit")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function yearlyProfitAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/stakeholder-profit", methods={"GET", "POST"}, name="accounting_report_stakeholder_profit")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function stakeholderProfitAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $accountSubHeads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('globalOption' => $option),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());

        return $this->render('ReportBundle:Accounting/Sales:ledger.html.twig', array(
            'accountHead' => $accountHead,
            'accountSubHeads' => $accountSubHeads,
            'heads' => $heads,
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * @Route("/customer-ledger", methods={"GET", "POST"}, name="accounting_report_sales_customer_ledger")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function customerLedgerAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = '';
        $customer = "";
        $overview = "";
        $customers = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->customerOutstanding($globalOption);
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['customerId']) and $data['customerId']) {
            $customerId = $data['customerId'];
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'id'=> $customerId));
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportCustomerLedger($globalOption->getId(),$data);
            $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($this->getUser(),$data);

        }
        return $this->render('ReportBundle:Accounting/Sales:ledger.html.twig', array(
                'entities' => $entities,
                'overview' => $overview,
                'customer' => $customer,
                'customers' => $customers,
                'option' => $globalOption,
                'searchForm' => $data,
        ));

    }

    /**
     * @Route("/user-sales-receive", methods={"GET", "POST"}, name="accounting_report_sales_user_summary")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function userSummaryAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->userSummary($globalOption,$data);
        return $this->render('ReportBundle:Accounting/Sales:userSummary.html.twig', array(
                'entities' => $entities,
                'option' => $globalOption,
                'searchForm' => $data,
        ));

    }

    /**
     * @Route("/customer-sales-receive", methods={"GET", "POST"}, name="accounting_report_sales_customer_summary")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function userSalesDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['user']) and $data['user']) {
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportCustomerDetails($globalOption,$data);
        }
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:customerDetails.html.twig', array(
            'entities' => $entities,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/sales-details", methods={"GET", "POST"}, name="accounting_report_sales_details")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

   public function salesDetailsAction()
   {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $customers = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getSalesCustomers($globalOption);
        $groups = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getProcessModes($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:sales.html.twig', array(
            'entities' => $entities,
            'transactionMethods' => $transactionMethods,
            'groups' => $groups,
            'users' => $users,
            'customers' => $customers,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
   }

    /**
     * @Route("/sales-details-load", methods={"GET", "POST"}, name="accounting_report_sales_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function salesDetailsLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        $salesBy = "";
        if(isset($data['startDateTime']) and $data['startDateTime'] and isset($data['endDateTime']) and $data['endDateTime'] ) {
            $entities = $em->getRepository('AccountingBundle:AccountSales')->reportFindWithSearch($globalOption,$data);
            if(isset($data['user']) and !empty($data['user'])){
                $salesBy = $this->getDoctrine()->getRepository(User::class)->find($data['user']);
            }
            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Sales:sales-data.html.twig', array(
                    'entities' => $entities,
                    'data' => $data,
                    'salesBy' => $salesBy,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }




    /**
     * @Route("/purchase-details", methods={"GET", "POST"}, name="accounting_report_purchase_details")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function purchaseDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['user']) and $data['user']) {
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportCustomerDetails($globalOption,$data);
        }
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($globalOption,$data);
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $groups = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->getProcessModes($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:customerDetails.html.twig', array(
            'entities' => $entities,
            'transactionMethods' => $transactionMethods,
            'groups' => $groups,
            'users' => $users,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }


    /**
     * @Route("/hospital-dashboard", methods={"GET", "POST"}, name="hms_report_dashboard")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsDashboardAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (!empty($data['date'])) {
            $datetime = new \DateTime($data['date']);
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }

        $summary = $em->getRepository('HospitalBundle:Invoice')->salesSummary($user, $data);
        $diagnosticOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'diagnostic');
        $admissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'admission');
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user, $data);
        $transactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->findWithTransactionOverview($user, $data);
        $commissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithCommissionOverview($user, $data);
        $salesTodayUser      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesUsers($user,$data);
        $salesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'false',array('diagnostic','admission','visit'));
        $previousSalesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'true',array('diagnostic','admission','visit'));
        $userSalesTodaySalesCommission      = $em->getRepository('HospitalBundle:DoctorInvoice')->userGroupCommissionSummary($user,$data);
        $userInvoiceReturn   = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->userGroupInvoiceReturnAmount($user,$data);
        $salesTodayTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserSalesOverview($user,$data,'false',array('diagnostic','admission','visit'));
        $previousSalesTransactionOverview   = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserSalesOverview($user,$data,'true',array('diagnostic','admission','visit'));
        $salesTodaySalesCommission          = $em->getRepository('HospitalBundle:DoctorInvoice')->commissionUserSummary($user,$data);
        $invoiceReturn   = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->userInvoiceReturnAmount($user,$data);

        $doctorSales   = $em->getRepository('HospitalBundle:Invoice')->assignDoctorInvoice($user,$data);
        $referredSales   = $em->getRepository('HospitalBundle:Invoice')->referredInvoice($user,$data);
        $commissionDoctorSummary   = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->commissionDoctorSummary($user,$data);
        $commissionServicesSummary   = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->commissionServicesSummary($user,$data);


        return $this->render('ReportBundle:Hospital:index.html.twig', array(

            'diagnosticOverview' => $diagnosticOverview,
            'admissionOverview' => $admissionOverview,
            'serviceOverview' => $serviceOverview,
            'transactionOverview' => $transactionOverview,
            'commissionOverview' => $commissionOverview,
            'salesTodayUsers' => $salesTodayUser,
            'salesTodayTransactionOverview'     => $salesTodayTransactionOverview,
            'previousSalesTransactionOverview'  => $previousSalesTransactionOverview,
            'salesTodayUserTransactionOverview'     => $salesTodayUserTransactionOverview,
            'previousSalesTodayUserTransactionOverview'     => $previousSalesTodayUserTransactionOverview,
            'salesTodaySalesCommission'         => $salesTodaySalesCommission,
            'userSalesTodaySalesCommission'     => $userSalesTodaySalesCommission,
            'invoiceReturn'                     => $invoiceReturn ,
            'userInvoiceReturn'                 => $userInvoiceReturn ,
            'doctorSales'                 => $doctorSales ,
            'referredSales'                 => $referredSales ,
            'commissionDoctorSummary'                 => $commissionDoctorSummary ,
            'commissionServicesSummary'                 => $commissionServicesSummary ,
            'summary' => $summary,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/sales-hospital-invoice", methods={"GET", "POST"}, name="hms_report_hospital_invoice")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsSalesInvoiceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $anesthesiaDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssistantDoctor($hospital);
        $departments = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getDepartments($hospital);
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        $processes = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getHospitalProcess($hospital);
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getReferredDoctors($hospital);
        $marketingUsers = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getMarketingUser($hospital);
        $discountedUsers = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getDiscountedUsers($hospital);
        return $this->render('ReportBundle:Hospital/Sales:invoice.html.twig', array(
            'employees' => $employees,
            'assignDoctors' => $assignDoctors,
            'anesthesiaDoctors' => $anesthesiaDoctors,
            'referredDoctors' => $referredDoctors,
            'discountedUsers' => $discountedUsers,
            'marketingUsers' => $marketingUsers,
            'departments' => $departments,
            'processes' => $processes,
            'searchForm' => $data,
            'option' => $user->getGlobalOption(),

        ));
    }

    /**
     * @Route("/sales-hospital-invoice-load", methods={"GET", "POST"}, name="hms_report_hospital_invoice_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function hmsSalesInvoiceAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        if(isset($data['startDate']) and $data['endDate']) {
            $entities = $em->getRepository('HospitalBundle:Invoice')->reportHmsLists($user , $data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Hospital/Sales:invoice-ajax.html.twig', array(
                    'entities' => $entities,
                    'option' => $user->getGlobalOption(),
                    'searchForm' => $data,
                )
            );
            return new Response($htmlProcess);
        }
        return new Response('Record Does not found');
    }

    /**
     * @Route("/daily-sales-collection", methods={"GET", "POST"}, name="hms_report_daily_sales_collection")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsSalesCollectionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->getFindEmployees($hospital);
        $discountedUsers = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getDiscountedUsers($hospital);
        return $this->render('ReportBundle:Hospital/Sales:sales-collection.html.twig', array(
            'employees' => $employees,
            'discountedUsers' => $discountedUsers,
            'option' => $user->getGlobalOption(),
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/daily-sales-collection-load", methods={"GET", "POST"}, name="hms_report_daily_sales_collection_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function hmsSalesCollectionAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        if(isset($data['startDate']) and $data['endDate']) {
            $entities = $em->getRepository('HospitalBundle:Invoice')->hmsSalesCollectionReports($user,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Hospital/Sales:sales-collection-ajax.html.twig', array(
                    'entities' => $entities,
                    'option' => $user->getGlobalOption(),
                    'searchForm' => $data,
                )
            );
            return new Response($htmlProcess);
        }
        return new Response('Record Does not found');
    }


    /**
     * @Route("/daily-sales-collection-commission", methods={"GET", "POST"}, name="hms_report_sales_collection_commission")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsSalesCollectionCommissionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital, array(5,6));
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        return $this->render('ReportBundle:Hospital/Sales:sales-collection-commission.html.twig', array(
            'referredDoctors' => $referredDoctors,
            'employees' => $employees,
            'option' => $user->getGlobalOption(),
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/daily-sales-collection-commission-load", methods={"GET", "POST"}, name="hms_report_daily_sales_collection_commission_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function hmsSalesCollectionCommissionAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        if(isset($data['startDate']) and $data['endDate']) {
            $entities = $em->getRepository('HospitalBundle:Invoice')->hmsSalesCollectionComissionReports($user,$data);
            $pagination = $entities->getQuery()->getResult();
            $commissionSummary = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommissionSummary($hospital, $entities);
            $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommission($hospital, $entities);
            $htmlProcess = $this->renderView(
                'ReportBundle:Hospital/Sales:sales-collection-commission-ajax.html.twig', array(
                    'entities' => $pagination,
                    'commissions' => $commissions,
                    'commissionSummary' => $commissionSummary,
                    'option' => $user->getGlobalOption(),
                    'searchForm' => $data,
                )
            );
            return new Response($htmlProcess);
        }
        return new Response('Record Does not found');
    }


    /**
     * @Route("/collection-commission-group", methods={"GET", "POST"}, name="hms_report_collection_commission_group")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsCommissionGroupAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital, array(5,6));
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        return $this->render('ReportBundle:Hospital/Sales:collection-commission-group.html.twig', array(
            'referredDoctors' => $referredDoctors,
            'employees' => $employees,
            'option' => $user->getGlobalOption(),
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/collection-commission-group-load", methods={"GET", "POST"}, name="hms_report_collection_commission_group_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function  hmsCommissionGroupAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        if(isset($data['startDate']) and $data['endDate']) {
            $entities = $em->getRepository('HospitalBundle:Invoice')->hmsCommissionGroupReports($user,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Hospital/Sales:collection-commission-group-ajax.html.twig', array(
                    'entities' => $entities,
                    'option' => $user->getGlobalOption(),
                    'searchForm' => $data,
                )
            );
            return new Response($htmlProcess);
        }
        return new Response('Record Does not found');
    }



     /**
     * @Route("/collection-commission-deatils", methods={"GET", "POST"}, name="hms_report_collection_commission_details")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsCommissionDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital, array(5,6));
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        return $this->render('ReportBundle:Hospital/Sales:sales-commission-details.html.twig', array(
            'referredDoctors' => $referredDoctors,
            'employees' => $employees,
            'option' => $user->getGlobalOption(),
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/collection-commission-deatils-load", methods={"GET", "POST"}, name="hms_report_collection_commission_details_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function  hmsCommissionDetailsAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        if(isset($data['startDate']) and $data['endDate']) {
            $entities = $em->getRepository('HospitalBundle:Invoice')->hmsCommissionReportDetails($user,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Hospital/Sales:sales-commission-details-ajax.html.twig', array(
                    'entities' => $entities,
                    'option' => $user->getGlobalOption(),
                    'searchForm' => $data,
                )
            );
            return new Response($htmlProcess);
        }
        return new Response('Record Does not found');
    }



    /**
     * @Route("/hospital-user-sales", methods={"GET", "POST"}, name="hms_report_user_sales")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsUserSalesAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (!empty($data['date'])) {
            $datetime = new \DateTime($data['date']);
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }

        $summary = $em->getRepository('HospitalBundle:Invoice')->salesSummary($user, $data);
        $diagnosticOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'diagnostic');
        $admissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'admission');
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user, $data);
        $transactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->findWithTransactionOverview($user, $data);
        $commissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithCommissionOverview($user, $data);
        $salesTodayUser      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesUsers($user,$data);

        $salesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'false',array('diagnostic','admission','visit'));
        $previousSalesTodayUserTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserGroupSalesOverview($user,$data,'true',array('diagnostic','admission','visit'));
        $userSalesTodaySalesCommission      = $em->getRepository('HospitalBundle:DoctorInvoice')->userGroupCommissionSummary($user,$data);
        $userInvoiceReturn   = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->userGroupInvoiceReturnAmount($user,$data);
        $salesTodayTransactionOverview      = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserSalesOverview($user,$data,'false',array('diagnostic','admission','visit'));
        $previousSalesTransactionOverview   = $em->getRepository('HospitalBundle:InvoiceTransaction')->todayUserSalesOverview($user,$data,'true',array('diagnostic','admission','visit'));
        $salesTodaySalesCommission          = $em->getRepository('HospitalBundle:DoctorInvoice')->commissionUserSummary($user,$data);
        $invoiceReturn   = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->userInvoiceReturnAmount($user,$data);

        return $this->render('ReportBundle:Hospital:user-sales.html.twig', array(

            'diagnosticOverview' => $diagnosticOverview,
            'admissionOverview' => $admissionOverview,
            'serviceOverview' => $serviceOverview,
            'transactionOverview' => $transactionOverview,
            'commissionOverview' => $commissionOverview,
            'salesTodayUsers' => $salesTodayUser,
            'salesTodayTransactionOverview'     => $salesTodayTransactionOverview,
            'previousSalesTransactionOverview'  => $previousSalesTransactionOverview,
            'salesTodayUserTransactionOverview'     => $salesTodayUserTransactionOverview,
            'previousSalesTodayUserTransactionOverview'     => $previousSalesTodayUserTransactionOverview,
            'salesTodaySalesCommission'         => $salesTodaySalesCommission,
            'userSalesTodaySalesCommission'     => $userSalesTodaySalesCommission,
            'invoiceReturn'                     => $invoiceReturn ,
            'userInvoiceReturn'                 => $userInvoiceReturn ,
            'summary' => $summary,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }



    /**
     * @Route("/hospital-diagnostic-invoice", methods={"GET", "POST"}, name="hms_report_diagnostic_invoice")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsDiagnosticInvoiceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();

        $globalOption = $this->getUser()->getGlobalOption();
        $hospital = $globalOption->getHospitalConfig();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        $processes = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAdmissionProcess($hospital,'visit',$data);
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital,'referred-doctor');

        $entities = $em->getRepository('HospitalBundle:Invoice')->reportInvoiceLists($user , $mode = 'diagnostic' , $data);
        return $this->render('ReportBundle:Hospital/Sales:diagnostic-invoice.html.twig', array(
            'entities' => $entities,
            'assignDoctors'                     => $assignDoctors,
            'referredDoctors'                     => $referredDoctors,
            'processes' => $processes,
            'employees' => $employees,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }


    /**
     * @Route("/hospital-visit-invoice", methods={"GET", "POST"}, name="hms_report_visit_invoice")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsVisitInvoiceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportVisitLists($user , $mode = 'visit' , $data);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        $processes = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAdmissionProcess($hospital,'visit',$data);
        return $this->render('ReportBundle:Hospital/Sales:visit-invoice.html.twig', array(
            'employees' => $employees,
            'assignDoctors' => $assignDoctors,
            'processes' => $processes,
            'entities' => $entities,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-admission-invoice", methods={"GET", "POST"}, name="hms_report_admission_invoice")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsAdmissionInvoiceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportAdmissionLists($user , $mode = 'admission' , $data);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $anesthesiaDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAanesthesiaDoctor($hospital);
        $assistantDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssistantDoctor($hospital);
        $departments = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getDepartments($hospital);
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        $diseases = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindDiseases($hospital);
        return $this->render('ReportBundle:Hospital/Sales:admission-invoice.html.twig', array(
            'employees' => $employees,
            'anesthesiaDoctor' => $anesthesiaDoctor,
            'assignDoctors' => $assignDoctors,
            'assistantDoctor' => $assistantDoctor,
            'departments' => $departments,
            'diseases' => $diseases,
            'entities' => $entities,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-admission-invoice-service", methods={"GET", "POST"}, name="hms_report_admission_service")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsAdmissionServiceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $serviceGroups = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getServiceGroups($hospital);
        $surgeryDepartment = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig'=> $hospital,'service'=>10),array('name'=>"ASC"));
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportAdmissionService($user , $mode = 'admission' , $data);
        $categories = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory')->findBy(array('parent'=>2),array('name' =>'asc' ));
        $departments = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory')->findBy(array('parent'=>7),array('name' =>'asc' ));
        return $this->render('ReportBundle:Hospital/Sales:service.html.twig', array(
            'entities' => $entities,
            'serviceGroups' => $serviceGroups,
            'departments' => $departments,
            'surgeryDepartment' => $surgeryDepartment,
            'categories' => $categories,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-admission-surgery-department", methods={"GET", "POST"}, name="hms_report_admission_surgery_department")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsAdmissionSurgeryDepartmentAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $serviceGroups = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getServiceGroups($hospital);
        $services = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getServices($hospital);
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportAdmissionDepartment($user , $mode = 'admission' , $data);
        return $this->render('ReportBundle:Hospital/Sales:surgery-department.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-admission-surgery-department-details", methods={"GET", "POST"}, name="hms_report_admission_surgery_department_details")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsAdmissionSurgeryDepartmentDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        $surgeryDepartment = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig'=> $hospital,'service'=>10),array('name'=>"ASC"));
        $serviceGroups = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getServiceGroups($hospital);
        $particulars = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getParticulars($hospital);
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportAdmissionDepartmentDetails($user , $mode = 'admission' , $data);
        return $this->render('ReportBundle:Hospital/Sales:surgery-department-details.html.twig', array(
            'entities' => $entities,
            'particulars' => $particulars,
            'serviceGroups' => $serviceGroups,
            'surgeryDepartment' => $surgeryDepartment,
            'employees' => $employees,
            'assignDoctors' => $assignDoctors,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-diseases", methods={"GET", "POST"}, name="hms_report_diseases")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsDiseasesAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportDiseases($user , $mode = 'admission' , $data);
        return $this->render('ReportBundle:Hospital/Sales:diseases.html.twig', array(
            'entities' => $entities,
            'assignDoctors' => $assignDoctors,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-diseases-details", methods={"GET", "POST"}, name="hms_report_diseases_details")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsDiseasesDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $diseaseses = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig'=>$hospital,'service'=>11),array('name'=>'ASC'));
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportDiseasesDetails($user , $mode = 'admission' , $data);
        return $this->render('ReportBundle:Hospital/Sales:diseases-details.html.twig', array(
            'entities' => $entities,
            'assignDoctors' => $assignDoctors,
            'diseaseses' => $diseaseses,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }


    /**
     * @Route("/monthly-commission-details", methods={"GET", "POST"}, name="hms_report_monthly_commission_details")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function monthlyCommissionDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $data = $_REQUEST;
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($user->getGlobalOption()->getHospitalConfig(), array(5, 6));
        return $this->render('ReportBundle:Hospital/Monthly:commission.html.twig', array(
            'referredDoctors' => $referredDoctors,
            'searchForm' => $data
        ));

    }

    /**
     * @Route("/monthly-commission-details-load", methods={"GET", "POST"}, name="hms_report_monthly_commission_details_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function monthlyCommissionDetailsLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();
        if(isset($data['month']) and $data['month'] and isset($data['year']) and $data['year'] ) {
            $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyCommissionDetails($user, $data);
            $monthlyReferredCommissionInvoice = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyReferredCommissionInvoice($user, $data);
            $commissionSummary = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->monthlyGroupBaseCommissionSummary($user, $data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Hospital/Monthly:commission-details.html.twig', array(
                    'referredInvoice' => $monthlyReferredCommissionInvoice,
                    'commissions' => $commissions,
                    'commissionSummary' => $commissionSummary,
                    'searchForm' => $data,
                    'globalOption' => $globalOption,
                )
            );
            return new Response($htmlProcess);
        }
        return new Response('Record Does not found');
    }


    /* Inventory Report */

    /**
     * @Route("/inv-system-overview", methods={"GET", "POST"}, name="inv_system_overview")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function invSystemOverviewAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $inventory = $globalOption->getInventoryConfig()->getId();
        $purchaseOverview = $em->getRepository('ReportBundle:Report')->invReportPurchaseOverview($inventory,$data);
        $priceOverview = $em->getRepository('ReportBundle:Report')->invReportStockPriceOverview($inventory,$data);
        $salesPurchasePrice = $em->getRepository('ReportBundle:Report')->invReportSalesPurchasePrice($inventory,$data);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        return $this->render('ReportBundle:Inventory:index.html.twig', array(
            'priceOverview' => $priceOverview[0],
            'stockOverview' => $stockOverview,
            'purchaseOverview' => $purchaseOverview,
            'salesPurchasePrice' => $salesPurchasePrice,
            'option' => $globalOption,
        ));
    }


    /**
     * @Route("/inv-stock-item-price", methods={"GET", "POST"}, name="inv_stock_item_price")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $priceOverview = $em->getRepository('ReportBundle:Report')->invReportStockPriceOverview($inventory,$data);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);

        return $this->render('ReportBundle:Inventory/Stock:stock.html.twig', array(
            'searchForm' => $data,
            'priceOverview' => $priceOverview[0],
            'stockOverview' => $stockOverview,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-stock-item-price-ajax-load", methods={"GET", "POST"}, name="inv_stock_item_price_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invStockItemAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportStockItemPrice($inventory,$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/Stock:stock-item-price-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }

    /**
     * @Route("/inv-category-stock-item-price", methods={"GET", "POST"}, name="inv_category_stock_item_price")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invCategoryStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('ReportBundle:Inventory/Stock:category-stock.html.twig', array(
            'searchForm' => $data,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-category-stock-item-price-ajax-load", methods={"GET", "POST"}, name="inv_category_stock_item_price_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invCategoryStockItemAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportCategoryStockItemPrice($inventory,$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/Stock:category-stock-item-price-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }


    /**
     * @Route("/inv-brand-stock-item-price", methods={"GET", "POST"}, name="inv_brand_stock_item_price")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invBrandStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('ReportBundle:Inventory/Stock:brand-stock.html.twig', array(
            'searchForm' => $data,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-brand-stock-item-price-ajax-load", methods={"GET", "POST"}, name="inv_brand_stock_item_price_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invBrandStockItemAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportBrandStockItemPrice($inventory,$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/Stock:brand-stock-item-price-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }

    /**
     * @Route("/inv-sales-profit-loss", methods={"GET", "POST"}, name="inv_sales_profit_loss")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $customers = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getSalesCustomers($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Inventory/Sales:index.html.twig', array(
            'transactionMethods' => $transactionMethods,
            'customers' => $customers,
            'searchForm' => $data,
            'users' => $users,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-sales-profit-loss-ajax-load", methods={"GET", "POST"}, name="inv_sales_profit_loss_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invSalesAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportSales($inventory->getId(),$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/Sales:ajax-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }

    /**
     * @Route("/inv-salesitem-profit-loss", methods={"GET", "POST"}, name="inv_salesitem_profit_loss")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invSalesItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $customers = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getSalesCustomers($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Inventory/SalesItem:index.html.twig', array(
            'transactionMethods' => $transactionMethods,
            'customers' => $customers,
            'searchForm' => $data,
            'users' => $users,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-salesitem-profit-loss-ajax-load", methods={"GET", "POST"}, name="inv_salesitem_profit_loss_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invSalesItemAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportSalesItem($inventory->getId(),$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/SalesItem:ajax-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }

    /**
     * @Route("/miss-system-dashboard", methods={"GET", "POST"}, name="miss_system_overview")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function missDashboardAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $customers = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getSalesCustomers($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Inventory/SalesItem:index.html.twig', array(
            'transactionMethods' => $transactionMethods,
            'customers' => $customers,
            'searchForm' => $data,
            'users' => $users,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/miss-stock-report", methods={"GET", "POST"}, name="miss_stock_report")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function missStockReportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('ReportBundle:Medicine/Stock:stock.html.twig', array(
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/miss-stock-report-ajax-load", methods={"GET", "POST"}, name="miss_stock_report_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function missStockReportAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $pagination = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->misRemainingStock($config);
        $htmlProcess = $this->renderView(
            'ReportBundle:Medicine/Stock:stock-item-price-data.html.twig', array(
                'entities' => $pagination,
            )
        );
        return new Response($htmlProcess);
    }

    /**
     * @Route("/miss-marketing-executive-report", methods={"GET", "POST"}, name="miss_marketing_executive_report")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function missMarketingExecutiveReportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('ReportBundle:Business/MarketingExecutive:index.html.twig', array(
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/miss-marketing-executive-report-ajax-load", methods={"GET", "POST"}, name="miss_marketing_executive_report_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function missMarketingExecutiveAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $global = $this->getUser()->getGlobalOption();
        $pagination = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessExecutive($global,$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Business/MarketingExecutive:ajax-data.html.twig', array(
                'entities' => $pagination,
                'data' => $data,
            )
        );
        return new Response($htmlProcess);
    }

    /**
     * @Route("/miss-customer-invoice", methods={"GET", "POST"}, name="miss_customer_invoice_report")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */
    public function customerInvoiceReportAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $executive = "";
        $marketing = (isset($data['marketing']) and $data['marketing'] ) ? $data['marketing'] :'';
        if($marketing){
            $executive = $this->getDoctrine()->getRepository(User::class)->find($marketing);
        }
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerInvoice($globalOption,$data);
        return $this->render('ReportBundle:Default/Ledger:customer.html.twig', array(
            'option' => $globalOption,
            'executive' => $executive,
            'entities' => $entities['current'],
            'previous' => $entities['previous'],
            'data' => $data,
        ));

    }

     /**
     * @Route("/miss-customer-invoice-ledger", methods={"GET", "POST"}, name="miss_customer_invoice_ledger_report")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */
    public function customerInvoiceLedgerReportAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $customer = "";
        $entities = "";
        $customerId = (isset($data['customer']) and $data['customer'] ) ? $data['customer'] :'';
        $customers = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->customerOutstanding($globalOption);
        if($customerId) {
             if($customerId){
                 $customer = $this->getDoctrine()->getRepository(Customer::class)->find($customerId);
             }
             $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerLedgerDetails($globalOption,$data);
        }
        return $this->render('ReportBundle:Default/Ledger:customerDetails.html.twig', array(
            'option' => $globalOption,
            'customers' => $customers,
            'customer' => $customer,
            'entities' => ($entities) ? $entities['current'] : "",
            'previous' => ($entities) ? $entities['previous'] : "",
            'searchForm' => $data,
        ));

    }

    /**
     * @Route("/miss-business-product", methods={"GET", "POST"}, name="miss_business_product_report")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */
    public function businessProductReportAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $executive = "";
        $customer = "";
        $marketing = (isset($data['marketing']) and $data['marketing'] ) ? $data['marketing'] :'';
        $customerId = (isset($data['customer']) and $data['customer'] ) ? $data['customer'] :'';
        if($marketing){
            $executive = $this->getDoctrine()->getRepository(User::class)->find($marketing);
        }
        if($customerId){
            $customer = $this->getDoctrine()->getRepository(Customer::class)->find($customerId);
        }
        $entities = $this->getDoctrine()->getRepository(BusinessInvoiceParticular::class)->getMarketingSalesProducts($globalOption,$data);
        $salesReturns = $this->getDoctrine()->getRepository(BusinessInvoiceReturnItem::class)->getMarketingSalesReturnProducts($globalOption,$data);

        return $this->render('ReportBundle:Business/Stock:stock.html.twig', array(
            'option' => $globalOption,
            'marketing' => $executive,
            'customer' => $customer,
            'entities' => $entities,
            'salesReturns' => $salesReturns,
            'searchForm' => $data,
        ));

    }


}
