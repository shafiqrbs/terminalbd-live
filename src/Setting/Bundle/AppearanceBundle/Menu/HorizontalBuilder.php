<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 3/4/15
 * Time: 3:36 PM
 */

namespace Setting\Bundle\AppearanceBundle\Menu;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu;
use Setting\Bundle\AppearanceBundle\Entity\MegaMenu;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\DependencyInjection\ContainerAware;
use Setting\Bundle\AppearanceBundle\Entity\MenuGrouping;
use Product\Bundle\ProductBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;


class HorizontalBuilder extends ContainerAware
{

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $globalOption = $securityContext->getToken()->getUser()->getGlobalOption();

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'page-sidebar-menu');
        $menu = $this->dashboardMenu($menu);
        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {

            $menu = $this->toolsMenu($menu);
            // $menu = $this->syndicateMenu($menu);
            $menu = $this->productCategoryMenu($menu);
            $menu = $this->manageFrontendMenu($menu);
            $menu = $this->manageVendorMenu($menu);
            $menu = $this->manageAdvertismentMenu($menu);
            $menu = $this->manageApplicationSettingMenu($menu);
            $menu = $this->manageDomainMenu($menu);
            $menu = $this->manageSystemAccountMenu($menu);
            $menu = $this->PayrollMenu($menu);
            $menu = $this->manageCustomerOrderMenu($menu);
            $menu = $this->BusinessMenu($menu);


        }

            $modules = $globalOption->getSiteSetting()->getAppModules();
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
                    $menu = $this->InventoryMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Hospital'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_HOSPITAL')){
                    $menu = $this->HospitalMenu($menu);
                }
            }



            $result = array_intersect($menuName, array('Dps'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DPS')){
                    $menu = $this->DpsMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Accounting'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ACCOUNTING')){
                    $menu = $this->AccountingMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Ecommerce'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ECOMMERCE')){
                    $menu = $this->EcommerceMenu($menu);
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

            $result = array_intersect($menuName, array('Restaurant'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_RESTAURANT')){
                    $menu = $this->RestaurantMenu($menu);
                }
            }

            $applications = array('accounting', 'e-commerce', 'inventory');
            $result = array_intersect($arrSlugs, $applications);
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_CONFIG')){
                    $menu = $this->applicationSettingMenu($menu);
                }
            }

            $applications = array('dms', 'hms', 'prescription', 'medicine');
            $result = array_intersect($arrSlugs, $applications);
            if (!empty($result)) {
                $roles = $securityContext->getToken()->getUser()->getRoles();
                if (array_intersect(array('ROLE_ADMIN','ROLE_SUPER_ADMIN','ROLE_DOMAIN_DOCTOR','ROLE_DOMAIN_MEDICINE_ADMIN','ROLE_DOMAIN_MEDICINE_MANAGER','ROLE_DOMAIN_HOSPITAL_MANAGER','ROLE_DOMAIN_HOSPITAL_DOCTOR','ROLE_DOMAIN_DMS_DOCTOR','ROLE_DOMAIN_DMS_ADMIN','ROLE_DOMAIN'),$roles)){
                  //  $menu = $this->DrugMenu($menu);
                }
            }

            if ($securityContext->isGranted('ROLE_DOMAIN') || $securityContext->isGranted('ROLE_SMS')) {
                $menu = $this->manageDomainInvoiceMenu($menu);
                //$menu = $this->manageCustomerOrderMenu($menu);
            }

            if ($securityContext->isGranted('ROLE_CUSTOMER')) {
                $menu = $this->manageCustomerOrderMenu($menu);
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

    public function manageCustomerOrderMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $menu
            ->addChild('Inbox & Transaction')
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'fa fa-user-circle');
        $menu['Inbox & Transaction']->addChild('Client', array('route' => 'agentclient'))->setAttribute('icon', 'icon-users');
        $menu['Inbox & Transaction']->addChild('Receive Invoice', array('route' => 'agentclient_invoice'))->setAttribute('icon', 'icon-money');
        $menu['Inbox & Transaction']->addChild('Manage Inbox')->setAttribute('icon', 'icon-money')->setAttribute('dropdown', true);
        $menu['Inbox & Transaction']['Manage Inbox']->addChild('Email', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-envelope');
        $menu['Inbox & Transaction']['Manage Inbox']->addChild('SMS', array('route' => 'invoicemodule'))->setAttribute('icon', 'icon-phone');
        return $menu;
    }


    public function BusinessMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $menu
            ->addChild('Business Management')
            ->setAttribute('icon', 'fa fa-hospital-o')
            ->setAttribute('dropdown', true);

        $menu['Business Management']->addChild('Client', array('route' => 'business_invoice'))
            ->setAttribute('icon', 'fa fa-medkit');
        $menu['Business Management']->addChild('Add Invoice', array('route' => 'business_invoice_new'))
            ->setAttribute('icon', 'fa fa-medkit');
        $menu['Business Management']->addChild('Expense')
            ->setAttribute('icon', 'icon icon-money')
            ->setAttribute('dropdown', true);
        $menu['Business Management']['Expense']->addChild('Expenditure', array('route' => 'business_account_expenditure'))
            ->setAttribute('icon', 'fa fa-indent');
        $menu['Business Management']['Expense']->addChild('Expense Category', array('route' => 'business_expensecategory'))
            ->setAttribute('icon', 'icon-tags');

        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {

            $menu['Business Management']->addChild('Master Data')
                ->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            if ($securityContext->isGranted('ROLE_DOMAIN_DMS_CONFIG')) {
                $menu['Business Management']['Master Data']->addChild('Configuration', array('route' => 'business_config_manage'))
                    ->setAttribute('icon', 'icon-cog');
            }
            $menu['Business Management']->addChild('Manage Stock')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Business Management']['Manage Stock']->addChild('Accessories Out', array('route' => 'business_product'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']['Manage Stock']->addChild('Accessories', array('route' => 'business_product'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']->addChild('Purchase')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Business Management']['Purchase']->addChild('Receive', array('route' => 'business_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']['Purchase']->addChild('Vendor', array('route' => 'business_vendor'))->setAttribute('icon', 'icon-tag');

            $menu['Business Management']->addChild('Sales Reports')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Business Management']['Sales Reports']->addChild('Sales Summary', array('route' => 'business_report_sales_summary'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']['Sales Reports']->addChild('Sales Details', array('route' => 'business_report_sales'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']['Sales Reports']->addChild('Sales Monthly', array('route' => 'business_report_sales_monthly'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']['Sales Reports']->addChild('Sales Yearly', array('route' => 'business_report_sales_yearly'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']['Sales Reports']->addChild('All Sales Yearly', array('route' => 'business_report_sales_all_yearly'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']['Sales Reports']->addChild('Treatment Base Sales', array('route' => 'business_report_sales_treatment'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']['Sales Reports']->addChild('Purchase', array('route' => 'business_report_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Business Management']->addChild('Accounting Report')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Business Management']['Accounting Report']->addChild('Cash in Hand', array('route' => 'business_report_cash'))
                ->setAttribute('icon', 'icon-money');
            $menu['Business Management']['Accounting Report']->addChild('Expenditure', array('route' => 'business_report_expenditure'))
                ->setAttribute('icon', 'fa fa-indent');
            $menu['Business Management']['Accounting Report']->addChild('Income', array('route' => 'business_report_income'))
                ->setAttribute('icon', 'icon-credit-card');
            $menu['Business Management']['Accounting Report']->addChild('Balance Sheet', array('route' => 'business_report_balance_sheet'))
                ->setAttribute('icon', 'icon-table');
            $menu['Business Management']->addChild('Stock Report')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Business Management']['Stock Report']->addChild('Accessories Stock', array('route' => 'business_report_stock'))
                ->setAttribute('icon', ' icon-inbox');
            $menu['Business Management']['Stock Report']->addChild('Accessories Out', array('route' => 'business_report_stock_out'))
                ->setAttribute('icon', 'icon-hdd');

        }
        return $menu;

    }

    public function WebsiteMenu($menu,$menuName)
    {

        $securityContext = $this->container->get('security.context');

        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_MANAGER')) {

            $option = $this->container->get('security.context')->getToken()->getUser()->getGlobalOption();
            $menu
                ->addChild('Manage Content')
                ->setAttribute('icon', 'fa fa-book')
                ->setAttribute('dropdown', true);

            $menu['Manage Content']->addChild('Page', array('route' => 'page'));
            if ($option->getSiteSetting()) {
                $syndicateModules = $option->getSiteSetting()->getSyndicateModules();
                if (!empty($syndicateModules)) {
                    foreach ($option->getSiteSetting()->getSyndicateModules() as $syndmod) {
                        $menu['Manage Content']->addChild($syndmod->getName(), array('route' => strtolower($syndmod->getModuleClass())));
                    }
                }

                $modules = $option->getSiteSetting()->getModules();
                if (!empty($modules)) {
                    foreach ($option->getSiteSetting()->getModules() as $mod) {
                        $menu['Manage Content']->addChild($mod->getName(), array('route' => strtolower($mod->getModuleClass())));
                    }
                }
            }
            $menu
                ->addChild('Media')
                ->setAttribute('icon', 'fa fa-picture-o')
                ->setAttribute('dropdown', true);

            $menu['Manage Content']->addChild('Contact', array('route' => 'contactpage_modify'));
            $menu['Media']->addChild('Galleries', array('route' => 'gallery'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_SETTING')) {

            $result = array_intersect($menuName, array('Ecommerce'));
            $securityContext = $this->container->get('security.context');
            $globalOption = $securityContext->getToken()->getUser()->getGlobalOption();
            $menu
                ->addChild('Manage Appearance')
                ->setAttribute('icon', 'fa fa-cog')
                ->setAttribute('dropdown', true);

            $menu['Manage Appearance']->addChild('Customize Template', array('route' => 'templatecustomize_edit', 'routeParameters' => array('id' => $globalOption->getId())));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_CONFIG') && $securityContext->isGranted('ROLE_ECOMMERCE')){
                    $menu['Manage Appearance']->addChild('E-commerce')->setAttribute('icon', 'icon-th-list')->setAttribute('dropdown', true);
                    $menu['Manage Appearance']['E-commerce']->addChild('E-commerce Widget', array('route' => 'appearancefeaturewidget'))->setAttribute('icon', 'icon-th-list');
                    $menu['Manage Appearance']['E-commerce']->addChild('Feature', array('route' => 'appearancefeature'))->setAttribute('icon', 'icon-th-list');
                    $menu['Manage Appearance']['E-commerce']->addChild('Feature Category', array('route' => 'featurecategory'))->setAttribute('icon', 'icon-th-list');
                    $menu['Manage Appearance']['E-commerce']->addChild('Feature Brand', array('route' => 'featurebrand'))->setAttribute('icon', 'icon-th-list');


                }
            }
            $menu['Manage Appearance']->addChild('Website')->setAttribute('icon', 'icon-th-list')->setAttribute('dropdown', true);
            $menu['Manage Appearance']['Website']->addChild('Website Widget', array('route' => 'appearancewebsitewidget'))->setAttribute('icon', 'icon-th-list');
            $menu['Manage Appearance']['Website']->addChild('Feature', array('route' => 'appearancefeature'))->setAttribute('icon', 'icon-th-list');
            $menu['Manage Appearance']->addChild('Menu')->setAttribute('icon', 'icon-th-list')->setAttribute('dropdown', true);
            if (!empty($result) and $securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_CONFIG') && $securityContext->isGranted('ROLE_ECOMMERCE')) {
                $menu['Manage Appearance']['Menu']->addChild('E-commerce Menu', array('route' => 'ecommercemenu'))->setAttribute('icon', 'icon-th-list');
            }
            $menu['Manage Appearance']['Menu']->addChild('Website Menu', array('route' => 'menu_manage'))->setAttribute('icon', 'icon-th-list');
            $menu['Manage Appearance']['Menu']->addChild('Menu Grouping', array('route' => 'menugrouping'))->setAttribute('icon', 'icon-th-list');

        }

        if ($securityContext->isGranted('ROLE_DOMAIN')) {
            $menu['Manage Appearance']->addChild('Settings', array('route' => 'globaloption_modify'));
        }
        return $menu;
    }

    public function AccountingMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $globalOption = $user->getGlobalOption();
        $modules = $user->getGlobalOption()->getSiteSetting()->getAppModules();
        $arrSlugs = array();
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
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_REPORT')) {

            $menu['Accounting']->addChild('Transaction & Report', array('route' => 'account_transaction'))
                ->setAttribute('icon', 'fa fa-money')
                ->setAttribute('dropdown', true);

            $menu['Accounting']['Transaction & Report']->addChild('Transaction Overview', array('route' => 'account_transaction'))->setAttribute('icon', 'icon-th-list');

            $accounting = array('inventory');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'report_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'))->setAttribute('icon', 'icon-th-list');
            }
            $accounting = array('e-commerce');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'report_income'))->setAttribute('icon', 'icon-th-list');
                /* $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'))->setAttribute('icon', 'icon-th-list');*/
            }
            $accounting = array('hms');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Transaction & Report']->addChild('Income', array('route' => 'hms_report_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Monthly Income',        array('route' => 'hms_report_monthly_income'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Expenditure Summary',        array('route' => 'hms_report_expenditure_summary'))->setAttribute('icon', 'icon-th-list');
                $menu['Accounting']['Transaction & Report']->addChild('Expenditure Details',        array('route' => 'hms_report_expenditure_details'))->setAttribute('icon', 'icon-th-list');
            }

        }

        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_TRANSACTION')) {

            $menu['Accounting']->addChild('Cash', array('route' => ''))
                ->setAttribute('icon', 'fa fa-money')
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Cash']->addChild('Cash Overview', array('route' => 'account_transaction_cash_overview'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Cash']->addChild('Cash Transaction', array('route' => 'account_transaction_cash'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Cash']->addChild('Bank Transaction', array('route' => 'account_transaction_bank'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Cash']->addChild('Mobile Transaction', array('route' => 'account_transaction_mobilebank'))->setAttribute('icon', 'icon-th-list');
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE')){
            $menu['Accounting']->addChild('Expenditure', array('route' => 'account_expenditure'))
                ->setAttribute('icon', 'fa fa-money')
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Expenditure']->addChild('Expense', array('route' => 'account_expenditure'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']['Expenditure']->addChild('Expense Category', array('route' => 'expensecategory'))->setAttribute('icon', 'icon-th-list');
        }
        /*$menu['Finance']->addChild('Petty Cash & Expense', array('route' => 'account_pettycash'))
            ->setAttribute('icon','fa fa-money')
            ->setAttribute('dropdown', true);
        $menu['Finance']['Petty Cash & Expense']->addChild('Petty Cash', array('route' => 'account_pettycash'))->setAttribute('icon', 'icon-th-list');
        $menu['Finance']['Petty Cash & Expense']->addChild('Expense',        array('route' => 'account_expenditure'))->setAttribute('icon', 'icon-th-list');
        $menu['Finance']['Petty Cash & Expense']->addChild('Expense Category',        array('route' => 'expensecategory'))->setAttribute('icon', 'icon-th-list');*/
        $inventory = array('inventory');
        $result = array_intersect($arrSlugs, $inventory);
        if (!empty($result)) {

            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']->addChild('Sales', array('route' => 'account_sales'));
                $menu['Accounting']->addChild('Sales Return', array('route' => 'account_sales_return'));
            }

            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']->addChild('Purchase', array('route' => 'account_purchase'));
                $menu['Accounting']->addChild('Purchase Return', array('route' => 'account_purchase_return'));
            }
        }
        $accounting = array('e-commerce');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result) && $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_ECOMMERCE')) {

            $menu['Accounting']->addChild('Online Order', array('route' => 'account_onlineorder'));
            $menu['Accounting']->addChild('Online Order Return', array('route' => 'account_onlineorder'));
        }

        $hospital = array('hms');
        $result = array_intersect($arrSlugs, $hospital);
        if (!empty($result)) {
            $menu['Accounting']->addChild('Sales', array('route' => 'account_sales_hospital'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']->addChild('Purchase', array('route' => 'account_purchase_hospital'))->setAttribute('icon', 'icon-th-list');

        }
        $restaurant = array('restaurant');
        $result = array_intersect($arrSlugs, $restaurant);
        if (!empty($result)) {
            $menu['Accounting']->addChild('Sales', array('route' => 'account_sales_restaurant'))->setAttribute('icon', 'icon-th-list');
            $menu['Accounting']->addChild('Purchase', array('route' => 'account_purchase_restaurant'))->setAttribute('icon', 'icon-th-list');

        }
        if($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_JOURNAL')){

            $menu['Accounting']->addChild('Journal', array('route' => 'account_journal'))->setAttribute('icon', 'icon-retweet');
        }


        return $menu;

    }

    public function InventoryMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $menu
            ->addChild('Sales')
            ->setAttribute('icon', 'fa fa-shopping-bag')
            ->setAttribute('dropdown', true);

        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES')) {

            $menu['Sales']->addChild('Customers', array('route' => 'inventory_customer'))->setAttribute('icon', 'icon icon-user');

            $deliveryProcess = $inventory->getDeliveryProcess();
            if (!empty($deliveryProcess)) {

                if (in_array('Pos', $deliveryProcess)) {

                    if ($this->container->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES_POS')){

                        $menu['Sales']->addChild('Point of Sales')
                            ->setAttribute('icon', 'fa fa-shopping-basket')
                            ->setAttribute('dropdown', true);
                        $menu['Sales']['Point of Sales']->addChild('Pos', array('route' => 'inventory_sales_new'))->setAttribute('icon', 'icon-shopping-cart');
                        $menu['Sales']['Point of Sales']->addChild('Sales', array('route' => 'inventory_sales'))->setAttribute('icon', ' icon-th-list');
                        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER') or $securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER')) {
                            $menu['Sales']['Point of Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-share-alt');
                        }
                        $menu['Sales']['Point of Sales']->addChild('Sales Import', array('route' => 'inventory_salesimport'))->setAttribute('icon', 'icon-upload');
                    }
                }

                if (in_array('OnlineSales', $deliveryProcess)) {

                    if ($this->container->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES_ONLINE')){
                        $menu['Sales']
                            ->addChild('Online Sales')
                            ->setAttribute('icon', 'icon icon-truck')
                            ->setAttribute('dropdown', true);
                        $menu['Sales']['Online Sales']->addChild('Add Sales', array('route' => 'inventory_salesonline_new'))->setAttribute('icon', ' icon-plus');
                        $menu['Sales']['Online Sales']->addChild('Sales', array('route' => 'inventory_salesonline'))->setAttribute('icon', ' icon-th-list');
                    }

                }

                if (in_array('GeneralSales', $deliveryProcess)) {
                    if ($this->container->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES_GENERAL')){
                        $menu['Sales']
                            ->addChild('General Sales')
                            ->setAttribute('icon', 'icon icon-truck')
                            ->setAttribute('dropdown', true);
                        $menu['Sales']['General Sales']->addChild('Add Sales', array('route' => 'inventory_salesgeneral_new'))->setAttribute('icon', ' icon-plus');
                        $menu['Sales']['General Sales']->addChild('Sales', array('route' => 'inventory_salesgeneral'))->setAttribute('icon', ' icon-th-list');
                        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER') or $securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER') ) {
                            $menu['Sales']['General Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-share-alt');
                        }
                        $menu['Sales']['General Sales']->addChild('Sales Import', array('route' => 'inventory_salesimport'))->setAttribute('icon', 'icon-upload');
                    }
                }

                if (in_array('ManualSales', $deliveryProcess)) {
                    if ($this->container->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES_MANUAL')){

                        $menu['Sales']
                            ->addChild('Manual Sales')
                            ->setAttribute('icon', 'fa fa-shopping-basket')
                            ->setAttribute('dropdown', true);
                        $menu['Sales']['Manual Sales']->addChild('Add Sales', array('route' => 'inventory_salesmanual_new'))->setAttribute('icon', 'fa fa-cart-plus');
                        $menu['Sales']['Manual Sales']->addChild('Sales', array('route' => 'inventory_salesmanual'))->setAttribute('icon', ' icon-th-list');
                        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER') or $securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER')) {
                            $menu['Sales']['Manual Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-exchange');
                        }

                    }
                }

                if (in_array('Order', $deliveryProcess)) {
                    if ($this->container->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES_ORDER')){
                        $menu['Sales']
                            ->addChild('Online Order')
                            ->setAttribute('icon', 'icon icon-truck')
                            ->setAttribute('dropdown', true);
                        $menu['Sales']['Online Order']->addChild('Online Customer', array('route' => 'inventory_customer'))->setAttribute('icon', 'icon icon-user');
                        $menu['Sales']['Online Order']->addChild('Order', array('route' => 'inventory_sales'))->setAttribute('icon', ' icon-th-list');
                        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER')) {
                            $menu['Sales']['Online Order']->addChild('Order Return', array('route' => 'inventory_salesreturn'))->setAttribute('icon', 'icon-share-alt');
                        }
                    }
                }


            }
        }
        $menu
            ->addChild('Inventory')
            ->setAttribute('icon', 'icon-archive')
            ->setAttribute('dropdown', true);

       /* if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {

            $menu['Inventory']->addChild('Manage Purchase', array('route' => 'purchase'))
                ->setAttribute('icon', 'icon icon-shopping-cart')
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Purchase']->addChild('Purchase', array('route' => 'purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Return', array('route' => 'inventory_purchasereturn'))
                ->setAttribute('icon', ' icon-reply');
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Import', array('route' => 'inventory_excelimproter'))
                ->setAttribute('icon', 'icon-upload');

        }*/


        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {

            $menu['Inventory']->addChild('Manage Purchase', array('route' => 'purchase'))
                ->setAttribute('icon', 'icon icon-list-alt')
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Purchase']->addChild('Purchase', array('route' =>'inventory_purchasesimple'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Return', array('route' => 'inventory_purchasereturn'))
                ->setAttribute('icon', ' icon-reply');
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Import', array('route' => 'inventory_excelimproter'))
                ->setAttribute('icon', 'icon-upload');


        }

        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {
            $menu['Inventory']->addChild('Purchase Item for Web', array('route' => 'inventory_purchasevendoritem'))
                ->setAttribute('icon', 'icon-info-sign');
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_STOCK')) {

            $menu['Inventory']->addChild('Manage Stock')->setAttribute('icon', 'icon icon-archive')->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Stock']->addChild('Stock Item', array('route' => 'inventory_item'))
                ->setAttribute('icon', 'icon-hdd');
            $menu['Inventory']['Manage Stock']->addChild('Barcode wise Stock', array('route' => 'inventory_barcode_branch_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Manage Stock']->addChild('Barcode Stock Details', array('route' => 'inventory_barcode_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Manage Stock']->addChild('Stock Item Details', array('route' => 'inventory_stockitem'))
                ->setAttribute('icon', 'icon-hdd');
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER')) {
            if ($inventory->getBarcodePrint() == 1) {
                $menu['Inventory']['Manage Stock']->addChild('Barcode Print', array('route' => 'inventory_barcode'))
                    ->setAttribute('icon', 'icon-barcode');
            }
            $menu['Inventory']['Manage Stock']->addChild('Damage', array('route' => 'inventory_damage'))
                ->setAttribute('icon', ' icon-trash');
        }


        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH')) {

            if ($inventory->getIsBranch() == 1) {
                $menu['Inventory']
                    ->addChild('Branch Delivery')
                    ->setAttribute('icon', 'icon icon-truck')
                    ->setAttribute('dropdown', true);
                $menu['Inventory']['Branch Delivery']->addChild('Delivery Invoice', array('route' => 'inventory_delivery'))->setAttribute('icon', 'icon-shopping-cart');
                $menu['Inventory']['Branch Delivery']->addChild('Return Invoice', array('route' => 'inventory_deliveryreturn'))->setAttribute('icon', 'icon-retweet');

            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_CONFIG')) {

            $menu['Inventory']->addChild('Configuration', array('route' => 'inventoryconfig_edit'))
                ->setAttribute('icon', 'icon icon-cogs');
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {

            $menu['Inventory']->addChild('Reports')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Reports']->addChild('Summary Overview', array('route' => 'inventory_report_overview'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Item Overview', array('route' => 'inventory_report_stock_item'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Till Stock', array('route' => 'inventory_report_till_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Periodic Stock', array('route' => 'inventory_report_periodic_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Operational Stock', array('route' => 'inventory_report_operational_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Group Stock', array('route' => 'inventory_report_group_stock'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Purchase with price', array('route' => 'inventory_report_purchase'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Sales Overview', array('route' => 'inventory_report_sales_overview'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Periodic Sales Item', array('route' => 'inventory_report_sales_item'))->setAttribute('icon', 'icon-bar-chart');
            $menu['Inventory']['Reports']->addChild('Sales with price', array('route' => 'inventory_report_sales'))->setAttribute('icon', 'icon-bar-chart');

        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER')) {
            if ($inventory->getIsBranch() == 1) {
                $menu['Inventory']->addChild('Branch Reports')
                    ->setAttribute('icon', 'icon-bar-chart')
                    ->setAttribute('dropdown', true);
                $menu['Inventory']['Branch Reports']->addChild('Stock Overview', array('route' => 'inventory_branch_report_overview'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Inventory']['Branch Reports']->addChild('Item wise Stock', array('route' => 'inventory_branch_report_stock'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Inventory']['Branch Reports']->addChild('Barcode wise Stock', array('route' => 'inventory_branch_report_barcode_item'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Inventory']['Branch Reports']->addChild('Item Stock', array('route' => 'inventory_branch_report_item'))->setAttribute('icon', 'icon-bar-chart');
                $menu['Inventory']['Branch Reports']->addChild('Sales Item', array('route' => 'inventory_branch_report_sales'))->setAttribute('icon', 'icon-bar-chart');
            }
        }

        return $menu;

    }

    public function EcommerceMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $menu
            ->addChild('E-commerce')
            ->setAttribute('icon', 'icon  icon-shopping-cart')
            ->setAttribute('dropdown', true);

        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_PRODUCT')) {

            $menu['E-commerce']->addChild('Product', array('route' => ''))
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setAttribute('dropdown', true);
            $menu['E-commerce']['Product']->addChild('Product', array('route' => 'inventory_goods'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Promotion', array('route' => 'ecommerce_promotion'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Discount', array('route' => 'ecommerce_discount'))->setAttribute('icon', 'icon-th-list');
            $menu['E-commerce']['Product']->addChild('Item Attribute', array('route' => 'itemattribute'))->setAttribute('icon', 'icon-th-list');

        }

        /*$menu['E-commerce']->addChild('Transaction', array('route' => ''))
            ->setAttribute('icon','fa fa-bookmark')
            ->setAttribute('dropdown', true);
        $menu['E-commerce']['Transaction']->addChild('Order',        array('route' => 'customer_order'))->setAttribute('icon', 'icon-th-list');
        $menu['E-commerce']['Transaction']->addChild('Pre-order',    array('route' => 'customer_preorder'))->setAttribute('icon', 'icon-th-list');
        */

        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_ORDER')) {

            $menu['E-commerce']->addChild('Order', array('route' => ''))
                ->setAttribute('icon', 'fa fa-bookmark')
                ->setAttribute('dropdown', true);
            $menu['E-commerce']['Order']->addChild('Order', array('route' => 'customer_order'))->setAttribute('icon', 'icon-truck');
            $menu['E-commerce']['Order']->addChild('Order Return', array('route' => 'customer_order'))->setAttribute('icon', 'icon-truck');
            $menu['E-commerce']['Order']->addChild('Pre-order', array('route' => 'customer_preorder'))->setAttribute('icon', 'icon-truck');

        }
        $menu['E-commerce']->addChild('Coupon', array('route' => 'ecommerce_coupon'))->setAttribute('icon', 'icon-tags');
        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_CONFIG')) {
            $menu['E-commerce']->addChild('Configuration', array('route' => 'ecommerce_config_modify'))->setAttribute('icon', 'fa fa-cog');
        }
        return $menu;
    }

    public function applicationSettingMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();

        $menu
            ->addChild('Master Data')
            ->setAttribute('icon', 'fa fa-server')
            ->setAttribute('dropdown', true);

        $globalOption = $user->getGlobalOption();
        $modules = $user->getGlobalOption()->getSiteSetting()->getAppModules();
        $arrSlugs = array();
        if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
            foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $arrSlugs[] = $mod->getSlug();
                }
            }
        }
        $accounting = array('accounting');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result)) {
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_CONFIG')) {

                $menu['Master Data']->addChild('Accounting', array('route' => ''))
                    ->setAttribute('icon', 'fa fa-building-o')
                    ->setAttribute('dropdown', true);
                $menu['Master Data']['Accounting']->addChild('Bank Account', array('route' => 'appsetting_bank'))->setAttribute('icon', 'fa fa-bank');
                $menu['Master Data']['Accounting']->addChild('Mobile Account', array('route' => 'appsetting_mobile_bank'))->setAttribute('icon', 'fa fa-mobile');
                $menu['Master Data']['Accounting']->addChild('Account Head', array('route' => 'accounthead'))->setAttribute('icon', 'fa fa-bar-chart-o');

            }
        }

        $inventories = array('inventory');
        $result = array_intersect($arrSlugs, $inventories);
        if (!empty($result)) {

            if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {

                $menu['Master Data']->addChild('Inventory', array('route' => ''))
                    ->setAttribute('icon', 'fa fa-archive')
                    ->setAttribute('dropdown', true);
                $menu['Master Data']['Inventory']->addChild('Master Item', array('route' => 'inventory_product'))->setAttribute('icon', 'icon-th-list');
                $menu['Master Data']['Inventory']->addChild('Item category', array('route' => 'itemtypegrouping_edit', 'routeParameters' => array('id' => $inventory->getId())))->setAttribute('icon', 'icon-th-list');
                $menu['Master Data']['Inventory']->addChild('Custom category', array('route' => 'inventory_category'))->setAttribute('icon', 'icon-th-list');
                $menu['Master Data']['Inventory']->addChild('Vendor', array('route' => 'inventory_vendor'))->setAttribute('icon', 'icon-th-list');
                $menu['Master Data']['Inventory']->addChild('Brand', array('route' => 'itembrand'))->setAttribute('icon', 'icon-th-list');
                //$menu['Master Data']['Inventory Setting']->addChild('Color', array('route' => 'itemcolor'))->setAttribute('icon', 'icon-th-list');
                $menu['Master Data']['Inventory']->addChild('Size Group', array('route' => 'itemsize_group'))->setAttribute('icon', 'icon-th-list');
                //$menu['Master Data']['Inventory Setting']->addChild('Ware House', array('route' => 'inventory_warehouse'))->setAttribute('icon', 'icon-th-list');
                if ($inventory->getIsBranch() == 1) {
                    $menu['Master Data']->addChild('Branches')->setAttribute('icon', 'icon-building')->setAttribute('dropdown', true);
                    $menu['Master Data']['Branches']->addChild('Branch Shop', array('route' => 'appsetting_branchshop'))->setAttribute('icon', 'icon-building');
                }
            }
        }

        $ecommerce = array('e-commerce');
        $result = array_intersect($arrSlugs, $ecommerce);
        $resInventory = array_intersect($arrSlugs, $inventories);
        if (!empty($result)) {

            if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE')) {
                $menu['Master Data']->addChild('E-commerce', array('route' => ''))
                    ->setAttribute('icon', 'fa fa-shopping-cart')
                    ->setAttribute('dropdown', true);
                if (empty($resInventory)) {
                    $menu['Master Data']['E-commerce']->addChild('Master Item', array('route' => 'inventory_product'))->setAttribute('icon', 'icon-th-list');
                    $menu['Master Data']['E-commerce']->addChild('Item category', array('route' => 'itemtypegrouping_edit', 'routeParameters' => array('id' => $inventory->getId())))->setAttribute('icon', 'icon-th-list');
                    $menu['Master Data']['E-commerce']->addChild('Custom category', array('route' => 'inventory_category'))->setAttribute('icon', 'icon-th-list');
                    $menu['Master Data']['E-commerce']->addChild('Brand', array('route' => 'itembrand'))->setAttribute('icon', 'icon-th-list');
                }

            }
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
            ->setAttribute('icon', 'icon icon-reorder')
            ->setAttribute('dropdown', true);
        $menu['Service & Sales']['Manage Service']->addChild('Add Item', array('route' => 'inventory_serviceitem_new'))
            ->setAttribute('icon', 'icon-plus');
        $menu['Service & Sales']['Manage Service']->addChild('Service Item', array('route' => 'inventory_serviceitem'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Service & Sales']->addChild('Manage Sales')
            ->setAttribute('icon', 'icon icon-reorder')
            ->setAttribute('dropdown', true);
        $menu['Service & Sales']['Manage Sales']->addChild('Create Invoice', array('route' => 'inventory_servicesales_new'))
            ->setAttribute('icon', 'fa fa-files-o');
        $menu['Service & Sales']['Manage Sales']->addChild('Sales', array('route' => 'inventory_servicesales'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Service & Sales']->addChild('System Setting')
            ->setAttribute('icon', 'icon icon-cogs')
            ->setAttribute('dropdown', true);

        /*
                if($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_CONFIG')) {
                    $menu['Service & Sales']['System Setting']->addChild('Configuration', array('route' => 'inventoryconfig_edit'))
                        ->setAttribute('icon', 'icon-hdd');
                }
                $menu['Inventory']['System Setting']->addChild('Variant', array('route' => 'colorsize'))
                    ->setAttribute('icon', 'icon-th-list');
                $menu['Inventory']['System Setting']->addChild('Ware House', array('route' => 'inventory_warehouse'))->setAttribute('icon', 'icon-th-list');*/
        return $menu;

    }

    public function HospitalMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $config = $user->getGlobalOption()->getHospitalConfig()->getInvoiceProcess();
        $menu
            ->addChild('Hospital & Diagnostic')
            ->setAttribute('icon', 'fa fa-hospital-o')
            ->setAttribute('dropdown', true);

        $menu['Hospital & Diagnostic']->addChild('Manage Invoice')
            ->setAttribute('icon', 'icon icon-medkit')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_OPERATOR')) {
            if (!empty($config) and in_array('diagnostic', $config)) {
                $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Diagnostic', array('route' => 'hms_invoice'))
                    ->setAttribute('icon', 'fa fa-hospital-o');
            }
            if (!empty($config) and in_array('admission', $config)) {
            $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Admission', array('route' => 'hms_invoice_admission'))
                ->setAttribute('icon', 'fa fa-ambulance');
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_MANAGER')) {
            if (!empty($config) and in_array('doctor', $config)) {
                $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Commission', array('route' => 'hms_doctor_commission_invoice'))
                    ->setAttribute('icon', 'fa fa-user-md');
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_OPERATOR')) {
            if (!empty($config)) {
                $menu['Hospital & Diagnostic']['Manage Invoice']->addChild('Patient', array('route' => 'hms_customer'))
                    ->setAttribute('icon', 'fa fa-user');
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_LAB') || $securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_DOCTOR')) {
            $menu['Hospital & Diagnostic']->addChild('Diagnostic Report')
                ->setAttribute('icon', 'fa fa-stethoscope')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Diagnostic Report']->addChild('Collection & Process', array('route' => 'hms_invoice_particular'))
                ->setAttribute('icon', 'fa fa-stethoscope');
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_MANAGER')) {

            $menu['Hospital & Diagnostic']->addChild('Master Data')
                ->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Diagnostic Test', array('route' => 'hms_pathology'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Doctor', array('route' => 'hms_doctor'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Referred Doctor', array('route' => 'hms_referreddoctor'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Cabin/Ward', array('route' => 'hms_cabin'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Surgery', array('route' => 'hms_surgery'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Other Service', array('route' => 'hms_other_service'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Service Group', array('route' => 'hms_service_group'))->setAttribute('icon', 'icon-tag');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Category', array('route' => 'hms_category'))->setAttribute('icon', 'icon-tag');
            $menu['Hospital & Diagnostic']['Master Data']->addChild('Commission', array('route' => 'hms_commission'))->setAttribute('icon', 'icon-tag');
            if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_CONFIG')) {
                $menu['Hospital & Diagnostic']['Master Data']->addChild('Configuration', array('route' => 'hms_config_manage'))
                    ->setAttribute('icon', 'icon-cog');
            }
            $menu['Hospital & Diagnostic']->addChild('Manage Stock')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Manage Stock']->addChild('Medicine', array('route' => 'hms_medicine'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Manage Stock']->addChild('Accessories', array('route' => 'hms_accessories'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']->addChild('Purchase')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Purchase']->addChild('Medicine Receive', array('route' => 'hms_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Purchase']->addChild('Accessories Receive', array('route' => 'hms_accessories_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Purchase']->addChild('Vendor', array('route' => 'hms_vendor'))->setAttribute('icon', 'icon-tag');

            $menu['Hospital & Diagnostic']->addChild('Reports')
                ->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            $menu['Hospital & Diagnostic']['Reports']->addChild('Sales Summary', array('route' => 'hms_report_sales_summary'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Reports']->addChild('Sales Details', array('route' => 'hms_report_sales_details'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Reports']->addChild('Service Wise Sales', array('route' => 'hms_report_sales_service'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Hospital & Diagnostic']['Reports']->addChild('Purchase', array('route' => 'hms_pathology'))
                ->setAttribute('icon', 'icon-th-list');
        }
        return $menu;

    }

    public function DmsMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $config = $user->getGlobalOption()->getHospitalConfig()->getInvoiceProcess();
        $menu
            ->addChild('Dental & Diagnosis')
            ->setAttribute('icon', 'fa fa-hospital-o')
            ->setAttribute('dropdown', true);

        $menu['Dental & Diagnosis']->addChild('Patient', array('route' => 'dms_invoice'))
            ->setAttribute('icon', 'fa fa-medkit');
        $menu['Dental & Diagnosis']->addChild('Treatment Schedule', array('route' => 'dms_treatment_plan'))
            ->setAttribute('icon', 'fa fa-calendar');
        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_OPERATOR')) {
            if (!empty($config) and in_array('admission', $config)) {
            $menu['Dental & Diagnosis']->addChild('Patient', array('route' => 'dms_invoice'))
                ->setAttribute('icon', 'fa fa-stethoscope');
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {
            if (!empty($config) and in_array('doctor', $config)) {
                $menu['Dental & Diagnosis']->addChild('Commission', array('route' => 'dms_doctor_commission_invoice'))
                    ->setAttribute('icon', 'fa fa-user-md');
            }
        }

        $menu['Dental & Diagnosis']->addChild('Expense')
            ->setAttribute('icon', 'icon icon-money')
            ->setAttribute('dropdown', true);
        $menu['Dental & Diagnosis']['Expense']->addChild('Expenditure', array('route' => 'dms_account_expenditure'))
            ->setAttribute('icon', 'fa fa-indent');
        $menu['Dental & Diagnosis']['Expense']->addChild('Expense Category', array('route' => 'dms_expensecategory'))
            ->setAttribute('icon', 'icon-tags');

        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {

            $menu['Dental & Diagnosis']->addChild('Master Data')
                ->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Master Data']->addChild('Treatment Plan', array('route' => 'dms_treatment'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Master Data']->addChild('Particular', array('route' => 'dms_particular'))
                ->setAttribute('icon', 'icon-th-list');
             $menu['Dental & Diagnosis']['Master Data']->addChild('Service', array('route' => 'dms_service'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Master Data']->addChild('Doctor', array('route' => 'dms_doctor'))
                ->setAttribute('icon', 'icon-th-list');
            if ($securityContext->isGranted('ROLE_DOMAIN_DMS_CONFIG')) {
                $menu['Dental & Diagnosis']['Master Data']->addChild('Configuration', array('route' => 'dms_config_manage'))
                    ->setAttribute('icon', 'icon-cog');
            }
            $menu['Dental & Diagnosis']->addChild('Manage Stock')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Manage Stock']->addChild('Accessories Out', array('route' => 'dms_treatment_accessories'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Manage Stock']->addChild('Accessories', array('route' => 'dms_medicine'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']->addChild('Purchase')
                ->setAttribute('icon', 'icon icon-truck')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Purchase']->addChild('Receive', array('route' => 'dms_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Purchase']->addChild('Vendor', array('route' => 'dms_vendor'))->setAttribute('icon', 'icon-tag');

            $menu['Dental & Diagnosis']->addChild('Sales Reports')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Summary', array('route' => 'dms_report_sales_summary'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Details', array('route' => 'dms_report_sales'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Monthly', array('route' => 'dms_report_sales_monthly'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Sales Yearly', array('route' => 'dms_report_sales_yearly'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('All Sales Yearly', array('route' => 'dms_report_sales_all_yearly'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Treatment Base Sales', array('route' => 'dms_report_sales_treatment'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']['Sales Reports']->addChild('Purchase', array('route' => 'dms_report_purchase'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Dental & Diagnosis']->addChild('Accounting Report')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Cash in Hand', array('route' => 'dms_report_cash'))
                ->setAttribute('icon', 'icon-money');
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Expenditure', array('route' => 'dms_report_expenditure'))
                ->setAttribute('icon', 'fa fa-indent');
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Income', array('route' => 'dms_report_income'))
                ->setAttribute('icon', 'icon-credit-card');
            $menu['Dental & Diagnosis']['Accounting Report']->addChild('Balance Sheet', array('route' => 'dms_report_balance_sheet'))
                ->setAttribute('icon', 'icon-table');
            $menu['Dental & Diagnosis']->addChild('Stock Report')
                ->setAttribute('icon', 'icon-bar-chart')
                ->setAttribute('dropdown', true);
            $menu['Dental & Diagnosis']['Stock Report']->addChild('Accessories Stock', array('route' => 'dms_report_stock'))
                ->setAttribute('icon', ' icon-inbox');
            $menu['Dental & Diagnosis']['Stock Report']->addChild('Accessories Out', array('route' => 'dms_report_stock_out'))
                ->setAttribute('icon', 'icon-hdd');

        }
        return $menu;

    }

    public function DpsMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $menu
            ->addChild('Doctor Prescription')
            ->setAttribute('icon', 'fa fa-hospital-o')
            ->setAttribute('dropdown', true);
        $menu['Doctor Prescription']->addChild('Patient', array('route' => 'dps_invoice'))
            ->setAttribute('icon', 'fa fa-medkit');
        $menu['Doctor Prescription']->addChild('Expense')
            ->setAttribute('icon', 'icon icon-money')
            ->setAttribute('dropdown', true);
        $menu['Doctor Prescription']['Expense']->addChild('Expenditure', array('route' => 'dps_account_expenditure'))
            ->setAttribute('icon', 'fa fa-indent');
        $menu['Doctor Prescription']['Expense']->addChild('Expense Category', array('route' => 'dps_expensecategory'))
            ->setAttribute('icon', 'icon-tags');
        if ($securityContext->isGranted('ROLE_DOMAIN_DMS_MANAGER')) {

            $menu['Doctor Prescription']->addChild('Master Data')->setAttribute('icon', 'icon icon-cog')
                ->setAttribute('dropdown', true);
            $menu['Doctor Prescription']['Master Data']->addChild('Particular', array('route' => 'dps_particular'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Doctor Prescription']['Master Data']->addChild('Service', array('route' => 'dps_service'))
                ->setAttribute('icon', 'icon-th-list');
            $menu['Doctor Prescription']['Master Data']->addChild('Doctor', array('route' => 'dps_doctor'))
                ->setAttribute('icon', 'icon-th-list');
            if ($securityContext->isGranted('ROLE_DOMAIN_DMS_CONFIG')) {
                $menu['Doctor Prescription']['Master Data']->addChild('Configuration', array('route' => 'dps_config_manage'))
                    ->setAttribute('icon', 'icon-cog');
            }

        }
        return $menu;

    }

    public function DrugMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $menu
            ->addChild('Drug')
            ->setAttribute('icon', 'fa fa-stethoscope')
            ->setAttribute('dropdown', true);
        $menu['Drug']->addChild('Add Drug', array('route' => 'medicine_user'))->setAttribute('icon', 'icon-medkit');
        if ($securityContext->isGranted('ROLE_ADMIN') OR $securityContext->isGranted('ROLE_SUPER_ADMIN')) {
        $menu['Drug']->addChild('Drug', array('route' => 'medicine'))->setAttribute('icon', 'icon-medkit');
        }
        return $menu;
    }

    public function medicineMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $globalOption = $securityContext->getToken()->getUser()->getGlobalOption();
        $menu
            ->addChild('Drug')
            ->setAttribute('icon', 'fa fa-files-o')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_DOMAIN_HOSPITAL_MANAGER') OR $securityContext->isGranted('ROLE_DOMAIN_DMS_DOCTOR') OR $securityContext->isGranted('ROLE_DOMAIN_HMS_DOCTOR')) {
            $menu['Invoice Sms & Email']->addChild('Manage Sms')->setAttribute('icon', 'icon-phone')->setAttribute('dropdown', true);
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Logs', array('route' => 'smssender'))->setAttribute('icon', 'icon-phone');
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-money');
            $menu['Invoice Sms & Email']->addChild('Invoice Application', array('route' => 'invoicemodule_domain'))->setAttribute('icon', 'fa fa-files-o');
        }
        if ($securityContext->isGranted('ROLE_SMS_BULK')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Bulk Sms', array('route' => 'smsbulk'))->setAttribute('icon', 'icon-envelope');
        }
        if ($securityContext->isGranted('ROLE_SMS_CONFIG')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Notification Setup', array('route' => 'domain_notificationconfig'))->setAttribute('icon', 'fa fa-bell ');
        }
        return $menu;
    }

    public function RestaurantMenu($menu)
    {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $menu
            ->addChild('Restaurant')
            ->setAttribute('icon', 'icon icon-th-large')
            ->setAttribute('dropdown', true);

        $menu['Restaurant']->addChild('Point of Sales ', array('route' => 'restaurant_invoice_new'))
            ->setAttribute('icon', 'icon-th-large');
        $menu['Restaurant']->addChild('Manage Sales', array('route' => 'restaurant_invoice'))
            ->setAttribute('icon', 'icon-list');
        $menu['Restaurant']->addChild('Customer', array('route' => 'restaurant_customer'))->setAttribute('icon', 'icon icon-user');
        if ($securityContext->isGranted('ROLE_DOMAIN_RESTAURANT_MANAGER')) {
        $menu['Restaurant']->addChild('Master Data')
            ->setAttribute('icon', 'icon icon-cog')
            ->setAttribute('dropdown', true);
        $menu['Restaurant']['Master Data']->addChild('Product', array('route' => 'restaurant_product'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Master Data']->addChild('Particular', array('route' => 'restaurant_particular'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Master Data']->addChild('Configuration', array('route' => 'restaurant_config_manage'))
            ->setAttribute('icon', 'icon-cog');
        $menu['Restaurant']->addChild('Manage Stock')
            ->setAttribute('icon', 'icon icon-truck')
            ->setAttribute('dropdown', true);
        $menu['Restaurant']['Manage Stock']->addChild('Stock Product', array('route' => 'restaurant_stock'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Manage Stock']->addChild('Purchase', array('route' => 'restaurant_purchase'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Manage Stock']->addChild('Vendor', array('route' => 'restaurant_vendor'))->setAttribute('icon', 'icon-tag');

        $menu['Restaurant']->addChild('Reports')
            ->setAttribute('icon', 'icon icon-cog')
            ->setAttribute('dropdown', true);
        $menu['Restaurant']['Reports']->addChild('Sales Summary', array('route' => 'restaurant_report_sales_summary'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Reports']->addChild('Sales Details', array('route' => 'restaurant_report_sales_details'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Reports']->addChild('Product Wise Sales', array('route' => 'restaurant_report_sales_service'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Reports']->addChild('Stock Wise Sales', array('route' => 'restaurant_report_sales_service'))
            ->setAttribute('icon', 'icon-th-list');
        $menu['Restaurant']['Reports']->addChild('Stock Summary', array('route' => 'restaurant_report_stock'))
            ->setAttribute('icon', 'icon-th-list');
        }
        return $menu;

    }

    public function ClientRelationManagementMenu($menu)
    {
        $menu
            ->addChild('CRM')
            ->setAttribute('dropdown', true);
        $menu['CRM']->addChild('People')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
        $menu['CRM']['People']->addChild('Sms', array('route' => 'domain_customer_sms'))->setAttribute('icon', 'icon-phone');
        $menu['CRM']['People']->addChild('Email', array('route' => 'domain_customer_email'))->setAttribute('icon', 'icon-envelope-alt');
        $menu['CRM']->addChild('Promotion')->setAttribute('icon', 'icon-trello')->setAttribute('dropdown', true);
        $menu['CRM']['Promotion']->addChild('SMS', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-phone');
        $menu['CRM']['Promotion']->addChild('Email', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-envelope-alt');
        $menu['CRM']->addChild('Staff')->setAttribute('icon', 'icon-foursquare')->setAttribute('dropdown', true);
        $menu['CRM']['Staff']->addChild('SMS', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-phone');
        $menu['CRM']['Staff']->addChild('Email', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-envelope-alt');
        $menu['CRM']->addChild('Inbox')->setAttribute('icon', 'icon-envelope')->setAttribute('dropdown', true);
        $menu['CRM']['Inbox']->addChild('SMS', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-phone');
        $menu['CRM']['Inbox']->addChild('Email', array('route' => 'domain_customer'))->setAttribute('icon', 'icon-envelope-alt');
        return $menu;

    }

    public function ReservationMenu($menu)
    {
        $menu
            ->addChild('Reservation')
            ->setAttribute('icon', 'fa fa-cog')
            ->setAttribute('dropdown', true);
        return $menu;

    }

    public function InstituteSystemMenu($menu)
    {
        $menu
            ->addChild('IMS')
            ->setAttribute('icon', 'fa fa-cog')
            ->setAttribute('dropdown', true);
        return $menu;

    }

    public function BillingSystemMenu($menu)
    {
        $menu
            ->addChild('Billing System')
            ->setAttribute('icon', 'fa fa-cog')
            ->setAttribute('dropdown', true);
        return $menu;

    }

    public function PayrollMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $menu
            ->addChild('HR & Payroll')
            ->setAttribute('icon', 'fa fa-group')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_HR_EMPLOYEE')) {

            $menu['HR & Payroll']->addChild('Human Resource')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Human Resource']->addChild('Employee', array('route' => 'domain_user'))->setAttribute('icon', 'icon-user');
        }
        if ($securityContext->isGranted('ROLE_HR_ATTENDANCE')) {
            $menu['HR & Payroll']->addChild('Attendance')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Attendance']->addChild('Employee', array('route' => 'attendance'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']['Attendance']->addChild('Leave Setup', array('route' => 'leave_setup'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']['Attendance']->addChild('Daily Attendance', array('route' => 'daily_attendance'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']['Attendance']->addChild('Calendar Weekend', array('route' => 'weekend'))->setAttribute('icon', 'icon-user');
        }
        if ($securityContext->isGranted('ROLE_PAYROLL_SALARY')) {

            $menu['HR & Payroll']->addChild('Payroll')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Payroll']->addChild('Salary Transaction', array('route' => 'account_paymentsalary'))->setAttribute('icon', 'icon-th-list');
            $menu['HR & Payroll']['Payroll']->addChild('Payment Salary', array('route' => 'account_paymentsalary_employee'))->setAttribute('icon', 'icon-th-list');
            $menu['HR & Payroll']['Payroll']->addChild('Salary Invoice', array('route' => 'account_salarysetting'))->setAttribute('icon', 'icon-th-list');
        }



        if ($securityContext->isGranted('ROLE_ADMIN')) {

            $menu['HR & Payroll']->addChild('Manage Agent')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Manage Agent']->addChild('Agent New', array('route' => 'agent_new'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']['Manage Agent']->addChild('Agent', array('route' => 'agent'))->setAttribute('icon', 'icon-user');
            $menu['HR & Payroll']->addChild('Agent Payroll')->setAttribute('icon', 'icon-group')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Agent Payroll']->addChild('Agent Transaction', array('route' => 'agentpayment'))->setAttribute('icon', 'icon-th-list');
            $menu['HR & Payroll']['Agent Payroll']->addChild('Agent Invoice', array('route' => 'agentpayment_invoice'))->setAttribute('icon', 'icon-th-list');
        }
        return $menu;

    }

    public function manageDomainInvoiceMenu($menu)
    {
        $securityContext = $this->container->get('security.context');
        $globalOption = $securityContext->getToken()->getUser()->getGlobalOption();
        $menu
            ->addChild('Invoice Sms & Email')
            ->setAttribute('icon', 'fa fa-files-o')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_SMS_MANAGER')) {
            $menu['Invoice Sms & Email']->addChild('Manage Sms')->setAttribute('icon', 'icon-phone')->setAttribute('dropdown', true);
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Logs', array('route' => 'smssender'))->setAttribute('icon', 'icon-phone');
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-money');
            $menu['Invoice Sms & Email']->addChild('Invoice Application', array('route' => 'invoicemodule_domain'))->setAttribute('icon', 'fa fa-files-o');
        }
        if ($securityContext->isGranted('ROLE_SMS_BULK')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Bulk Sms', array('route' => 'smsbulk'))->setAttribute('icon', 'icon-envelope');
        }
        if ($securityContext->isGranted('ROLE_SMS_CONFIG')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Notification Setup', array('route' => 'domain_notificationconfig'))->setAttribute('icon', 'fa fa-bell ');
        }
        return $menu;
    }


    public function manageSystemAccountMenu($menu)
    {
        $menu
            ->addChild('System Transaction')
            ->setAttribute('icon', 'fa fa-money')
            ->setAttribute('dropdown', true);
        $menu['System Transaction']->addChild('Bank', array('route' => 'bankaccount'))->setAttribute('icon', 'icon-money');
        $menu['System Transaction']->addChild('Mobile Bank', array('route' => 'mobilebankaccount'))->setAttribute('icon', 'icon-money');
        return $menu;
    }

    public function manageDomainMenu($menu)
    {
        $menu
            ->addChild('Manage Domain')
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('dropdown', true);
        $menu['Manage Domain']->addChild('Setting Package')->setAttribute('icon', ' icon-cogs')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Setting Package']->addChild('Application', array('route' => 'applicationpricing'))->setAttribute('icon', 'icon-briefcase');
        $menu['Manage Domain']['Setting Package']->addChild('SMS/Email', array('route' => 'smspricing'))->setAttribute('icon', 'icon-envelope');
        $menu['Manage Domain']->addChild('Manage Operation')->setAttribute('icon', 'icon-cog')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Manage Operation']->addChild('Domain', array('route' => 'tools_domain'))->setAttribute('icon', 'fa fa-server');
        $menu['Manage Domain']->addChild('Manage Invoice')->setAttribute('icon', 'icon-money')->setAttribute('dropdown', true);
        $menu['Manage Domain']['Manage Invoice']->addChild('Customer Invoice', array('route' => 'invoicemodule'))->setAttribute('icon', 'icon-money');
        $menu['Manage Domain']['Manage Invoice']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'))->setAttribute('icon', 'icon-money');

        return $menu;
    }


    public function manageFrontendMenu($menu)
    {
        $menu
            ->addChild('Manage Frontend')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);

        $menu['Manage Frontend']->addChild('Site Slider', array('route' => 'siteslider'));
        $menu['Manage Frontend']->addChild('Site Content', array('route' => 'sitecontent'));
        $menu['Manage Frontend']->addChild('Manage Mega Menu', array('route' => 'megamenu'));
        $menu['Manage Frontend']->addChild('Feature Category', array('route' => 'category_sorting'));
        $menu['Manage Frontend']->addChild('Collection', array('route' => 'collection'));

        return $menu;

    }

    public function toolsMenu($menu)
    {
        $menu
            ->addChild('Tools')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);

        $menu['Tools']->addChild('Manage Option', array('route' => 'globaloption'));
        $menu['Tools']->addChild('Manage Setting', array('route' => 'sitesetting'));
        $menu['Tools']->addChild('Location', array('route' => 'location'));
        $menu['Tools']->addChild('Business Sector', array('route' => 'syndicate'));
        $menu['Tools']->addChild('Designation', array('route' => 'designation'));
        $menu['Tools']->addChild('Course', array('route' => 'course'));
        $menu['Tools']->addChild('Institute Level', array('route' => 'institutelevel'));
        $menu['Tools']->addChild('Syndicate Module', array('route' => 'syndicatemodule'));
        $menu['Tools']->addChild('Application Module', array('route' => 'appmodule'));
        $menu['Tools']->addChild('Application Testimonial', array('route' => 'applicationtestimonial'));
        $menu['Tools']->addChild('Module', array('route' => 'module'));
        $menu['Tools']->addChild('Theme', array('route' => 'theme'));
        $menu['Tools']->addChild('Menu Custom', array('route' => 'menucustom'));
        $menu['Tools']->addChild('Menu Group', array('route' => 'menugroup'));
        $menu['Tools']->addChild('Manage Brand', array('route' => 'branding'));
        /*    $menu['Tools']->addChild('Inventory&Accounting')
                ->setAttribute('icon','icon icon-reorder')
                ->setAttribute('dropdown', true);
            $menu['Tools']['Inventory&Accounting']->addChild('Color', array('route' => 'color'))->setAttribute('icon', 'icon-th-list');
            $menu['Tools']['Inventory&Accounting']->addChild('Size', array('route' => 'size'))->setAttribute('icon', 'icon-th-list');
            $menu['Tools']['Inventory&Accounting']->addChild('Account Head', array('route' => 'accounthead'))->setAttribute('icon','fa fa-money');*/

        return $menu;
    }

    public function syndicateMenu($menu)
    {
        $menu
            ->addChild('Syndicate')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);

        $menu['Syndicate']->addChild('Education', array('route' => 'education'));
        $menu['Syndicate']->addChild('Vendor', array('route' => 'vendor'));
        return $menu;
    }

    public function productCategoryMenu($menu)
    {
        $menu
            ->addChild('Product Category')
            ->setAttribute('icon', 'fa fa-bookmark')
            ->setAttribute('dropdown', true);

        $menu['Product Category']->addChild('Add Category', array('route' => 'category_new'));
        $menu['Product Category']->addChild('Listing', array('route' => 'category'));
        return $menu;
    }

    public function appearanceMenu($menu)
    {


    }

    public function manageAdvertismentMenu($menu)
    {
        $menu
            ->addChild('Manage Advertisment')
            ->setAttribute('icon', 'fa fa-bookmark')
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

    public function frontendEommerceMenu(FactoryInterface $factory, array $options)
    {

        $subdomain = $this->container->get('router')->getContext()->getParameter('subdomain');
        $menu = $factory->createItem('root');
        $menus = $this->container->get('doctrine')->getRepository('SettingAppearanceBundle:EcommerceMenu')->getActiveMenus($subdomain);

        $categoryRepository = $this->container->get('doctrine')->getRepository('ProductProductBundle:Category');
        foreach ($menus as $item) {

            /** @var EcommerceMenu $item */

            $menuName = $item->getName();
            $menu
                ->addChild($menuName)
                ->setAttribute('dropdown', true);
            $this->buildDomainCategoryMenus($menu[$menuName], $categoryRepository->buildCategoryGroup($item->getCategories()));
            $this->buildDomainBrandMenu($menu[$menuName], $item->getBrands());
            $this->buildDomainPromotionMenu($menu[$menuName], $item->getPromotions());
            $this->buildDomainTagMenu($menu[$menuName], $item->getTags());
            $this->buildDomainDiscountMenu($menu[$menuName], $item->getDiscounts());
            $this->buildDomainFeatureMenu($menu[$menuName], $item->getFeatures());
            // $this->buildDomainPromotionMenu($menu[$menuName], $item->getCollections($subdomain));
            //$this->buildDomainTagMenu($menu[$menuName], $item->getCollections($subdomain));
            // $this->buildBrandMenu($menu[$menuName], $item->getBrands());
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

    public function manageApplicationSettingMenu($menu)
    {
        $menu
            ->addChild('Application Setting')
            ->setAttribute('icon', 'fa fa-cog')
            ->setAttribute('dropdown', true);
        $menu['Application Setting']->addChild('Account Head', array('route' => 'accounthead'))->setAttribute('icon', 'icon-th-list');
        $menu['Application Setting']->addChild('Transaction Method', array('route' => 'transactionmethod_new'))->setAttribute('icon', 'icon-th-list');
        $menu['Application Setting']->addChild('Color', array('route' => 'color'))->setAttribute('icon', 'icon-th-list');
        $menu['Application Setting']->addChild('Size', array('route' => 'size'))->setAttribute('icon', 'icon-th-list');
        $menu['Application Setting']->addChild('Hospital Category', array('route' => 'hms_category'))->setAttribute('icon', 'icon-th-list');
        return $menu;

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
            ->setAttribute('icon', 'fa fa-bookmark')
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
