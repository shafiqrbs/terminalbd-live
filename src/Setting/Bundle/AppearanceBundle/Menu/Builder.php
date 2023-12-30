<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 3/4/15
 * Time: 3:36 PM
 */

namespace Setting\Bundle\AppearanceBundle\Menu;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Setting\Bundle\AppearanceBundle\Entity\MegaMenu;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\DependencyInjection\ContainerAware;


class Builder extends ContainerAware
{

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $globalOption = $securityContext->getGlobalOption();

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'page-sidebar-menu');
        $menu = $this->dashboardMenu($menu);
        if ($securityContext->getRole() === 'ROLE_SUPER_ADMIN') {

	        $menu = $this->manageDomainMenu($menu);
	    //    $menu = $this->businessMenu($menu);
	      //  $menu = $this->AccountingMenu($menu);
	     //   $menu = $this->PayrollMenu($menu);
	        $menu = $this->toolsMenu($menu);
	        $menu = $this->manageFrontendMenu($menu);
	        $menu = $this->manageApplicationSettingMenu($menu);
	        // $menu = $this->productCategoryMenu($menu);
	        // $menu = $this->manageVendorMenu($menu);
	        // $menu = $this->manageAdvertismentMenu($menu);
           //  $menu = $this->manageSystemAccountMenu($menu);
          //  $menu = $this->reservationMenu($menu);

        }
            $modules = "";
            if($globalOption->getSiteSetting()){
                $modules = $globalOption->getSiteSetting()->getAppModules();
            }
            $arrSlugs = array();
            $menuName = array();
            if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
                foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                    if (!empty($mod->getModuleClass())) {
                        $menuName[] = $mod->getModuleClass();
                        $arrSlugs[] = $mod->getSlug();
                    }
                }
            }

            $result = array_intersect($menuName, array('Inventory'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_INVENTORY')){
                    if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES')) {
                        $menu = $this->InventorySalesMenu($menu);
                    }
                    if ($securityContext->isGranted('ROLE_INVENTORY')) {
                        $menu = $this->InventoryMenu($menu);
                    }
                }
            }

            $result = array_intersect($menuName, array('Hospital'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_HOSPITAL')){
                    $menu = $this->HospitalMenu($menu);
                }
            }



            $result = array_intersect($menuName, array('Miss'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_MEDICINE')){
                    $menu = $this->medicineMenu($menu);
                }
            }


            $result = array_intersect($menuName, array('Dps'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DPS')){
                    $menu = $this->DpsMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Restaurant'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_RESTAURANT')){
                    $menu = $this->RestaurantMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Business'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_BUSINESS') or $securityContext->isGranted('ROLE_DOMAIN')){
                    $menu = $this->BusinessMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Hotel'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_HOTEL') or $securityContext->isGranted('ROLE_DOMAIN')){
	                $menu = $this->ReservationMenu($menu);
                }
            }

		    $result = array_intersect($menuName, array('Election'));
		    if (!empty($result)) {
			    if ($securityContext->isGranted('ROLE_ELECTION')){
				    //$menu = $this->ElectionMenu($menu);
				    $menu = $this->CommitteeMenu($menu);
			    }
		    }

            $result = array_intersect($menuName, array('Accounting'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ACCOUNTING')){
                    $menu = $this->AccountingMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Assets'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ASSETS')){
                    $menu = $this->AssetsMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Payroll'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_HR') || $securityContext->isGranted('ROLE_PAYROLL')){
                    $menu = $this->PayrollMenu($menu);
                }
            }

		    $result = array_intersect($menuName, array('Website'));
		    if (!empty($result)) {
			    if ($securityContext->isGranted('ROLE_WEBSITE')){
				    $menu = $this->WebsiteMenu($menu,$menuName);
			    }
		    }


	    if ($securityContext->isGranted('ROLE_DOMAIN') || $securityContext->isGranted('ROLE_SMS')) {
                $menu = $this->manageDomainInvoiceMenu($menu);
            }
            return $menu;
    }

    public function dashboardMenu($menu)
    {
        $menu
            ->addChild('Dashboard', array('route' => 'homepage'))
            ->setAttribute('icon', 'fa fa-home');
        return $menu;
    }

/*    public function manageCustomerOrderMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $menu
            ->addChild('Inbox & Transaction')
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'fa fa-user-circle');
        $menu['Inbox & Transaction']->addChild('Client', array('route' => 'agentclient'))->setAttribute('icon', 'icon-users');
        $menu['Inbox & Transaction']->addChild('Receive Invoice', array('route' => 'agentclient_invoice'));
        $menu['Inbox & Transaction']->addChild('Manage Inbox')->setAttribute('dropdown', true);
        $menu['Inbox & Transaction']['Manage Inbox']->addChild('Email', array('route' => 'invoicesmsemail'));
        $menu['Inbox & Transaction']['Manage Inbox']->addChild('SMS', array('route' => 'invoicemodule'));
        return $menu;
    }*/


    public function businessMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        /* @var $config BusinessConfig */

        $config = $securityContext->getGlobalOption()->getBusinessConfig();
       // $business = empty($config->getBusinessModel()) ? "Business" : ucfirst($config->getBusinessModel());
        $business = 'Inventory & Sales';

        $menu
            ->addChild("{$business}")
            ->setAttribute( 'icon', 'icon icon-bar-chart' )
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_BUSINESS_INVOICE') and $config->getBusinessModel() != "association") {
	        $menu[$business]->addChild('Manage Sales')->setAttribute('dropdown', true);
            $menu[$business]['Manage Sales']->addChild('Add Sales', array('route' => 'business_invoice_new'))
                ;
            $menu[$business]['Manage Sales']->addChild('Sales', array('route' => 'business_invoice'));
            $menu[$business]['Manage Sales']->addChild('Android Sales', array('route' => 'business_android_sales'))
                ;
            if($config->isConditionSales() == 1){
                $menu[$business]['Manage Sales']->addChild('Condition Sales', array('route' => 'business_invoice_condition'))
                    ;
            }
            $menu[$business]['Manage Sales']->addChild('Sales Return', array('route' => 'business_invoice_return'))
                ;
            $menu[$business]['Manage Sales']->addChild('Sales Return Item', array('route' => 'business_invoice_return_item'))
                ;
            if($securityContext->isGranted('ROLE_CRM') or $securityContext->isGranted('ROLE_DOMAIN')) {
                $menu[$business]['Manage Sales']->addChild('Customer ', array('route' => 'domain_customer'));
            }
        }
        if ($securityContext->isGranted('ROLE_BUSINESS_INVOICE') and $config->getBusinessModel() == "distribution") {
            $menu[$business]->addChild('Store Customer')->setAttribute('dropdown', true);
            $menu[$business]['Store Customer']->addChild('Store Ledger', array('route' => 'business_store_ledger'));
            $menu[$business]['Store Customer']->addChild('Store', array('route' => 'business_store'));
        }
        if( ($config->getBusinessModel() == "association" and $securityContext->isGranted('ROLE_CRM')) or ($config->getBusinessModel() == "association" and $securityContext->isGranted('ROLE_DOMAIN'))) {
            $menu[$business]->addChild('Manage Member', array('route' => 'domain_association'));
            $menu[$business]->addChild('Member Invoice', array('route' => 'business_invoice'));
            if ($securityContext->isGranted('ROLE_BUSINESS_ASSOCIATION_REPORT')){
                $menu[$business]->addChild( 'Association Reports' )
                    ->setAttribute( 'dropdown', true );
                $menu[$business]['Association Reports']->addChild( 'Invoice Summary', array( 'route' => 'business_report_sales_summary' ) )
                    ;
                $menu[$business]['Association Reports']->addChild( 'Invoice Details', array( 'route' => 'business_report_sales_details' ) )
                    ;
                $menu[$business]['Association Reports']->addChild( 'Customer Invoice', array( 'route' => 'business_report_customer_sales_item' ) )
                    ;
                $menu[$business]['Association Reports']->addChild( 'Product Wise Invoice', array( 'route' => 'business_report_sales_stock' ) )                              ;
                $menu[$business]['Association Reports']->addChild( 'User wise Invoice', array( 'route' => 'business_report_sales_user' ) )
                    ;
                $menu[$business]['Association Reports']->addChild( 'User Monthly Invoice', array( 'route' => 'business_report_sales_user_monthly' ) )
                    ;
            }
        }
	    if($config->isShowStock() == 1 and $securityContext->isGranted('ROLE_BUSINESS_PURCHASE')) {
		    $menu[$business]->addChild('Manage Purchase')->setAttribute('dropdown', true);
		    $menu[$business]['Manage Purchase']->addChild('Purchase', array('route' => 'business_purchase'));
		    $menu[$business]['Manage Purchase']->addChild('Add Purchase', array('route' => 'business_purchase_new'));
		    $menu[$business]['Manage Purchase']->addChild('Purchase Return', array('route' => 'business_purchase_return'));
            if($config->getBusinessModel() == 'distribution') {
                $menu[$business]['Manage Purchase']->addChild(
                    'Damage Return',
                    array('route' => 'business_distribution_return')
                );
            }
		    $menu[$business]['Manage Purchase']->addChild('Purchase Item', array('route' => 'business_purchase_item'));
		    $menu[$business]['Manage Purchase']->addChild('Vendor', array('route' => 'business_vendor'));

	    }
        if ($config->isShowStock() == 1 and $securityContext->isGranted('ROLE_BUSINESS_STOCK')) {
            $menu[$business]->addChild('Manage Stock')->setAttribute('dropdown', true);
            $menu[$business]['Manage Stock']->addChild('Stock Item', array('route' => 'business_stock'));
            $menu[$business]['Manage Stock']->addChild('Stock Import', array('route' => 'business_itemimporter'));
            $menu[$business]['Manage Stock']->addChild('Category', array('route' => 'business_category'));
            $menu[$business]['Manage Stock']->addChild('Stock Shortlist', array('route' => 'business_stock_shortlist'));
            $menu[$business]['Manage Stock']->addChild('Stock History', array('route' => 'business_stock_history'));
            $menu[$business]['Manage Stock']->addChild('Stock Adjustment', array('route' => 'business_stock_adjustment'));
            if($config->isStockHistory() == 1) {
                $menu[$business]['Manage Stock']->addChild('Product Ledger', array('route' => 'business_stock_ledger'));
            }
            $menu[$business]['Manage Stock']->addChild('Stock Report', array('route' => 'business_report_stock_report'));
            if($config->getProductionType() == 'pre-production') {
                $menu[$business]['Manage Stock']->addChild( 'Pre-production', array( 'route' => 'business_production' ) );
            }
            $menu[$business]['Manage Stock']->addChild('Stock Transfer', array('route' => 'business_stock_transfer'));
            $menu[$business]['Manage Stock']->addChild('Manage Damage', array('route' => 'business_damage'));

            if($config->getBusinessModel() == 'commission') {
                    $menu[$business]->addChild( 'Agency Stock' )->setAttribute( 'dropdown', true );
                    $menu[$business]['Agency Stock']->addChild('Vendor Stock Item', array('route' => 'business_vendor_stock_item'));
                    $menu[$business]['Agency Stock']->addChild('Vendor Stock', array('route' => 'business_vendor_stock'));
                    $menu[$business]['Agency Stock']->addChild( 'Stock Report' )->setAttribute( 'dropdown', true );
                    $menu[$business]['Agency Stock']['Stock Report']->addChild( 'Vendor base Sales', array( 'route' => 'business_report_vendor_commission_sales' ) );
            }
        }
        if ($securityContext->isGranted('ROLE_BUSINESS_REPORT') and $config->getBusinessModel() != "association") {
            $menu[$business]->addChild('Reports')->setAttribute('dropdown', true);
            $menu[$business]['Reports']->addChild( 'System Overview', array( 'route' => 'business_report_system_overview' ) );
            $menu[$business]['Reports']->addChild( 'Stock Price', array( 'route' => 'business_report_stock_price' ) );
            $menu[$business]['Reports']->addChild( 'Item Ledger', array( 'route' => 'business_report_stock_ledger' ) );
            $menu[$business]['Reports']->addChild( 'Item Monthly Ledger', array( 'route' => 'business_report_monthly_stock_ledger' ) );
            $menu[$business]['Reports']->addChild( 'Monthly Stock Summary', array( 'route' => 'business_report_monthly_stock_summary' ) );
            if($securityContext->isGranted('ROLE_BUSINESS_REPORT') and $securityContext->isGranted('ROLE_BUSINESS_PURCHASE')) {
                $menu[$business]['Reports']->addChild('Sales')
                    ->setAttribute('dropdown', true);
                $menu[$business]['Reports']['Sales']->addChild('Sales Summary', array('route' => 'business_report_sales_summary'));
                $menu[$business]['Reports']['Sales']->addChild('Sales & Income', array('route' => 'business_report_sales_details'));
                $menu[$business]['Reports']['Sales']->addChild('Customer Item Sales', array('route' => 'business_report_customer_sales_item'));
                $menu[$business]['Reports']['Sales']->addChild('Item Sales & Income', array('route' => 'business_report_sales_stock'));
                if ($config->getBusinessModel() == 'commission') {
                    $menu[$business]['Reports']['Sales']->addChild(
                        'Sales Item Details',
                        array('route' => 'business_report_commission_stock')
                    );
                }
                $menu[$business]['Reports']['Sales']->addChild(
                    'Daily Item Sales Price',
                    array('route' => 'business_report_monthly_sales_price')
                );

                $menu[$business]['Reports']['Sales']->addChild(
                    'Customer Daily Item Sales',
                    array('route' => 'business_report_customer_monthly_sales_stock')
                );

                $menu[$business]['Reports']['Sales']->addChild(
                    'Daily Sales Stock',
                    array('route' => 'business_report_monthly_sales_stock')
                );

                if ($config->getBusinessModel() == 'distribution') {

                    $menu[$business]['Reports']['Sales']->addChild(
                        'Sales SR',
                        array('route' => 'business_report_sales_sr')
                    );
                    $menu[$business]['Reports']['Sales']->addChild(
                        'Sales DSR',
                        array('route' => 'business_report_sales_dsr')
                    );
                    $menu[$business]['Reports']['Sales']->addChild(
                        'Sales Area',
                        array('route' => 'business_report_sales_area')
                    );

                }
                if($config->isConditionSales() == 1) {
                    $menu[$business]['Reports']->addChild('Condition')
                        ->setAttribute('dropdown', true);

                    $menu[$business]['Reports']['Condition']->addChild(
                        'Condition',
                        array('route' => 'business_report_condition')
                    );
                    $menu[$business]['Reports']['Condition']->addChild(
                        'Condition Customer',
                        array('route' => 'business_report_condition_customer')
                    );
                    $menu[$business]['Reports']['Condition']->addChild(
                        'Condition Details',
                        array('route' => 'business_report_condition_details')
                    );
                }

            }
            if($securityContext->isGranted('ROLE_BUSINESS_REPORT') and $securityContext->isGranted('ROLE_BUSINESS_PURCHASE')){
                $menu[$business]['Reports']['Sales']->addChild( 'User Monthly Invoice', array( 'route' => 'business_report_sales_user_monthly' ) );
                $menu[$business]['Reports']->addChild( 'Purchase' )
                    ->setAttribute( 'dropdown', true );;
                $menu[$business]['Reports']['Purchase']->addChild( 'Purchase Summary', array( 'route' => 'business_report_purchase_summary' ) );
                $menu[$business]['Reports']['Purchase']->addChild( 'Vendor Ledger', array( 'route' => 'business_report_purchase_ledger' ) );
                $menu[$business]['Reports']['Purchase']->addChild( 'Vendor Details', array( 'route' => 'business_report_purchase_vendor_details' ) );
                $menu[$business]['Reports']['Purchase']->addChild( 'Stock Price', array( 'route' => 'business_report_purchase_stock_price' ) );
                $menu[$business]['Reports']['Purchase']->addChild( 'Stock Item Price', array( 'route' => 'business_report_item_stock_price' ) );
                $menu[$business]['Reports']['Purchase']->addChild( 'Stock Item', array( 'route' => 'business_report_item_stock' ) );
            }

        }
        $menu[$business]->addChild( 'Notepad', array('route' => 'domain_notepad'));
	    if ($securityContext->isGranted('ROLE_BUSINESS_MANAGER')) {
		    $menu[$business]->addChild('Master Data')->setAttribute('dropdown', true);
		    if($config->isShowStock() == 1){
                $menu[$business]['Master Data']->addChild('User Sales Setup', array('route' => 'business_sales_user'));
                $menu[$business]['Master Data']->addChild('Wear House', array('route' => 'business_wearhouse'));
                $menu[$business]['Master Data']->addChild('Brand', array('route' => 'business_brand'));
                $menu[$business]['Master Data']->addChild('Marketing', array('route' => 'business_marketing'));
                $menu[$business]['Master Data']->addChild('Courier', array('route' => 'business_courier'));
            }
		    $menu[$business]['Master Data']->addChild('Configuration', array('route' => 'business_config_manage'));
	    }
	    return $menu;

    }

    public function InstituteMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $config = $securityContext->getGlobalOption()->getEducationConfig();

        $menu
            ->addChild('Institute Management')
            ->setAttribute('icon', 'icon-briefcase')
            ->setAttribute('dropdown', true);

	    if ($securityContext->isGranted('ROLE_BUSINESS_MANAGER')) {

		    $menu['Institute Management']->addChild('Master Data')

		                                ->setAttribute('dropdown', true);
		    $menu['Institute Management']['Master Data']->addChild('Option', array('route' => 'education_particular'));
		    $menu['Institute Management']['Master Data']->addChild('Configuration', array('route' => 'education_config_manage'));
	    }
	    return $menu;

    }

    public function WebsiteMenu($menu,$menuName)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $option = $securityContext->getGlobalOption();

        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_MANAGER')) {

            $menu
                ->addChild('Manage Website')
                ->setAttribute('icon', 'fa fa-book')
                ->setAttribute('dropdown', true);

            $menu['Manage Website']->addChild('Page', array('route' => 'page'));
            if ($option->getSiteSetting()) {
                $syndicateModules = $option->getSiteSetting()->getSyndicateModules();
                if (!empty($syndicateModules)) {
                    foreach ($option->getSiteSetting()->getSyndicateModules() as $syndmod) {
                        $menu['Manage Website']->addChild($syndmod->getName(), array('route' => strtolower($syndmod->getModuleClass())));
                    }
                }

                $modules = $option->getSiteSetting()->getModules();
                if (!empty($modules)) {
                    foreach ($option->getSiteSetting()->getModules() as $mod) {
                        $menu['Manage Website']->addChild($mod->getName(), array('route' => strtolower($mod->getModuleClass())));
                    }
                }
                if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_WEDGET') && $securityContext->isGranted('ROLE_WEBSITE')){
                    $menu['Manage Website']->addChild('Page Feature')->setAttribute('dropdown', true);
                    $menu['Manage Website']['Page Feature']->addChild('Widget', array('route' => 'appearancewebsitewidget'));
                    $menu['Manage Website']['Page Feature']->addChild('Feature', array('route' => 'appearancefeature'));
                }
            }
            $menu
                ->addChild('Media')
                ->setAttribute('icon', 'fa fa-picture-o')
                ->setAttribute('dropdown', true);

            $menu['Manage Website']->addChild('Contact', array('route' => 'contactpage_modify'));
            $menu['Media']->addChild('Galleries', array('route' => 'gallery'));
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_SETTING')) {

            $website = array_intersect($menuName, array('Website'));
            $menu
                ->addChild('Manage Appearance')
                ->setAttribute('icon', 'fa fa-cogs')
                ->setAttribute('dropdown', true);

            $menu['Manage Appearance']->addChild( 'Customize Template', array( 'route'=> 'templatecustomize_edit'));

            if($website and $option->getMainApp()->getSlug() == "website"){
                $menu['Manage Appearance']->addChild('Website')->setAttribute('dropdown', true);
                $menu['Manage Appearance']['Website']->addChild('Website Widget', array('route' => 'appearancewebsitewidget'));
                $menu['Manage Appearance']['Website']->addChild('Feature', array('route' => 'appearancefeature'));

            }
            $menu['Manage Appearance']->addChild('Website Menu', array('route' => 'menu_manage'));
            if($website) {
                $menu['Manage Appearance']->addChild('Menu Grouping', array('route' => 'menugrouping'));
            }
        }

        return $menu;
    }

    public function AccountingMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        /* @var  $globalOption GlobalOption */

        $globalOption = $securityContext->getGlobalOption();

        $modules = $globalOption->getSiteSetting()->getAppModules();
        $arrSlugs = [];
        if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
            foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $arrSlugs[] = $mod->getSlug();
                }
            }
        }

        $menu
            ->addChild('Accounting')
            ->setAttribute('icon', 'fa fa-building-o')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES') ||  ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR'))) {
	    $menu['Accounting']->addChild('Manage Sales', array('route' => ''))
	                       ->setAttribute('dropdown', true);

            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']['Manage Sales']->addChild('Sales', array('route' => 'account_sales'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES') ||  $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')) {
                $menu['Accounting']['Manage Sales']->addChild('Add Receive', array('route' => 'account_sales_new'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES_ADJUSTMENT')) {
                $menu['Accounting']['Manage Sales']->addChild('Sales Adjustment', array('route' => 'account_salesadjustment'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES_REPORT')) {
                $menu['Accounting']['Manage Sales']->addChild('Reports', array('route' => ''))->setAttribute('dropdown', true);
                $menu['Accounting']['Manage Sales']['Reports']->addChild('Outstanding', array('route' => 'report_customer_outstanding'));
                $menu['Accounting']['Manage Sales']['Reports']->addChild('Summary', array('route' => 'report_customer_summary'));
                $menu['Accounting']['Manage Sales']['Reports']->addChild('Ledger', array('route' => 'report_customer_ledger'));
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')) {
	    $menu['Accounting']->addChild('Manage Purchase', array('route' => ''))

	                       ->setAttribute('dropdown', true);
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')){
                $menu['Accounting']['Manage Purchase']->addChild('Purchase', array('route' => 'account_purchase'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')) {
                $menu['Accounting']['Manage Purchase']->addChild('Add Payment', array('route' => 'account_purchase_new'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']['Manage Purchase']->addChild('Commission', array('route' => 'account_purchasecommission'));
                if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE_REPORT')) {
                    $menu['Accounting']['Manage Purchase']->addChild('Reports', array('route' => ''))->setAttribute('dropdown', true);
                    $menu['Accounting']['Manage Purchase']['Reports']->addChild('Outstanding', array('route' => 'report_vendor_outstanding'));
                    $menu['Accounting']['Manage Purchase']['Reports']->addChild('Summary', array('route' => 'report_vendor_summary'));
                    $menu['Accounting']['Manage Purchase']['Reports']->addChild('Ledger', array('route' => 'report_vendor_ledger'));
                }
            }
	    }
	    if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')){
		    $menu['Accounting']->addChild('Bill & Expenditure', array('route' => ''))
                
		                       ->setAttribute('dropdown', true);
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE')) {
                $menu['Accounting']['Bill & Expenditure']->addChild('Expense', array('route' => 'account_expenditure'));
            }
		    if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')) {
                $menu['Accounting']['Bill & Expenditure']->addChild('Add Expense', array('route' => 'account_expenditure_new'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE')) {
                $menu['Accounting']['Bill & Expenditure']->addChild('Android Process', array('route' => 'account_expenditure_android'));
                $menu['Accounting']['Bill & Expenditure']->addChild('Expense Category', array('route' => 'expensecategory'));
                if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE_PURCHASE')) {
                    $menu['Accounting']['Bill & Expenditure']->addChild('Bill Voucher', array('route' => 'account_expense_purchase'));
                    $menu['Accounting']['Bill & Expenditure']->addChild('Account Vendor', array('route' => 'account_vendor'));
                }
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_REPORT')) {
                $menu['Accounting']['Bill & Expenditure']->addChild('Reports', array('route' => ''))->setAttribute('dropdown', true);
                $menu['Accounting']['Bill & Expenditure']['Reports']->addChild('Account Head', array('route' => 'report_expenditure_summary'));
                $menu['Accounting']['Bill & Expenditure']['Reports']->addChild('Category', array('route' => 'report_expenditure_category'));
                $menu['Accounting']['Bill & Expenditure']['Reports']->addChild('Details', array('route' => 'report_expenditure_details'));
            }

        }

        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_TRANSACTION') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_CASH')) {

            $menu['Accounting']->addChild('Cash', array('route' => ''))
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Cash']->addChild('Cash Overview', array('route' => 'account_transaction_cash_overview'));
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_RECONCILIATION')) {
                $menu['Accounting']['Cash']->addChild('Cash Reconciliation', array('route' => 'account_cashreconciliation'));
            }
            $menu['Accounting']['Cash']->addChild('Collection & Payment', array('route' => 'account_transaction_cash_summary'));
            $menu['Accounting']['Cash']->addChild('All Cash Flow', array('route' => 'account_transaction_accountcash'));
            $menu['Accounting']['Cash']->addChild('Cash Transaction', array('route' => 'account_transaction_cash'));
            $menu['Accounting']['Cash']->addChild('Bank Transaction', array('route' => 'account_transaction_bank'));
            $menu['Accounting']['Cash']->addChild('Mobile Transaction', array('route' => 'account_transaction_mobilebank'));
            $menu['Accounting']['Cash']->addChild('Reports', array('route' => ''))->setAttribute('dropdown', true);
            $menu['Accounting']['Cash']['Reports']->addChild('Purchase & Expense',array('route' => 'account_transaction_purchase_expense'));
            $menu['Accounting']['Cash']['Reports']->addChild('Monthly Cash',array('route' => 'account_transaction_monthly'));
            $menu['Accounting']['Cash']['Reports']->addChild('Yearly Cash',array('route' => 'account_transaction_yearly'));
        }
        if($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_LOAN')){
            $menu['Accounting']->addChild('Manage Loan', array('route' => 'account_loan'))
                
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Manage Loan']->addChild('Loan', array('route' => 'account_loan'));
            $menu['Accounting']['Manage Loan']->addChild('Loan New', array('route' => 'account_loan_new'));
        }
         if($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_CONDITION')){
            $menu['Accounting']->addChild('Condition Account', array('route' => 'account_condition'))
                
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Condition Account']->addChild('New Ledger Voucher', array('route' => 'account_condition_ledger_new'));
            $menu['Accounting']['Condition Account']->addChild('Condition Ledger', array('route' => 'account_condition_ledger'));
            $menu['Accounting']['Condition Account']->addChild('Condition Account', array('route' => 'account_condition'));
         }

        if($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_JOURNAL')){
            $menu['Accounting']->addChild('Journal', array('route' => 'account_transaction'))
                ->setAttribute('dropdown', true);
            $result = array_intersect($arrSlugs, array('accounting'));
            if (!empty($result)) {
                $menu['Accounting']['Journal']->addChild('Journal Voucher', array('route' => 'journal_voucher'));
            }else{
                $menu['Accounting']['Journal']->addChild('Double Entry', array('route' => 'account_double_entry'));
            }
            $menu['Accounting']['Journal']->addChild('Journal', array('route' => 'account_journal'));
            $menu['Accounting']['Journal']->addChild('Contra Account', array('route' => 'account_balancetransfer'));
            $menu['Accounting']['Journal']->addChild('Profit Withdrawal', array('route' => 'account_profit_withdrawal'));
            $menu['Accounting']['Journal']->addChild('Profit Generate', array('route' => 'account_profit'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_REPORT')) {

            $menu['Accounting']->addChild('Financial Report', array('route' => 'account_transaction'))
                
                ->setAttribute('dropdown', true);
            $accounting = array('inventory');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'report_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'));
            }
            $accounting = array('e-commerce');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'report_income'));
                /* $menu['Accounting']['Financial Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'));*/
            }
            $restaurant = array('restaurant');
            $result = array_intersect($arrSlugs, $restaurant);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Daily Income', array('route' => 'report_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income', array('route' => 'report_monthly_income'));
            }

            $accounting = array('hms');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'hms_report_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income',array('route' => 'hms_report_monthly_income'));
            }
            $accounting = array('miss');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'account_medicine_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income', array('route' => 'account_medicine_income_monthly'));
            }
            $accounting = array('business');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'account_business_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income',        array('route' => 'account_business_income_monthly'));

            }
            $accounting = array('hotel');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'account_business_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income',        array('route' => 'account_business_income_monthly'));

            }
            $menu['Accounting']['Financial Report']->addChild('Trail Balance', array('route' => 'account_trail_balance'));
            $menu['Accounting']['Financial Report']->addChild('Balance Sheet', array('route' => 'account_balance_sheet'));

        }

        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_CONFIG')) {
            $menu['Accounting']->addChild('Master Data', array('route' => ''))
                
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Master Data']->addChild('Account User', array('route' => 'account_user'));
            $menu['Accounting']['Master Data']->addChild('Bank Account', array('route' => 'accountbank'));
            $menu['Accounting']['Master Data']->addChild('Mobile Account', array('route' => 'accountmobilebank'));
            $menu['Accounting']['Master Data']->addChild('Configuration', array('route' => 'account_config_manage'));
            $menu['Accounting']['Master Data']->addChild('Account Head', array('route' => 'accounthead'));
        }

        return $menu;

    }

    public function AssetsMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $menu
            ->addChild('Office Notes')
            ->setAttribute('icon', 'icon-file')
            ->setAttribute('dropdown', true);
        $menu['Office Notes']->addChild("Office Notes List", array('route' => 'assets_officenote'));
        $menu['Office Notes']->addChild("New Office Notes", array('route' => 'assets_officenote_new'));

        $menu
            ->addChild('Procurement')
            ->setAttribute('icon', 'icon-shopping-cart')
            ->setAttribute('dropdown', true);
        $menu['Procurement']->addChild('Requisition', array('route' => 'pro_purchaserequisition'));
        $menu['Procurement']->addChild('Requisition', array('route' => 'pro_purchaserequisition_new'));
        $menu['Procurement']->addChild('Issue', array('route' => ''))->setAttribute('dropdown', true);
        $menu['Procurement']['Issue']->addChild('PO Issue', array('route' => 'pro_purchaserequisition_poissue'));
        $menu['Procurement']['Issue']->addChild('Stock Issue', array('route' => 'pro_purchaserequisition_stockissue'));
        $menu['Procurement']['Issue']->addChild('Local Purchase Issue', array('route' => 'pro_purchaserequisition_purchaseissue'));
        $menu['Procurement']->addChild("Purchase Order List", array('route' => 'pro_purchaseorder'));
        $menu['Procurement']->addChild("Item Receive", array('route' => 'pro_receive'));
        $menu['Procurement']->addChild("Receive Voucher", array('route' => 'pro_receive'));

        $menu
            ->addChild('Stock Inventory')
            ->setAttribute('icon', 'icon-archive')
            ->setAttribute('dropdown', true);
        $menu['Stock Inventory']->addChild('Stock Issue',array('route' => 'assets_stockissue'));
        $menu['Stock Inventory']->addChild('Assets',array('route' => 'assets_stockitem', 'routeParameters' => array('type' => 'Assets')));
        $menu['Stock Inventory']->addChild('Inventory',array('route' => 'assets_stockitem', 'routeParameters' => array('type' => 'Inventory')));
        $menu['Stock Inventory']->addChild('Stock Details', array('route' => 'assets_barcode_stock'));
        $menu['Stock Inventory']->addChild('Item', array('route' => 'assetsitem'));
        $menu['Stock Inventory']->addChild('Opening Stock', array('route' => 'assets_purchaseitem_new'));
        $menu['Stock Inventory']->addChild('Item Import', array('route' => 'itemstock_import'));

         $menu
            ->addChild('Master Data')
            ->setAttribute('icon', 'icon-archive')
            ->setAttribute('dropdown', true);
        $menu['Master Data']->addChild('Club/Department',array('route' => 'assets_club'));
        $menu['Master Data']->addChild('Brand',array('route' => 'assetsitembrand'));
        $menu['Master Data']->addChild('Category',array('route' => 'assetscategory'));
        $menu['Master Data']->addChild('Vendor',array('route' => 'assets_vendor'));

        $menu
            ->addChild('Fixed Assets')
            ->setAttribute('icon', 'icon-archive')
            ->setAttribute('dropdown', true);

        $menu['Fixed Assets']->addChild('Manage Asset')->setAttribute('dropdown', true);
        $menu['Fixed Assets']['Manage Asset']->addChild("All Asset", array('route' => 'assets_product'));
       /* if($user->getGlobalOption()->getAssetsConfig()->getCategories()){
            foreach ($user->getGlobalOption()->getAssetsConfig()->getParentCategories() as $category):
                $menu['Fixed Assets']['Manage Asset']->addChild("{$category->getName()}", array('route' => 'assets_product', 'routeParameters' => array('parent' => $category->getSlug())));
            endforeach;
        }*/

        $menu['Fixed Assets']->addChild('Asset Distribution')->setAttribute('dropdown', true);
        $menu['Fixed Assets']['Asset Distribution']->addChild("All Distribution", array('route' => 'assets_distribution'));
        /*if($user->getGlobalOption()->getAssetsConfig()->getCategories()){
            foreach ($user->getGlobalOption()->getAssetsConfig()->getParentCategories() as $category):
                $menu['Fixed Assets']['Asset Distribution']->addChild("{$category->getName()}", array('route' => 'assets_distribution', 'routeParameters' => array('parent' => $category->getSlug())));
            endforeach;
        }*/
        $menu['Fixed Assets']['Asset Distribution']->addChild("Add Distribution", array('route' => 'assets_distribution'));

        $menu['Fixed Assets']->addChild('Asset Ledger')->setAttribute('dropdown', true);
        $menu['Fixed Assets']['Asset Ledger']->addChild("All Ledger", array('route' => 'assets_ledger'));
        $menu['Fixed Assets']['Asset Ledger']->addChild("Product", array('route' => 'assets_ledger_product'));
        $menu['Fixed Assets']['Asset Ledger']->addChild("Item", array('route' => 'assets_ledger_item'));
        $menu['Fixed Assets']['Asset Ledger']->addChild("Category", array('route' => 'assets_ledger_category'));
        $menu['Fixed Assets']['Asset Ledger']->addChild("Branch", array('route' => 'assets_ledger_branch'));

        $menu['Fixed Assets']->addChild('Asset Maintenance')->setAttribute('dropdown', true);
/*        $menu['Fixed Assets']['Asset Maintenance']->addChild("Asset Maintenance", array('route' => 'serviceinvoice'));*/
        $menu['Fixed Assets']->addChild('Asset Disposal')->setAttribute('dropdown', true);
        $menu['Fixed Assets']['Asset Disposal']->addChild("Disposal", array('route' => 'assets_disposal'));
        $menu['Fixed Assets']['Asset Disposal']->addChild("New Disposal", array('route' => 'assets_disposal_new'));
        return $menu;

    }

    public function InventorySalesMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $inventory = $securityContext->getGlobalOption()->getInventoryConfig();
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES')) {

            $menu
            ->addChild('Sales')
            ->setAttribute('icon', 'fa fa-shopping-bag')
            ->setAttribute('dropdown', true);
            if($inventory->isPos() == 1 and $securityContext->isGranted('ROLE_POS')){
                $menu['Sales']->addChild('Pos', array('route' => 'pos_desktop'));
            }
            $deliveryProcess = $inventory->getDeliveryProcess();
            if ($inventory->isInvoice() == 1) {
                $menu['Sales']->addChild('New Invoice', array('route' => 'inventory_salesonline_new'));
            }
            $menu['Sales']->addChild('Sales', array('route' => 'inventory_salesonline'));
            if($inventory->isInvoice() == 1 and $securityContext->isGranted('ROLE_POS_ANDROID')){
                $menu['Sales']->addChild('Android Sales', array('route' => 'pos_android_sales'));
            }
           $menu['Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'));
           $menu['Sales']->addChild('Sales Import', array('route' => 'inventory_salesimport'));

            if ($securityContext->isGranted('ROLE_CRM') or $securityContext->isGranted('ROLE_DOMAIN')) {
                $menu['Sales']->addChild('Customer', array('route' => 'domain_customer'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {
                $menu['Sales']->addChild('Reports')
                    ->setAttribute('dropdown', true);
                $menu['Sales']['Reports']->addChild('Sales Overview', array('route' => 'inventory_report_sales_overview'));
                $menu['Sales']['Reports']->addChild('Sales with price', array('route' => 'inventory_report_sales'));
                $menu['Sales']['Reports']->addChild('Sales Item Details', array('route' => 'inventory_report_sales_item_details'));
                $menu['Sales']['Reports']->addChild('Daily Sales', array('route' => 'inventory_report_daily_sales'));
                $menu['Sales']['Reports']->addChild('Monthly Sales', array('route' => 'inventory_report_monthly_sales'));
                $menu['Sales']['Reports']->addChild('Daily  Sales & Profit', array('route' => 'inventory_report_daily_sales_profit'));
                $menu['Sales']['Reports']->addChild('Monthly  Sales & Profit', array('route' => 'inventory_report_monthly_sales_profit'));
                $menu['Sales']['Reports']->addChild('Sales with price', array('route' => 'inventory_report_sales'));
                $menu['Sales']['Reports']->addChild('Periodic Sales Item', array('route' => 'inventory_report_sales_item'));
                $menu['Sales']['Reports']->addChild('Sales by User', array('route' => 'inventory_report_sales_user'));
                $menu['Sales']['Reports']->addChild('User Sales Target', array('route' => 'inventory_report_sales_user_target'));
            }
        }
        return $menu;

    }

    public function InventoryMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $inventory = $securityContext->getGlobalOption()->getInventoryConfig();
        $menu
            ->addChild('Inventory')
            ->setAttribute('icon', 'icon-archive')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {

            $menu['Inventory']->addChild('Manage Purchase', array('route' => 'purchase'))
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Purchase']->addChild('Purchase', array('route' =>'inventory_purchasesimple'))
                ;
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Return', array('route' => 'inventory_purchasereturn'))
                ;
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Import', array('route' => 'inventory_excelimproter'))
                ;
            $menu['Inventory']['Manage Purchase']->addChild('Vendor', array('route' => 'inventory_vendor'));
            $menu['Inventory']['Manage Purchase']->addChild('Pre-purchase', array('route' => 'prepurchaseitem'));
	        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {
            $menu['Inventory']['Manage Purchase']->addChild('Reports')

                ->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Purchase']['Reports']->addChild('Purchase with price', array('route' => 'inventory_report_purchase'));
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_STOCK')) {

            $menu['Inventory']->addChild('Manage Stock')->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Stock']->addChild('Add Item', array('route' => 'item_new'))
                ;
            $menu['Inventory']['Manage Stock']->addChild('Stock Item', array('route' => 'inventory_item'))
                ;
            $menu['Inventory']['Manage Stock']->addChild('Category', array('route' => 'inventory_category'));
            $menu['Inventory']['Manage Stock']->addChild('Purchase Item', array('route' => 'inventory_purchaseitem'));
            $menu['Inventory']['Manage Stock']->addChild('Barcode wise Stock', array('route' => 'inventory_barcode_branch_stock'));
            $menu['Inventory']['Manage Stock']->addChild('Barcode Stock Details', array('route' => 'inventory_barcode_stock'));
            $menu['Inventory']['Manage Stock']->addChild('Stock Item Details', array('route' => 'inventory_stockitem'));
            $menu['Inventory']['Manage Stock']->addChild('Stock Short List', array('route' => 'inventory_stockitem_short_list'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER')) {
            if ($inventory->getBarcodePrint() == 1) {
                $menu['Inventory']['Manage Stock']->addChild('Barcode Print', array('route' => 'inventory_barcode'))
                    ;
            }
            $menu['Inventory']['Manage Stock']->addChild('Stock Adjustment', array('route' => 'inv_stock_adjustment'));
            $menu['Inventory']['Manage Stock']->addChild('Damage', array('route' => 'inventory_damage'));

            $menu['Inventory']->addChild('Master Data', array('route' => ''))
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Master Data']->addChild('Master Item', array('route' => 'inventory_product'));
            $menu['Inventory']['Master Data']->addChild('Item Import', array('route' => 'inventory_excelimproter'));
           // $menu['Inventory']['Master Data']->addChild('Item category', array('route' => 'itemtypegrouping_edit', 'routeParameters' => array('id' => $inventory->getId())));
            $menu['Inventory']['Master Data']->addChild('Brand', array('route' => 'itembrand'));;
            $menu['Inventory']['Master Data']->addChild('Size Group', array('route' => 'itemsize_group'));;
            if ($inventory->getIsBranch() == 1) {
                $menu['Inventory']['Master Data']->addChild('Branches')->setAttribute('icon', 'icon-building')->setAttribute('dropdown', true);
                $menu['Inventory']['Master Data']['Branches']->addChild('Branch Shop', array('route' => 'appsetting_branchshop'));
            }
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_CONFIG')) {

            $menu['Inventory']->addChild('Configuration', array('route' => 'inventoryconfig_edit'))
                ;
            $menu['Inventory']->addChild('User Sales Setup', array('route' => 'inventory_sales_user'))
                ;
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {

            $menu['Inventory']->addChild('Reports')

                ->setAttribute('dropdown', true);
            $menu['Inventory']['Reports']->addChild('System Overview', array('route' => 'inventory_report_overview'));
            $menu['Inventory']['Reports']->addChild('Item Overview', array('route' => 'inventory_report_stock_item'));
            $menu['Inventory']['Reports']->addChild('Till Stock', array('route' => 'inventory_report_till_stock'));
            $menu['Inventory']['Reports']->addChild('Periodic Stock', array('route' => 'inventory_report_periodic_stock'));
            $menu['Inventory']['Reports']->addChild('Operational Stock', array('route' => 'inventory_report_operational_stock'));
            $menu['Inventory']['Reports']->addChild('Group Stock', array('route' => 'inventory_report_group_stock'));
            $menu['Inventory']['Reports']->addChild('Purchase with price', array('route' => 'inventory_report_purchase'));

        }
        return $menu;

    }

    public function AnonymousProductSalesMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();

        $menu
            ->addChild('Service & Sales')
            ->setAttribute('icon', 'icon icon-th-large')
            ->setAttribute('dropdown', true);

        $menu['Service & Sales']->addChild('Manage Service')
            ->setAttribute('dropdown', true);
        $menu['Service & Sales']['Manage Service']->addChild('Add Item', array('route' => 'inventory_serviceitem_new'))
            ;
        $menu['Service & Sales']['Manage Service']->addChild('Service Item', array('route' => 'inventory_serviceitem'))
            ;
        $menu['Service & Sales']->addChild('Manage Sales')
            ->setAttribute('dropdown', true);
        $menu['Service & Sales']['Manage Sales']->addChild('Create Invoice', array('route' => 'inventory_servicesales_new'))
            ;
        $menu['Service & Sales']['Manage Sales']->addChild('Sales', array('route' => 'inventory_servicesales'))
            ;
        $menu['Service & Sales']->addChild('System Setting')
            ->setAttribute('dropdown', true);

        /*
                if($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_CONFIG')) {
                    $menu['Service & Sales']['System Setting']->addChild('Configuration', array('route' => 'inventoryconfig_edit'))
                        ;
                }
                $menu['Inventory']['System Setting']->addChild('Variant', array('route' => 'colorsize'))
                    ;
                $menu['Inventory']['System Setting']->addChild('Ware House', array('route' => 'inventory_warehouse'));*/
        return $menu;

    }

    public function HospitalMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $config = $securityContext->getGlobalOption()->getHospitalConfig();
        $process = $securityContext->getGlobalOption()->getHospitalConfig()->getInvoiceProcess();
        $menu
            ->addChild('Hospital & Diagnostic')
            ->setAttribute('icon', 'fa fa-hospital-o')
            ->setAttribute('dropdown', true);
        $menu['Hospital & Diagnostic']->addChild('Manage Invoice')
            ->setAttribute('dropdown', true);
        if (!empty($config) and in_array('diagnostic', $process) && $securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_OPERATOR')) {
            $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Report Delivery', array('route' => 'hms_invoice_particular'));
            $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Diagnostic', array('route' => 'hms_invoice'));
        }
        if (!empty($config) and in_array('admission', $process) && $securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_ADMISSION')) {
            $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Admission', array('route' => 'hms_invoice_admission'));
            $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Cabin/Wrad Booking', array('route' => 'hms_invoice_admission_booking'));
        }
        if (!empty($config) and in_array('visit', $process) && $securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_VISIT')) {
            $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Doctor Visit', array('route' => 'hms_prescription'));
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_MANAGER') || $securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_COMMISSION')) {
            if (!empty($config) and in_array('commission', $process)) {
                $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Commission', array('route' => 'hms_doctor_commission_invoice'));
                $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Commission Payment', array('route' => 'hms_doctor_invoice'));
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_MANAGER') or $securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_ADMISSION')) {
            if (!empty($config)) {
                $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Patient', array('route' => 'hms_customer'));
            }
        }

        if ((in_array('diagnostic', $process) && $securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_LAB')) || (in_array('diagnostic', $process) && $securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_DOCTOR'))) {

                $menu['Hospital & Diagnostic']->addChild('Pathological & Process', array('route' => 'hms_invoice_report_process'))
                ;
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_ISSUE') &&  $config->isInventory() == 1) {
            $menu['Hospital & Diagnostic']->addChild('Sales & Issue', array('route' => 'hms_sales'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_PURCHASE') &&  $config->isInventory() == 1) {
            $menu['Hospital & Diagnostic']->addChild('Manage Purchase')->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Manage Purchase']->addChild('Purchase', array('route' => 'hms_purchase'));
            $menu['Hospital & Diagnostic']['Manage Purchase']->addChild('Vendor/Supplier', array('route' => 'hms_vendor'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_STOCK') &&  $config->isInventory() == 1) {
            $menu['Hospital & Diagnostic']->addChild('Manage Stock', array('route' => 'hms_stock'));

        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_MASTERDATA')) {

                $menu['Hospital & Diagnostic']->addChild('Master Data')->setAttribute('dropdown', true);
                if(in_array('diagnostic', $process)){
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Pathology Test', array('route' => 'hms_pathology'))
                    ;
                }
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Doctor', array('route' => 'hms_doctor'))
                    ;
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Lab User', array('route' => 'hms_labuser'))
                    ;
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Referred', array('route' => 'hms_referreddoctor'))
                    ;
                if(in_array('admission', $process)) {
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Cabin/Ward', array('route' => 'hms_cabin'));
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Surgery', array('route' => 'hms_surgery'));
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Marketing Executive', array('route' => 'hms_marketing_executive'));
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Services/Procedure', array('route' => 'hms_other_service'));
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Service Group', array('route' => 'hms_service_group'));
                }
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Commission', array('route' => 'hms_commission'));
                /* $menu['Hospital & Diagnostic']['Master Data']->addChild('Category', array('route' => 'hms_category'));
                */
                if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_CONFIG')) {
                    $menu['Hospital & Diagnostic']['Master Data']->addChild('Configuration', array('route' => 'hms_config_manage'))
                        ;
                }
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_REPORT')) {
                $menu['Hospital & Diagnostic']->addChild('Reports')

                    ->setAttribute('dropdown', true);
                $menu['Hospital & Diagnostic']['Reports']->addChild('Sales Summary', array('route' => 'hms_report_sales_summary'))
                    ;
                $menu['Hospital & Diagnostic']['Reports']->addChild('Sales Details', array('route' => 'hms_report_sales_details'));
                $menu['Hospital & Diagnostic']['Reports']->addChild('Sales Discount', array('route' => 'hms_report_sales_discount_details'));
                $menu['Hospital & Diagnostic']['Reports']->addChild('Monthly Sales & Cash', array('route' => 'hms_report_monthly_cash'))
                    ;
                $menu['Hospital & Diagnostic']['Reports']->addChild('Monthly Commission', array('route' => 'hms_report_monthly_commission'))
                    ;
                $menu['Hospital & Diagnostic']['Reports']->addChild('Referred Commission', array('route' => 'hms_report_monthly_referred_commission'))
                    ;
                $menu['Hospital & Diagnostic']['Reports']->addChild('Commission Summary', array('route' => 'hms_report_commission_group'))
                    ;
                $menu['Hospital & Diagnostic']['Reports']->addChild('Service Wise Sales', array('route' => 'hms_report_sales_service'))
                    ;
            }

        return $menu;

    }

    public function DmsMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $config = $securityContext->getGlobalOption()->getHospitalConfig()->getInvoiceProcess();
        $menu
            ->addChild('Dental & Diagnosis')
            ->setAttribute('icon', 'fa fa-medkit')
            ->setAttribute('dropdown', true);

        $menu['Dental & Diagnosis']->addChild('Patient', array('route' => 'dms_invoice'))
            ;
        $menu['Dental & Diagnosis']->addChild('Treatment Schedule', array('route' => 'dms_treatment_plan'))
            ;
        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_OPERATOR')) {
            if (!empty($config) and in_array('admission', $config)) {
            $menu['Dental & Diagnosis']->addChild('Patient', array('route' => 'dms_invoice'))
                ;
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {
            if (!empty($config) and in_array('doctor', $config)) {
                $menu['Dental & Diagnosis']->addChild('Commission', array('route' => 'dms_doctor_commission_invoice'))
                    ;
            }
        }

        $menu['Dental & Diagnosis']->addChild('Expense')
            
            ->setAttribute('dropdown', true);
        $menu['Dental & Diagnosis']['Expense']->addChild('Expenditure', array('route' => 'dms_account_expenditure'))
            ;
        $menu['Dental & Diagnosis']['Expense']->addChild('Expense Category', array('route' => 'dms_expensecategory'))
            ;

        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {

            $menu['Dental & Diagnosis']->addChild('Master Data')

                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Master Data']->addChild('Treatment Plan', array('route' => 'dms_treatment'))
                ;
            $menu['Dental & Diagnosis']['Master Data']->addChild('Particular', array('route' => 'dms_particular'))
                ;
             $menu['Dental & Diagnosis']['Master Data']->addChild('Service', array('route' => 'dms_service'))
                ;
            $menu['Dental & Diagnosis']['Master Data']->addChild('Doctor', array('route' => 'dms_doctor'))
                ;
            if ($securityContext->isGranted('ROLE_DOMAIN_DMS_CONFIG')) {
                $menu['Dental & Diagnosis']['Master Data']->addChild('Configuration', array('route' => 'dms_config_manage'))
                    ;
            }
            $menu['Dental & Diagnosis']->addChild('Manage Stock')
                
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Manage Stock']->addChild('Accessories Out', array('route' => 'dms_treatment_accessories'))
                ;
            $menu['Dental & Diagnosis']['Manage Stock']->addChild('Accessories', array('route' => 'dms_medicine'))
                ;
            $menu['Dental & Diagnosis']->addChild('Purchase')
                
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Purchase']->addChild('Receive', array('route' => 'dms_purchase'))
                ;
            $menu['Dental & Diagnosis']['Purchase']->addChild('Vendor', array('route' => 'dms_vendor'));

            $menu['Dental & Diagnosis']->addChild('Sales Reports')

                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Summary', array('route' => 'dms_report_sales_summary'))
                ;
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Details', array('route' => 'dms_report_sales'))
                ;
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Monthly', array('route' => 'dms_report_sales_monthly'))
                ;
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Yearly', array('route' => 'dms_report_sales_yearly'))
                ;
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('All Sales Yearly', array('route' => 'dms_report_sales_all_yearly'))
                ;
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Treatment Base Sales', array('route' => 'dms_report_sales_treatment'))
                ;
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Purchase', array('route' => 'dms_report_purchase'))
                ;
            $menu['Dental & Diagnosis']->addChild('Accounting Report')

                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Cash in Hand', array('route' => 'dms_report_cash'))
                ;
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Expenditure', array('route' => 'dms_report_expenditure'))
                ;
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Income', array('route' => 'dms_report_income'))
                ;
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Balance Sheet', array('route' => 'dms_report_balance_sheet'))
                ;
            $menu['Dental & Diagnosis']->addChild('Stock Report')

                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Stock Report']->addChild('Accessories Stock', array('route' => 'dms_report_stock'))
                ;
            $menu['Dental & Diagnosis']['Stock Report']->addChild('Accessories Out', array('route' => 'dms_report_stock_out'))
                ;

        }
        return $menu;

    }

    public function DpsMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $mainApp = $securityContext->getGlobalOption()->getMainApp()->getModuleClass();
        $menu
            ->addChild('Doctor Prescription')
            ->setAttribute('icon', 'fa fa-medkit')
            ->setAttribute('dropdown', true);

        //if($mainApp == "Hospital" and $securityContext->isGranted('ROLE_DOMAIN_DMS_DOCTOR')){
        if($mainApp == "Hospital" and $securityContext->isGranted('ROLE_DPS_DOCTOR')){
            $menu['Doctor Prescription']->addChild('Prescription', array('route' => 'dps_prescription'));
        }else{
            $menu['Doctor Prescription']->addChild('Patient', array('route' => 'dps_invoice'));
            $menu['Doctor Prescription']->addChild('Expense')->setAttribute('dropdown', true);
            $menu['Doctor Prescription']['Expense']->addChild('Expenditure', array('route' => 'dps_account_expenditure'));
            $menu['Doctor Prescription']['Expense']->addChild('Expense Category', array('route' => 'dps_expensecategory'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {

            $menu['Doctor Prescription']->addChild('Master Data')
                ->setAttribute('dropdown', true);
            $menu['Doctor Prescription']['Master Data']->addChild('Particular', array('route' => 'dps_particular'))
                ;
            $menu['Doctor Prescription']['Master Data']->addChild('Diseases', array('route' => 'dps_diseases'))
                ;
            $menu['Doctor Prescription']['Master Data']->addChild('Service', array('route' => 'dps_service'))
                ;
            if($mainApp != "Hospital") {
                $menu['Doctor Prescription']['Master Data']->addChild('Doctor', array('route' => 'dps_doctor'))
                    ;
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_DMS_CONFIG')) {
                $menu['Doctor Prescription']['Master Data']->addChild('Configuration', array('route' => 'dps_config_manage'))
                    ;
            }

        }
        return $menu;

    }

    public function DrugMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Drug')
            
            ->setAttribute('dropdown', true);
        $menu['Drug']->addChild('Add Drug', array('route' => 'medicinebrand_new'));
        $menu['Drug']->addChild('Add Drug', array('route' => 'medicine_user'));
        if ($securityContext->isGranted('ROLE_ADMIN') OR $securityContext->isGranted('ROLE_SUPER_ADMIN')) {
        $menu['Drug']->addChild('Drug', array('route' => 'medicine'));
        }
        return $menu;
    }

    public function medicineMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Medicine')
            ->setAttribute('icon', 'icon icon-th-large')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_MEDICINE_SALES')) {
	        $menu['Medicine']->addChild('Manage Sales')

	                         ->setAttribute('dropdown', true);
        	    $menu['Medicine']['Manage Sales']->addChild('Add Sales', array('route' => 'medicine_sales_temporary_new'))
                    ;
        	    $menu['Medicine']['Manage Sales']->addChild('Sales', array('route' => 'medicine_sales'))
                    ;
        	    $menu['Medicine']['Manage Sales']->addChild('Hold Sales', array('route' => 'medicine_sales_hold'))
                    ;
                $menu['Medicine']['Manage Sales']->addChild('Android Sales', array('route' => 'medicine_sales_android'))
            ;
                $menu['Medicine']['Manage Sales']->addChild('Sales Return')->setAttribute('dropdown', true);
        	    $menu['Medicine']['Manage Sales']['Sales Return']->addChild('Add Item Return', array('route' => 'medicine_sales_item'))
                    ;
                $menu['Medicine']['Manage Sales']['Sales Return']->addChild('Item Return', array('route' => 'medicine_sales_return'));
                $menu['Medicine']['Manage Sales']['Sales Return']->addChild('Add Invoice Return', array('route' => 'medicine_sales_customer_item'));
                $menu['Medicine']['Manage Sales']['Sales Return']->addChild('Invoice Return', array('route' => 'medicine_sales_customer_return_invoice'));


                if ($securityContext->isGranted('ROLE_CRM') or $securityContext->isGranted('ROLE_DOMAIN')) {
	                $menu['Medicine']['Manage Sales']->addChild('Customer', array('route' => 'domain_customer'));
	                $menu['Medicine']['Manage Sales']->addChild('Notepad', array('route' => 'domain_notepad'));
                }
        }
	    if ($securityContext->isGranted('ROLE_MEDICINE_PURCHASE')) {

		    $menu['Medicine']->addChild('Manage Purchase')

		                     ->setAttribute('dropdown', true);
            $menu['Medicine']['Manage Purchase']->addChild('Add Purchase', array('route' => 'medicine_purchase_new'))
                                                ;
		    $menu['Medicine']['Manage Purchase']->addChild('Purchase', array('route' => 'medicine_purchase'))
		                                        ;
		    $menu['Medicine']['Manage Purchase']->addChild('Android', array('route' => 'medicine_purchase_android'))
		                                        ;
		    $menu['Medicine']['Manage Purchase']->addChild('Instant Purchase', array('route' => 'medicine_instantpurchase'))
		                                        ;
		    $menu['Medicine']['Manage Purchase']->addChild('Purchase Return', array('route' => 'medicine_purchase_return'))
		                                        ;
		    $menu['Medicine']['Manage Purchase']->addChild('Pre-purchase', array('route' => 'medicine_prepurchase'))
		                                        ;
		    $menu['Medicine']['Manage Purchase']->addChild('Vendor/Supplier', array('route' => 'medicine_vendor'));

	    }


	    if ($securityContext->isGranted('ROLE_MEDICINE_STOCK')) {

		    $menu['Medicine']->addChild('Manage Stock')

	                             ->setAttribute('dropdown', true);
                $menu['Medicine']['Manage Stock']->addChild('Stock Item', array('route' => 'medicine_stock'))
                    ;
                $menu['Medicine']['Manage Stock']->addChild('Stock Item Details', array('route' => 'medicine_purchase_item'))
                    ;
                $menu['Medicine']['Manage Stock']->addChild('Stock Item History', array('route' => 'medicine_stock_item_history'))
                    ;
                $menu['Medicine']['Manage Stock']->addChild('Medicine Expiration', array('route' => 'medicine_expiry_item'))
                    ;
                $menu['Medicine']['Manage Stock']->addChild('Current Short List', array('route' => 'medicine_stock_current_short_item'));
                $menu['Medicine']['Manage Stock']->addChild('Short List', array('route' => 'medicine_stock_short_item'))
                    ;
	            if ($securityContext->isGranted('ROLE_MEDICINE_MANAGER')) {
                    $menu['Medicine']['Manage Stock']->addChild( 'Medicine Transfer', array( 'route' => 'medicine_transfer' ) )
                        ;
                }
	            if ($securityContext->isGranted('ROLE_MEDICINE_MANAGER')) {
		            $menu['Medicine']['Manage Stock']->addChild( 'Stock House', array( 'route' => 'medicine_stockhouse' ) )
		                                             ;
	                $menu['Medicine']['Manage Stock']->addChild( 'Stock Adjustment', array( 'route' => 'stock_adjustment' ) )
		                                             ;
	                $menu['Medicine']['Manage Stock']->addChild( 'Damage', array( 'route' => 'medicine_damage' ) );
	                $menu['Medicine']['Manage Stock']->addChild( 'Import', array( 'route' => 'medicinestock_import' ) )
		                                             ;
	            }
            if ($securityContext->isGranted('ROLE_MEDICINE_MANAGER')) {
            $menu['Medicine']->addChild('Purchase & Sales')
                ->setAttribute('dropdown', true);
            $menu['Medicine']['Purchase & Sales']->addChild('Vendor Accounts', array('route' => 'medicine_report_vendor_sales'));
            $menu['Medicine']['Purchase & Sales']->addChild('Vendor Medicine', array('route' => 'medicine_report_vendor_sales_medicine'));
            }
        }
        if ($securityContext->isGranted('ROLE_MEDICINE_ADMIN')) {
            $menu['Medicine']->addChild('Master Data')->setAttribute('dropdown', true);
            $menu['Medicine']['Master Data']->addChild('Setting', array('route' => 'medicine_particular'))
                ;
            $menu['Medicine']['Master Data']->addChild('Minimum Stock Setup', array('route' => 'medicine_minimum'))
                ;
            $menu['Medicine']['Master Data']->addChild('User Sales Setup', array('route' => 'medicine_sales_user', 'routeParameters' => array('source' => 'medicine')))
                ;
            $menu['Medicine']['Master Data']->addChild('Configuration', array('route' => 'medicine_config_manage'))
                ;
            $menu['Medicine']['Master Data']->addChild('New Medicine', array('route' => 'medicine_user'))
                ;
        }
        if ($securityContext->isGranted('ROLE_MEDICINE_REPORT')) {

                $menu['Medicine']->addChild('Reports')
                    
                    ->setAttribute('dropdown', true);
	        $menu['Medicine']['Reports']->addChild('System Overview', array('route' => 'medicine_system_overview'))
	                                    ;
            $menu['Medicine']['Reports']->addChild('Top Sales Item', array('route' => 'medicine_report_top_sales_item'));
            $menu['Medicine']['Reports']->addChild('Low Sales Item', array('route' => 'medicine_report_low_sales_item'));
	        $menu['Medicine']['Reports']->addChild('Sales')
                    
                    ->setAttribute('dropdown', true);
                $menu['Medicine']['Reports']['Sales']->addChild('Sales Summary', array('route' => 'medicine_report_sales_summary'))
                    ;
	            $menu['Medicine']['Reports']['Sales']->addChild('Daily Sales Chart', array('route' => 'medicine_report_daily_hourly_sales'))
                    ;
	            $menu['Medicine']['Reports']['Sales']->addChild('Monthly Sales Chart', array('route' => 'medicine_report_monthly_sales_purchase'))
                    ;
	            $menu['Medicine']['Reports']['Sales']->addChild('Sales Details', array('route' => 'medicine_report_sales_details'))
                    ;
	            $menu['Medicine']['Reports']['Sales']->addChild('Sales Daily', array('route' => 'medicine_report_sales_daily'))
                    ;
	            $menu['Medicine']['Reports']['Sales']->addChild('Vendor base Sales', array('route' => 'medicine_report_sales_vendor_customer'));
	            $menu['Medicine']['Reports']['Sales']->addChild('Product Wise Sales', array('route' => 'medicine_report_sales_stock'))
                    ;
	            $menu['Medicine']['Reports']['Sales']->addChild('User Wise Sales', array('route' => 'medicine_report_sales_user'))
                    ;
	            $menu['Medicine']['Reports']['Sales']->addChild('User Monthly Sales', array('route' => 'medicine_report_sales_user_monthly'))
                    ;
	            $menu['Medicine']['Reports']->addChild('Purchase')
	                                        
	                                        ->setAttribute('dropdown', true);
                $menu['Medicine']['Reports']['Purchase']->addChild('Purchase Summary', array('route' => 'medicine_report_purchase_summary'))
                    ;
                $menu['Medicine']['Reports']['Purchase']->addChild('Purchase Item', array('route' => 'medicine_report_purchase_item'));
                $menu['Medicine']['Reports']['Purchase']->addChild('Vendor Ledger', array('route' => 'medicine_report_purchase_vendor'));
                $menu['Medicine']['Reports']['Purchase']->addChild('Vendor Details', array('route' => 'medicine_report_purchase_vendor_details'));
	            $menu['Medicine']['Reports']['Purchase']->addChild('Vendor Stock', array('route' => 'medicine_report_product_stock_sales'));
		        $menu['Medicine']['Reports']['Purchase']->addChild('Purchase Wise Sales', array('route' => 'medicine_report_purchase_stock'));
	            $menu['Medicine']['Reports']->addChild('Brand', array('route' => 'medicine_report_purchase_brand_sales'));
	            $menu['Medicine']['Reports']->addChild('Stock Adjustment', array('route' => 'stock_adjustment_summary'));
	            $menu['Medicine']['Reports']->addChild('Remaining Stock', array('route' => 'medicine_report_remaining_stock'));
	            $menu['Medicine']['Reports']->addChild('Brand Stock Price', array('route' => 'medicine_report_brand_stock'));
	            $menu['Medicine']['Reports']->addChild('Brand Details', array('route' => 'medicine_report_purchase_brand_sales_details'));

         }

            return $menu;

    }

    public function RestaurantMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Restaurant')
            ->setAttribute('icon', 'icon icon-th-large')
            ->setAttribute('dropdown', true);

        $menu['Restaurant']->addChild('Point of Sales ', array('route' => 'restaurant_invoice_new'));
        $menu['Restaurant']->addChild('Manage Sales', array('route' => 'restaurant_invoice'))
            ;
        $menu['Restaurant']->addChild('Android Sales', array('route' => 'restaurant_invoice_android'))
            ;
        $menu['Restaurant']->addChild('Customer', array('route' => 'restaurant_customer'));
        if ($securityContext->isGranted('ROLE_DOMAIN_RESTAURANT_MANAGER')) {

            $menu['Restaurant']->addChild('Manage Purchase')
                ->setAttribute('dropdown', true);
            $menu['Restaurant']['Manage Purchase']->addChild('Purchase', array('route' => 'restaurant_purchase'))
            ;
            $menu['Restaurant']['Manage Purchase']->addChild('Vendor', array('route' => 'restaurant_vendor'));

            $menu['Restaurant']->addChild('Manage Stock')
            
            ->setAttribute('dropdown', true);
            $menu['Restaurant']['Manage Stock']->addChild('Stock Item', array('route' => 'restaurant_stock'))  ;
            $menu['Restaurant']['Manage Stock']->addChild('Stock Import', array('route' => 'restaurant_itemimporter'))  ;
            $menu['Restaurant']['Manage Stock']->addChild('Item Damage', array('route' => 'restaurant_damage'))
            ;
            $menu['Restaurant']['Manage Stock']->addChild('Pre-production', array('route' => 'restaurant_production'))
                ;

            $menu['Restaurant']->addChild('Master Data')

                ->setAttribute('dropdown', true);
            $menu['Restaurant']['Master Data']->addChild('Product Sorting', array('route' => 'restaurant_product_sorting'))
                ;
            $menu['Restaurant']['Master Data']->addChild('Category', array('route' => 'restaurant_category'))
                ;
            $menu['Restaurant']['Master Data']->addChild('Particular', array('route' => 'restaurant_particular'))
                ;
            $menu['Restaurant']['Master Data']->addChild('Configuration', array('route' => 'restaurant_config_manage'))
                ;
        $menu['Restaurant']->addChild('Reports')

            ->setAttribute('dropdown', true);
        $menu['Restaurant']['Reports']->addChild('Sales Summary', array('route' => 'restaurant_report_sales_summary'))
            ;
        /*
        $menu['Restaurant']['Reports']->addChild('Purchase Summary', array('route' => 'restaurant_report_purchase_summary'))
            ;
        $menu['Restaurant']['Reports']->addChild('Sales Details', array('route' => 'restaurant_report_sales_details'))
            ;*/
        $menu['Restaurant']['Reports']->addChild('User Wise Sales', array('route' => 'restaurant_report_sales_user'))
            ;
        $menu['Restaurant']['Reports']->addChild('Product Wise Sales', array('route' => 'restaurant_report_sales_product'))
            ;
        $menu['Restaurant']['Reports']->addChild('Stock Wise Sales', array('route' => 'restaurant_report_sales_product'))
            ;
 /*       $menu['Restaurant']['Reports']->addChild('Stock Summary', array('route' => 'restaurant_report_stock'))
            ;*/
        }
        return $menu;

    }

    public function ClientRelationManagementMenu($menu)
    {
        $menu
            ->addChild('CRM')
            ->setAttribute('dropdown', true);
        $menu['CRM']->addChild('People')->setAttribute('dropdown', true);
        $menu['CRM']['People']->addChild('Sms', array('route' => 'domain_customer_sms'));
        $menu['CRM']['People']->addChild('Email', array('route' => 'domain_customer_email'));
        $menu['CRM']->addChild('Promotion')->setAttribute('icon', 'icon-trello')->setAttribute('dropdown', true);
        $menu['CRM']['Promotion']->addChild('SMS', array('route' => 'domain_customer'));
        $menu['CRM']['Promotion']->addChild('Email', array('route' => 'domain_customer'));
        $menu['CRM']->addChild('Staff')->setAttribute('icon', 'icon-foursquare')->setAttribute('dropdown', true);
        $menu['CRM']['Staff']->addChild('SMS', array('route' => 'domain_customer'));
        $menu['CRM']['Staff']->addChild('Email', array('route' => 'domain_customer'));
        $menu['CRM']->addChild('Inbox')->setAttribute('dropdown', true);
        $menu['CRM']['Inbox']->addChild('SMS', array('route' => 'domain_customer'));
        $menu['CRM']['Inbox']->addChild('Email', array('route' => 'domain_customer'));
        return $menu;

    }

	public function ReservationMenu($menu)
	{

	    $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $config = $securityContext->getGlobalOption()->getHotelConfig();

        $menu
			->addChild('Hotel & Restaurant')
			->setAttribute('icon', 'fa fa-hotel')
			->setAttribute('dropdown', true);

		if ($securityContext->isGranted('ROLE_HOTEL_INVOICE')) {

			$menu['Hotel & Restaurant']->addChild('Hotel')->setAttribute('icon', 'fa fa-bed')->setAttribute('dropdown', true);
			$menu['Hotel & Restaurant']['Hotel']->addChild('Create Invoice', array('route' => 'hotel_invoice_new'))
			                                         ;
			$menu['Hotel & Restaurant']['Hotel']->addChild('Invoice', array('route' => 'hotel_invoice'))
			                           ;
            if($config->getInvoiceForRestaurant() == 1) {
                $menu['Hotel & Restaurant']->addChild('Restaurant')->setAttribute('icon', 'icon icon-food')->setAttribute('dropdown', true);
                $menu['Hotel & Restaurant']['Restaurant']->addChild('Create Invoice', array('route' => 'hotel_restaurantinvoice_new'))
                ;
                $menu['Hotel & Restaurant']['Restaurant']->addChild('Invoice', array('route' => 'hotel_restaurantinvoice'));

            }


		}

		if ($securityContext->isGranted('ROLE_CRM') or $securityContext->isGranted('ROLE_DOMAIN')) {
			$menu['Hotel & Restaurant']->addChild('Notepad', array('route' => 'domain_notepad'));
			$menu['Hotel & Restaurant']->addChild('Customer', array('route' => 'domain_customer'));
		}
        if($config->getInvoiceForRestaurant() == 1) {
            if ($securityContext->isGranted('ROLE_HOTEL_PURCHASE')) {

                $menu['Hotel & Restaurant']->addChild('Purchase')->setAttribute('dropdown', true);
                $menu['Hotel & Restaurant']['Purchase']->addChild('Purchase', array('route' => 'hotel_purchase'));
                $menu['Hotel & Restaurant']['Purchase']->addChild('Vendor', array('route' => 'hotel_vendor'));
            }

            if ($securityContext->isGranted('ROLE_HOTEL_STOCK')) {
                $menu['Hotel & Restaurant']->addChild('Manage Stock', array('route' => 'hotel_stock'));
                $menu['Hotel & Restaurant']->addChild('Manage Damage', array('route' => 'hotel_damage'));
            }
        }

		if ($securityContext->isGranted('ROLE_HOTEL_ADMIN')) {
			$menu['Hotel & Restaurant']->addChild('Master Data')->setAttribute('dropdown', true);
			$menu['Hotel & Restaurant']['Master Data']->addChild('Room', array('route' => 'hotel_room'));
			$menu['Hotel & Restaurant']['Master Data']->addChild('Category', array('route' => 'hotel_category'));
			$menu['Hotel & Restaurant']['Master Data']->addChild('Option', array('route' => 'hotel_option'));
			$menu['Hotel & Restaurant']['Master Data']->addChild('Configuration', array('route' => 'hotel_config_manage'));
		}
		if ($securityContext->isGranted('ROLE_HOTEL_REPORT')) {
			$menu['Hotel & Restaurant']->addChild( 'Reports' )

			                           ->setAttribute( 'dropdown', true );
			$menu['Hotel & Restaurant']['Reports']->addChild( 'Sales' )
			                                      ->setAttribute( 'dropdown', true );
			$menu['Hotel & Restaurant']['Reports']['Sales']->addChild( 'Sales Summary', array( 'route' => 'hotel_report_sales_summary' ) )
			                                               ;
			$menu['Hotel & Restaurant']['Reports']['Sales']->addChild( 'Sales Details', array( 'route' => 'hotel_report_sales_details' ) )
			                                               ;
			$menu['Hotel & Restaurant']['Reports']['Sales']->addChild( 'Customer Sales', array( 'route' => 'hotel_report_customer_sales_item' ) )
			                                               ;
			$menu['Hotel & Restaurant']['Reports']['Sales']->addChild( 'Product Wise Sales', array( 'route' => 'hotel_report_sales_stock' ) )
			                                               ;
			$menu['Hotel & Restaurant']['Reports']->addChild( 'Purchase' )
			                                      ->setAttribute( 'dropdown', true );
			$menu['Hotel & Restaurant']['Reports']['Purchase']->addChild( 'Purchase Summary', array( 'route' => 'hotel_report_purchase_summary' ) )
			                                                  ;
			$menu['Hotel & Restaurant']['Reports']['Purchase']->addChild( 'Vendor Ledger', array( 'route' => 'hotel_report_purchase_vendor' ) );
		}
		return $menu;

	}

	public function ElectionMenu($menu)
	{

		$securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

		$menu
			->addChild('Election')
			->setAttribute('icon', 'icon-briefcase')
			->setAttribute('dropdown', true);
		$menu['Election']->addChild('Manage Election')
		                           
		                           ->setAttribute('dropdown', true);
		if ($securityContext->isGranted('ROLE_ELECTION_OPERATOR')) {
			$menu['Election']['Manage Election']->addChild( 'Election Setup', array( 'route' => 'election_setup' ) );
			$menu['Election']['Manage Election']->addChild( 'Candidate', array( 'route' => 'election_candidate' ) );
			$menu['Election']['Manage Election']->addChild( 'Committee', array( 'route' => 'election_committee' ) );
			$menu['Election']['Manage Election']->addChild( 'Vote Center', array( 'route' => 'election_votecenter' ) );
			/*		$menu['Election']->addChild('Organization')
												 
												 ->setAttribute('dropdown', true);
					$menu['Election']['Committee']->addChild('Committee Setup', array('route' => 'election_organizationcommittee'));*/
			$menu['Election']->addChild( 'Members', array( 'route' => 'election_member' ) );
			$menu['Election']->addChild( 'Voters', array( 'route' => 'election_voter' ) );
			$menu['Election']->addChild( 'Campaign', array( 'route' => 'election_event' ) );
			$menu['Election']->addChild( 'Campaign Analysis', array( 'route' => 'election_campaign' ) );
		}
		if ($securityContext->isGranted('ROLE_ELECTION_MANAGER')) {
			$menu['Election']->addChild( 'Manage SMS', array( 'route' => 'election_sms' ) );
		}
		if ($securityContext->isGranted('ROLE_ELECTION_ADMIN')) {
			$menu['Election']->addChild( 'Master Data' )

			                             ->setAttribute( 'dropdown', true );
			$menu['Election']['Master Data']->addChild( 'Setting Option', array( 'route' => 'election_particular' ) );
			$menu['Election']['Master Data']->addChild( 'Setting Location', array( 'route' => 'election_location' ) );
			$menu['Election']['Master Data']->addChild( 'Member Import', array( 'route' => 'election_member_import' ) );
			$menu['Election']['Master Data']->addChild( 'Configuration', array( 'route' => 'election_config_manage' ) );
		}
		if ($securityContext->isGranted('ROLE_ELECTION_REPORT')) {
			$menu['Election']->addChild( 'Report' )
			                             ->setAttribute( 'dropdown', true );
			$menu['Election']['Report']->addChild( 'Vote Center', array( 'route' => 'election_report_vote_center' ) );
			$menu['Election']['Report']->addChild( 'Union Base Center', array( 'route' => 'election_report_union_base_vote_center' ) );
			$menu['Election']['Report']->addChild( 'Vote Center Details', array( 'route' => 'election_report_voter_cenetr_details' ));
			$menu['Election']['Report']->addChild( 'Member List', array( 'route' => 'election_report_member_list' ));
		}
		if ($securityContext->isGranted('ROLE_ELECTION_ADMIN')) {
			$menu['Election']->addChild( 'Accounting' )

			                             ->setAttribute( 'dropdown', true );
			$menu['Election']['Accounting']->addChild( 'Expense', array( 'route' => 'election_account_expenditure' ) );
			$menu['Election']['Accounting']->addChild( 'Expense Category', array( 'route' => 'election_expensecategory' ) );
			$menu['Election']['Accounting']->addChild( 'Expense Report', array( 'route' => 'election_account_expenditure_report' ) );
		}
		return $menu;

	}

    public function CommitteeMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Committee')
            ->setAttribute('icon', 'icon-briefcase')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_ELECTION_OPERATOR')) {
              $menu['Committee']->addChild( 'Committee', array( 'route' => 'political_committee' ) );
            $menu['Committee']->addChild( 'Members', array( 'route' => 'election_member' ) );
            $menu['Committee']->addChild( 'Campaign', array( 'route' => 'election_event' ) );
            $menu['Committee']->addChild( 'Campaign Analysis', array( 'route' => 'election_campaign' ) );
        }
        if ($securityContext->isGranted('ROLE_ELECTION_MANAGER')) {
            $menu['Committee']->addChild( 'Manage SMS', array( 'route' => 'election_sms' ) );
        }
        if ($securityContext->isGranted('ROLE_ELECTION_ADMIN')) {
            $menu['Committee']->addChild( 'Master Data' )
                ->setAttribute( 'dropdown', true );
            $menu['Committee']['Master Data']->addChild( 'Setting Option', array( 'route' => 'election_particular' ) );
            $menu['Committee']['Master Data']->addChild( 'Setting Location', array( 'route' => 'election_location' ) );
            $menu['Committee']['Master Data']->addChild( 'Member Import', array( 'route' => 'election_member_import' ) );
            $menu['Committee']['Master Data']->addChild( 'Configuration', array( 'route' => 'election_config_manage' ) );
        }
        if ($securityContext->isGranted('ROLE_ELECTION_REPORT')) {
            $menu['Committee']->addChild( 'Report' )
                ->setAttribute( 'dropdown', true );
            $menu['Committee']['Report']->addChild( 'Vote Center', array( 'route' => 'election_report_vote_center' ) );
            $menu['Committee']['Report']->addChild( 'Union Base Center', array( 'route' => 'election_report_union_base_vote_center' ) );
            $menu['Committee']['Report']->addChild( 'Vote Center Details', array( 'route' => 'election_report_voter_cenetr_details' ));
            $menu['Committee']['Report']->addChild( 'Member List', array( 'route' => 'election_report_member_list' ));
        }
        if ($securityContext->isGranted('ROLE_ELECTION_ADMIN')) {
            $menu['Committee']->addChild( 'Accounting' )
                ->setAttribute( 'dropdown', true );
            $menu['Committee']['Accounting']->addChild( 'Expense', array( 'route' => 'election_account_expenditure' ) );
            $menu['Committee']['Accounting']->addChild( 'Expense Category', array( 'route' => 'election_expensecategory' ) );
            $menu['Committee']['Accounting']->addChild( 'Expense Report', array( 'route' => 'election_account_expenditure_report' ) );
        }
        return $menu;

    }

    public function InstituteSystemMenu($menu)
    {
        $menu
            ->addChild('IMS')
            
            ->setAttribute('dropdown', true);
        return $menu;

    }

    public function BillingSystemMenu($menu)
    {
        $menu
            ->addChild('Billing System')
            
            ->setAttribute('dropdown', true);
        return $menu;

    }

    public function PayrollMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $global = $securityContext->getGlobalOption();
        $menu
            ->addChild('HR & Payroll')
            ->setAttribute('icon', 'icon-user')
            ->setAttribute('dropdown', true);
        if($global->getIsBranch() == 1) {
            $menu['HR & Payroll']->addChild('Branch', array('route' => 'domain_branches'));
        }
        if ($securityContext->isGranted('ROLE_HR_EMPLOYEE')) {
            $menu['HR & Payroll']->addChild('System Users', array('route' => 'domain_user'));
        }
        if ($securityContext->isGranted('ROLE_HR_ATTENDANCE')) {
            $menu['HR & Payroll']->addChild('Attendance')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Attendance']->addChild('Daily Sheet', array('route' => 'attendance'));
            $menu['HR & Payroll']['Attendance']->addChild('Daily Attendance', array('route' => 'daily_attendance'));
            $menu['HR & Payroll']['Attendance']->addChild('Calendar Weekend', array('route' => 'weekend'));
        }
        if ($securityContext->isGranted('ROLE_PAYROLL_SALARY')) {

            $menu['HR & Payroll']->addChild('Payroll')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Payroll']->addChild('Salary Transaction', array('route' => 'account_paymentsalary'));
            $menu['HR & Payroll']['Payroll']->addChild('Payroll Generate', array('route' => 'payroll'));
            $menu['HR & Payroll']['Payroll']->addChild('Manage Employee', array('route' => 'employee_payroll'));

        }

        if ($securityContext->isGranted('ROLE_PAYROLL_SETTING')) {

            $menu['HR & Payroll']->addChild('Setting')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Setting']->addChild('Payroll Setting', array('route' => 'payrollsetting'));
            $menu['HR & Payroll']['Setting']->addChild('Department', array('route' => 'hrdepartment'));
            $menu['HR & Payroll']['Setting']->addChild('Leave Setup', array('route' => 'leave_setup'));
            $menu['HR & Payroll']['Setting']->addChild('Leave Policy', array('route' => 'leavepolicy'));

        }

        /*if ($securityContext->isGranted('ROLE_ADMIN')) {

            $menu['HR & Payroll']->addChild('Manage Agent')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Manage Agent']->addChild('Agent New', array('route' => 'agent_new'));
            $menu['HR & Payroll']['Manage Agent']->addChild('Agent', array('route' => 'agent'));
            $menu['HR & Payroll']->addChild('Agent Payroll')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Agent Payroll']->addChild('Agent Transaction', array('route' => 'agentpayment'));
            $menu['HR & Payroll']['Agent Payroll']->addChild('Agent Invoice', array('route' => 'agentpayment_invoice'));
        }*/
        return $menu;

    }

    public function manageDomainInvoiceMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Invoice Sms & Email')
            ->setAttribute('icon', 'fa fa-phone')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_SMS_MANAGER')) {
            $menu['Invoice Sms & Email']->addChild('Manage Sms')->setAttribute('dropdown', true);
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Logs', array('route' => 'smssender'));
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'));
            $menu['Invoice Sms & Email']->addChild('Invoice Application', array('route' => 'invoicemodule_domain'));
        }
        if ($securityContext->isGranted('ROLE_SMS_BULK')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Bulk Sms', array('route' => 'smsbulk'));
        }
        if ($securityContext->isGranted('ROLE_SMS_CONFIG')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Notification Setup', array('route' => 'domain_notificationconfig'));
        }
        return $menu;
    }

    public function manageSystemAccountMenu($menu)
    {
        $menu
            ->addChild('System Transaction')
            ->setAttribute('icon', 'fa fa-money')
            ->setAttribute('dropdown', true);
        $menu['System Transaction']->addChild('Bank', array('route' => 'bankaccount'));
        $menu['System Transaction']->addChild('Mobile Bank', array('route' => 'mobilebankaccount'));
        return $menu;
    }

    public function manageDomainMenu($menu)
    {
        $menu
            ->addChild('Manage Domain')
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('dropdown', true);
        $menu['Manage Domain']->addChild('Domain Operation', array('route' => 'tools_domain'));
	    $menu['Manage Domain']->addChild('Service Pricing')->setAttribute('dropdown', true);
	    $menu['Manage Domain']['Service Pricing']->addChild('Application', array('route' => 'applicationpricing'));
	    $menu['Manage Domain']['Service Pricing']->addChild('SMS/Email', array('route' => 'smspricing'));
/*
	    $menu['Manage Domain']->addChild('Manage Invoice')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Manage Invoice']->addChild('Customer Invoice', array('route' => 'invoicemodule'));
        $menu['Manage Domain']['Manage Invoice']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'));*/

        return $menu;
    }

    public function manageFrontendMenu($menu)
    {
        $menu
            ->addChild('Manage Frontend')
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('dropdown', true);
        $menu['Manage Frontend']->addChild('Site Slider', array('route' => 'siteslider'));
        $menu['Manage Frontend']->addChild('Site Content', array('route' => 'sitecontent'));
        $menu['Manage Frontend']->addChild('Testimonial', array('route' => 'sitetestimonial'));
        $menu['Manage Frontend']->addChild('Team', array('route' => 'siteteam'));
        $menu['Manage Frontend']->addChild('Manage Mega Menu', array('route' => 'megamenu'));
        $menu['Manage Frontend']->addChild('Feature Category', array('route' => 'category_sorting'));
        $menu['Manage Frontend']->addChild('Collection', array('route' => 'collection'));
        return $menu;

    }

    public function toolsMenu($menu)
    {
        $menu
            ->addChild('Tools')
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('dropdown', true);

/*	    $menu['Tools']->addChild('Manage Option', array('route' => 'globaloption'));
        $menu['Tools']->addChild('Manage Setting', array('route' => 'sitesetting'));*/
	    /*$menu['Tools']->addChild('Course', array('route' => 'course'));
        $menu['Tools']->addChild('Institute Level', array('route' => 'institutelevel'));*/
	    $menu['Tools']->addChild('Software')->setAttribute('dropdown', true);
	    $menu['Tools']['Software']->addChild('Application ', array('route' => 'appmodule'));
	    $menu['Tools']['Software']->addChild('Application Testimonial', array('route' => 'applicationtestimonial'));
	    //$menu['Tools']->addChild('Syndicate Module', array('route' => 'syndicatemodule'));
	    $menu['Tools']->addChild('Master Data')->setAttribute('dropdown', true);
	    $menu['Tools']['Master Data']->addChild('Location', array('route' => 'location'));
	    $menu['Tools']['Master Data']->addChild('Business Sector', array('route' => 'syndicate'));
	    $menu['Tools']['Master Data']->addChild('Designation', array('route' => 'designation'));
	    $menu['Tools']['Master Data']->addChild('Medicine Import', array('route' => 'medicine_import'));
	    $menu['Tools']['Master Data']->addChild('Medicine', array('route' => 'medicinebrand'));
		$menu['Tools']->addChild('Appearance')->setAttribute('dropdown', true);
	    $menu['Tools']['Appearance']->addChild('Module', array('route' => 'module'));
        $menu['Tools']['Appearance']->addChild('Theme', array('route' => 'theme'));
        $menu['Tools']['Appearance']->addChild('Menu Custom', array('route' => 'menucustom'));
       // $menu['Tools']->addChild('Menu Group', array('route' => 'menugroup'));
       // $menu['Tools']->addChild('Manage Brand', array('route' => 'branding'));
        /*    $menu['Tools']->addChild('Inventory&Accounting')
                ->setAttribute('icon','icon icon-reorder')
                ->setAttribute('dropdown', true);
            $menu['Tools']['Inventory&Accounting']->addChild('Color', array('route' => 'color'));
            $menu['Tools']['Inventory&Accounting']->addChild('Size', array('route' => 'size'));
            $menu['Tools']['Inventory&Accounting']->addChild('Account Head', array('route' => 'accounthead'))->setAttribute('icon','fa fa-money');*/

        return $menu;
    }

    public function syndicateMenu($menu)
    {
        $menu
            ->addChild('Syndicate')
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('dropdown', true);

        $menu['Syndicate']->addChild('Education', array('route' => 'education'));
        $menu['Syndicate']->addChild('Vendor', array('route' => 'vendor'));
        return $menu;
    }

    public function productCategoryMenu($menu)
    {
        $menu
            ->addChild('Product Category')
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('dropdown', true);

        $menu['Product Category']->addChild('Add Category', array('route' => 'category_new'));
        $menu['Product Category']->addChild('Listing', array('route' => 'category'));
        return $menu;
    }
    public function manageApplicationSettingMenu($menu)
    {
        $menu
            ->addChild('Application Setting')
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('dropdown', true);
        $menu['Application Setting']->addChild('Account Head', array('route' => 'accounthead'));
        $menu['Application Setting']->addChild('Transaction Method', array('route' => 'transactionmethod_new'));
        $menu['Application Setting']->addChild('Color', array('route' => 'color'));
        $menu['Application Setting']->addChild('Size', array('route' => 'size'));
        $menu['Application Setting']->addChild('Hospital Category', array('route' => 'hms_category'));
        return $menu;

    }

    public function appearanceMenu($menu)
    {


    }

    public function manageAdvertismentMenu($menu)
    {
        $menu
            ->addChild('Manage Advertisment')
            
            ->setAttribute('dropdown', true);

        $menu['Manage Advertisment']->addChild('Advertisment', array('route' => 'advertisment'));

        return $menu;

    }

    public function footerMenu(FactoryInterface $factory, array $options)
    {

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', '');
        $grouping = $this->container->get('doctrine')->getRepository('SettingAppearanceBundle:MenuGrouping')->getFooterMenu();
        if ($grouping) {
            foreach ($grouping as $row) {

                $menu
                    ->addChild($row->getMenu()->getMenu(), array(
                        'route' => 'frontend_page',
                        'routeParameters' => array('slug' => $row->getMenu()->getMenuSlug())
                    ));

            }
        }
        return $menu;
    }

    public function categoryMenu(FactoryInterface $factory, array $options)
    {

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'list-group margin-bottom-25 sidebar-menu');

        $this->buildChildMenus($menu, $this->getCategoryList());

        return $menu;

    }

    public function megaMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menus = $this->container->get('doctrine')->getRepository('SettingAppearanceBundle:MegaMenu')->getActiveMenus();
        $categoryRepository = $this->container->get('doctrine')->getRepository('ProductProductBundle:Category');
        foreach ($menus as $item) {
            /** @var MegaMenu $item */
            $menuName = $item->getName();
            $menu
                ->addChild($menuName)
                ->setAttribute('dropdown', true);
            $this->buildChildMenus($menu[$menuName], $categoryRepository->buildCategoryGroup($item->getCategories()));
            $this->buildCollectionMenu($menu[$menuName], $item->getCollections());
            $this->buildBrandMenu($menu[$menuName], $item->getBrands());
        }

        return $menu;
    }



    private function buildDomainCategoryMenus(ItemInterface $menu, $categories)
    {

        foreach ($categories as $category) {

            /** var Category $category */
            $categoryName = $category['name'];

            if (!empty($categoryName)) {

                $menu
                    ->addChild($categoryName, array('route' => 'webservice_product_category',
                        'routeParameters' => array('id' => $category['id'])
                    ))
                    ->setAttribute('icon', 'fa fa-angle-right');

                if (!empty($category['__children'])) {
                    $menu->setAttribute('dropdown', true);
                    $menu[$categoryName]->setChildrenAttribute('class', 'dropdown-menu');
                    $this->buildDomainCategoryMenus($menu[$categoryName], $category['__children']);
                }
            }
        }
    }

    private function buildDomainBrandMenu(ItemInterface $menu, $brands)
    {
        $menu
            ->addChild('brands')
            ->setAttribute('brands', true)
            ->setAttribute('class', 'col-md-12 nav-brands');
        foreach ($brands as $brand) {
            /** @var Branding $brand */
            $menu['brands']->addChild($brand->getName(), array('route' => 'webservice_product_brand',
                'routeParameters' => array('id' => $brand->getId())
            ))
                ->setAttribute('brand', true)
                ->setAttribute('icon', 'fa fa-angle-right');

        }
    }

    private function buildDomainPromotionMenu(ItemInterface $menu, $collections)
    {

        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getSlug())
                ));
            }
        }

    }

    private function buildDomainTagMenu(ItemInterface $menu, $collections){
        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getSlug())
                ));
            }
        }
    }

    private function buildDomainDiscountMenu(ItemInterface $menu, $collections)
    {

        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getId())
                ));
            }
        }

    }

    private function buildDomainFeatureMenu(ItemInterface $menu, $collections)
    {

        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getId())
                ));
            }
        }

    }



    private function buildChildMenus(ItemInterface $menu, $categories)
    {

        foreach ($categories as $category) {

            /** var Category $category */
            $categoryName = $category['name'];

            if (!empty($categoryName)) {

                $menu
                    ->addChild($categoryName, array('route' => 'frontend_category',
                        'routeParameters' => array('slug' => $category['slug'])
                    ))
                    ->setAttribute('icon', 'fa fa-angle-right');

                if (!empty($category['__children'])) {
                    $menu->setAttribute('dropdown', true);
                    $menu[$categoryName]->setChildrenAttribute('class', 'dropdown-menu');
                    $this->buildChildMenus($menu[$categoryName], $category['__children']);
                }
            }
        }
    }

    private function buildBrandMenu(ItemInterface $menu, $brands)
    {
        $menu
            ->addChild('brands')
            ->setAttribute('brands', true)
            ->setAttribute('class', 'col-md-12 nav-brands');
        foreach ($brands as $brand) {
            /** @var Branding $brand */
            $menu['brands']->addChild($brand->getName(), array('route' => 'frontend_brand',
                'routeParameters' => array('slug' => $brand->getSlug())
            ))
                ->setAttribute('brand', true)
                ->setAttribute('icon', $brand->getAbsolutePath());;
        }
    }

    private function buildCollectionMenu(ItemInterface $menu, $collections)
    {

        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getSlug())
                ));
            }
        }

    }

    public function manageVendorMenu($menu)
    {
        $securityContext = $this->container->get('security.context');

        $menu
            ->addChild('Manage Vendor')
            
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $menu['Manage Vendor']->addChild('Vendor', array('route' => 'vendor_user'));
            $menu['Manage Vendor']->addChild('Education', array('route' => 'education'));
            $menu['Manage Vendor']->addChild('Scholarship', array('route' => 'scholarship'));
        }
        return $menu;
    }

    protected function getCategoryList()
    {
        $repo = $this->container->get('doctrine')->getRepository('ProductProductBundle:Category');
        $options = array(
            'decorate' => false,
            'representationField' => 'slug',
            'html' => false
        );

        return $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            $options
        );
    }

}
