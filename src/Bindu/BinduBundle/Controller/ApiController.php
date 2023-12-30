<?php

namespace Bindu\BinduBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\PaymentCard;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiController extends Controller
{

    public function checkApiValidation($request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $unique = $request->headers->get('X-API-SECRET');
        $setup = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $unique,'status'=>1));
        if (!empty($setup) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return $setup;
        }
        return "invalid";
    }

    public function setupAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $uniqueCode = $formData['uniqueCode'];
        $mobile = $formData['mobile'];
        $deviceId = $formData['deviceId'];
        $data = array();
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $uniqueCode,'mobile' => $mobile,'status'=>1));
        if (empty($entity) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
                return new Response('Unauthorized access.', 401);
        }else{

            /* @var $entity GlobalOption */

            $device = $this->getDoctrine()->getRepository('SettingToolBundle:AndroidDeviceSetup')->insert($entity,$deviceId);
            if($device) {
                $address = '';
                $vatRegNo = '';
                $vatPercentage = '';
                $vatEnable = '';
                $printFooter = "";
                if ($entity->getMainApp()->getSlug() == "miss") {
                    $address = $entity->getMedicineConfig()->getAddress();
                    $vatPercentage = $entity->getMedicineConfig()->getVatPercentage();
                    $vatRegNo = $entity->getMedicineConfig()->getVatRegNo();
                    $vatEnable = $entity->getMedicineConfig()->isVatEnable();
                    $footerMessage = $entity->getMedicineConfig()->getPrintFooterText();
                    if($footerMessage){
                        $printFooter = $footerMessage;
                    }else{
                        $printFooter = "Thanks For Visiting our pharmacy";
                    }
                } elseif ($entity->getMainApp()->getSlug() == "business") {
                    $address = $entity->getBusinessConfig()->getAddress();
                    $vatPercentage = $entity->getBusinessConfig()->getVatPercentage();
                    $vatRegNo = $entity->getBusinessConfig()->getVatRegNo();
                    $vatEnable = $entity->getBusinessConfig()->getVatEnable();
                    $footerMessage = $entity->getBusinessConfig()->getPrintFooterText();
                    if($footerMessage){
                        $printFooter = $footerMessage;
                    }else{
                        $printFooter = "Thanks For Visiting our business store";
                    }
                } elseif ($entity->getMainApp()->getSlug() == "restaurant") {
                    $address = $entity->getRestaurantConfig()->getAddress();
                    $vatPercentage = $entity->getRestaurantConfig()->getVatPercentage();
                    $vatRegNo = $entity->getRestaurantConfig()->getVatRegNo();
                    $vatEnable = $entity->getRestaurantConfig()->getVatEnable();
                    $footerMessage = $entity->getRestaurantConfig()->getPrintFooterText();
                    if($footerMessage){
                        $printFooter = $footerMessage;
                    }else{
                        $printFooter = "Thanks For Visiting our restaurant";
                    }
                } elseif ($entity->getMainApp()->getSlug() == "inventory") {
                    $address = $entity->getInventoryConfig()->getAddress();
                    $vatPercentage = $entity->getInventoryConfig()->getVatPercentage();
                    $vatRegNo = $entity->getInventoryConfig()->getVatRegNo();
                    $vatEnable = $entity->getInventoryConfig()->getVatEnable();
                    $footerMessage = $entity->getInventoryConfig()->getPrintFooterText();
                    if($footerMessage){
                        $printFooter = $footerMessage;
                    }else{
                        $printFooter = "Thanks For Visiting our shop";
                    }
                }
                $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();
                $data = array(
                    'setupId' => $entity->getId(),
                    'deviceId' => $device,
                    'uniqueCode' => $entity->getUniqueCode(),
                    'name' => $entity->getName(),
                    'mobile' => $mobile,
                    'email' => $entity->getEmail(),
                    'locationId' => $entity->getLocation()->getId(),
                    'address' => $address,
                    'locationName' => $printFooter,
                    'main_app' => $entity->getMainApp()->getId(),
                    'main_app_name' => $entity->getMainApp()->getSlug(),
                    'appsManual' => $entity->getMainApp()->getApplicationManual(),
                    'website' => $entity->getDomain(),
                    'vatRegNo' => $vatRegNo,
                    'vatPercentage' => $vatPercentage,
                    'vatEnable' => $vatEnable,
                );
            }
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }


    function hex6ToHex8($hex6) {
        return str_replace("#","0xFF",$hex6);
    }

    function random_color_code() {
        $red = str_pad(dechex(rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $green = str_pad(dechex(rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $blue = str_pad(dechex(rand(0, 255)), 2, '0', STR_PAD_LEFT);
        return '#' . $red . $green . $blue;
    }

    public function stringNullChecker($var){
        if (is_null($var)) {
            return "";
        } else {
            return (string) $var;
        }
    }

    public function numberNullChecker($var){
        if (is_null($var)) {
            return 0;
        } else {
            return (float) $var;
        }
    }

    public function splashAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $uniqueCode = $formData['uniqueCode'];
        $mobile = $formData['mobile'];
        $deviceId = $formData['deviceId'];
        $data = array();
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $uniqueCode,'mobile' => $mobile,'status'=>1));
        if (empty($entity) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return new Response('Unauthorized access.', 401);
        }else {

            $device = $this->getDoctrine()->getRepository('SettingToolBundle:AndroidDeviceSetup')->insert($entity, $deviceId);
            if ($device) {
                /* @var $entity GlobalOption */

                $address = '';
                $vatRegNo = '';
                $vatPercentage = '';
                $vatEnable = '';
                $printFooter = "";

                if ($entity->getMainApp()->getSlug() == "miss") {
                    $address = $entity->getMedicineConfig()->getAddress();
                    $vatPercentage = $entity->getMedicineConfig()->getVatPercentage();
                    $vatRegNo = $entity->getMedicineConfig()->getVatRegNo();
                    $vatEnable = $entity->getMedicineConfig()->isVatEnable();
                    $footerMessage = $entity->getMedicineConfig()->getPrintFooterText();
                    if ($footerMessage) {
                        $printFooter = $footerMessage;
                    } else {
                        $printFooter = "Thanks For Visiting our pharmacy";
                    }
                } elseif ($entity->getMainApp()->getSlug() == "business") {
                    $address = $entity->getBusinessConfig()->getAddress();
                    $vatPercentage = $entity->getBusinessConfig()->getVatPercentage();
                    $vatRegNo = $entity->getBusinessConfig()->getVatRegNo();
                    $vatEnable = $entity->getBusinessConfig()->getVatEnable();
                    $footerMessage = $entity->getBusinessConfig()->getPrintFooterText();
                    if ($footerMessage) {
                        $printFooter = $footerMessage;
                    } else {
                        $printFooter = "Thanks For Visiting our business store";
                    }
                } elseif ($entity->getMainApp()->getSlug() == "restaurant") {
                    $address = $entity->getRestaurantConfig()->getAddress();
                    $vatPercentage = $entity->getRestaurantConfig()->getVatPercentage();
                    $vatRegNo = $entity->getRestaurantConfig()->getVatRegNo();
                    $vatEnable = $entity->getRestaurantConfig()->getVatEnable();
                    $footerMessage = $entity->getRestaurantConfig()->getPrintFooterText();
                    if ($footerMessage) {
                        $printFooter = $footerMessage;
                    } else {
                        $printFooter = "Thanks For Visiting our restaurant";
                    }
                } elseif ($entity->getMainApp()->getSlug() == "inventory") {
                    $address = $entity->getInventoryConfig()->getAddress();
                    $vatPercentage = $entity->getInventoryConfig()->getVatPercentage();
                    $vatRegNo = $entity->getInventoryConfig()->getVatRegNo();
                    $vatEnable = $entity->getInventoryConfig()->getVatEnable();
                    $footerMessage = $entity->getInventoryConfig()->getPrintFooterText();
                    if ($footerMessage) {
                        $printFooter = $footerMessage;
                    } else {
                        $printFooter = "Thanks For Visiting our shop";
                    }
                }
                $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();
                $androidHeaderBg = (string)trim($entity->getTemplateCustomize()->getMobileHeaderBgColor());
                $bodyBg = (string)trim($entity->getTemplateCustomize()->getBodyColor());
                $appPrimaryColor = (string)trim($entity->getTemplateCustomize()->getAppPrimaryColor());
                $appSecondaryColor = (string)trim($entity->getTemplateCustomize()->getAppSecondaryColor());
                $appBarColor = (string)trim($entity->getTemplateCustomize()->getAppBarColor());
                $appTextTitle = (string)trim($entity->getTemplateCustomize()->getAppTextTitle());
                $appTextColor = (string)trim($entity->getTemplateCustomize()->getAppTextColor());
                $appCartColor = (string)trim($entity->getTemplateCustomize()->getAppCartColor());
                $appMoreColor = (string)trim($entity->getTemplateCustomize()->getAppMoreColor());
                $appDiscountColor = (string)trim($entity->getTemplateCustomize()->getAppDiscountColor());
                $appBorderActiveColor = (string)trim($entity->getTemplateCustomize()->getAppBorderActiveColor());
                $appBorderInactiveColor = (string)trim($entity->getTemplateCustomize()->getAppBorderInactiveColor());
                $appBorderColor = (string)trim($entity->getTemplateCustomize()->getAppBorderColor());
                $appPositiveColor = (string)trim($entity->getTemplateCustomize()->getAppPositiveColor());
                $appNegativeColor = (string)trim($entity->getTemplateCustomize()->getAppNegativeColor());
                $appIconColor = (string)trim($entity->getTemplateCustomize()->getAndroidIconColor());
                $appAnchorColor = (string)trim($entity->getTemplateCustomize()->getAndroidAnchorColor());
                $appAnchorHoverColor = (string)trim($entity->getTemplateCustomize()->getAndroidAnchorHoverColor());
                $searchPageBgColor = (string)trim($entity->getTemplateCustomize()->getSearchPageBgColor());
                $appFooterBgColor = (string)trim($entity->getTemplateCustomize()->getMobileFooterBgColor());
                $appFooterIconBgColor = (string)trim($entity->getTemplateCustomize()->getMobileFooterAnchorBg());
                $appFooterIconColor = (string)trim($entity->getTemplateCustomize()->getMobileFooterAnchorColor());
                $appFooterIconColorHover = (string)trim($entity->getTemplateCustomize()->getMobileFooterAnchorColorHover());

                $appSuccessColor = (string)trim($entity->getTemplateCustomize()->getAppSuccessColor());
                $appNoticeColor = (string)trim($entity->getTemplateCustomize()->getAppNoticeColor());
                $appCloseColor = (string)trim($entity->getTemplateCustomize()->getAppCloseColor());
                $morePageColor = (string)trim($entity->getTemplateCustomize()->getAppMoreColor());
                $inputBgColor = (string)trim($entity->getTemplateCustomize()->getInputBgColor());
                $inputBgFocusColor = (string)trim($entity->getTemplateCustomize()->getInputBgFocusColor());

                $tawk = (string)trim($entity->getTemplateCustomize()->getTawk());
                $pixel = (string)trim($entity->getTemplateCustomize()->getFacebookPixel());
                $messenger = (string)trim($entity->getTemplateCustomize()->getFbMessenger());
                $analytic = (string)trim($entity->getTemplateCustomize()->getGoogleAnalytic());

                $logo = empty($entity->getTemplateCustomize()->getWebPath('logo')) ? "" : $entity->getTemplateCustomize()->getWebPath('logo');
                //$introImage = empty($entity->getTemplateCustomize()->getWebPath('androidLogo')) ? '': $entity->getTemplateCustomize()->getWebPath('androidLogo');
                $logo = '';
                $introImage = '';

                $data = array(

                    'deviceId' => $device,
                    'vatEnable' => $vatEnable,
                    'setupId' => $entity->getId(),
                    'uniqueCode' => $entity->getUniqueCode(),
                    'name' => $entity->getName(),
                    'mobile' => $mobile,
                    'tawk' => $this->stringNullChecker($tawk),
                    'pixel' => $this->stringNullChecker($pixel),
                    'messenger' => $this->stringNullChecker($messenger),
                    'analytic' => $this->stringNullChecker($analytic),
                    'email' => $this->stringNullChecker($entity->getEmail()),
                    'locationId' => $entity->getLocation()->getId(),
                    'address' => $this->stringNullChecker($address),
                    'locationName' => $this->stringNullChecker($entity->getLocation()->getName()),
                    'main_app' => $entity->getMainApp()->getId(),
                    'main_app_name' => $entity->getMainApp()->getSlug(),
                    'mainApp' => $entity->getMainApp()->getId(),
                    'mainAppName' => $entity->getMainApp()->getSlug(),
                    'appsManual' => $this->stringNullChecker($entity->getMainApp()->getApplicationManual()),
                    'website' => $this->stringNullChecker($entity->getDomain()),
                    'vatRegNo' => $this->stringNullChecker($vatRegNo),
                    'vatPercentage' => $this->numberNullChecker($vatPercentage),
                    'bodyBg' => empty($bodyBg) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($bodyBg),
                    'appHeaderBg' => empty($androidHeaderBg) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($androidHeaderBg),
                    'appPrimaryColor' => empty($appPrimaryColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appPrimaryColor),
                    'appSecondaryColor' => empty($appSecondaryColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appSecondaryColor),
                    'appBarColor' => empty($appBarColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appBarColor),
                    'appTextTitle' => empty($appTextTitle) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appTextTitle),
                    'appTextColor' => empty($appTextColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appTextColor),
                    'appCartColor' => empty($appCartColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appCartColor),
                    'appMoreColor' => empty($appMoreColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appMoreColor),
                    'appBorderColor' => empty($appBorderColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appBorderColor),
                    'appBorderActiveColor' => empty($appBorderActiveColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appBorderActiveColor),
                    'appBorderInactiveColor' => empty($appBorderInactiveColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appBorderInactiveColor),
                    'appPositiveColor' => empty($appPositiveColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appPositiveColor),
                    'appNegativeColor' => empty($appNegativeColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appNegativeColor),
                    'appDiscountColor' => empty($appDiscountColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appDiscountColor),
                    'appIconColor' => empty($appIconColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appIconColor),
                    'appAnchorColor' => empty($appAnchorColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appAnchorColor),
                    'appAnchorHoverColor' => empty($appAnchorHoverColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appAnchorHoverColor),
                    'searchPageBgColor' => empty($searchPageBgColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($searchPageBgColor),
                    'morePageBgColor' => empty($morePageColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($morePageColor),
                    'appFooterBgColor' => empty($appFooterBgColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appFooterBgColor),
                    'appFooterIconBgColor' => empty($appFooterIconBgColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appFooterIconBgColor),
                    'appFooterIconColor' => empty($appFooterIconColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appFooterIconColor),
                    'appFooterIconColorHover' => empty($appFooterIconColorHover) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appFooterIconColorHover),
                    'appSuccessColor' => empty($appSuccessColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appSuccessColor),
                    'appNoticeColor' => empty($appNoticeColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appNoticeColor),
                    'appCloseColor' => empty($appCloseColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appCloseColor),
                    'inputBgColor' => empty($inputBgColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($inputBgColor),
                    'inputBgFocusColor' => empty($inputBgFocusColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($inputBgFocusColor),
                    // 'logo'      =>  $this->imageBase64($logo),
                    'logo' => '',
                    'intro' => '',
                );
            }

            $slides = array();
            $introPages = array();
            $users = $this->getDoctrine()->getRepository('UserBundle:User')->getCustomers($entity);
            $customers = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->getApiCustomer($entity);

            if($entity->getMainApp()->getSlug() == 'miss'){
                $stocks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getApiSpalshStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $stocks = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $stocks = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $stocks = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getApiStock($entity);
            }

            if($entity->getMainApp()->getSlug() == 'miss'){
                $categories = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->getApiCategory($entity);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $categories = $this->getDoctrine()->getRepository('RestaurantBundle:Category')->getApiCategory($entity);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $categories = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getApiCategory($entity);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $categories = $this->getDoctrine()->getRepository('BusinessBundle:Category')->getApiCategory($entity);
            }

            if($entity->getMainApp()->getSlug() == 'miss'){
                $vendors = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->getApiVendor($entity);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->getApiVendor($entity);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $vendors = $this->getDoctrine()->getRepository('InventoryBundle:Vendor')->getApiVendor($entity);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->getApiVendor($entity);
            }

            $result = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findAll();
            $methods = array();

            /* @var $row TransactionMethod */

            foreach($result as $key => $row) {
                $methods[$key]['item_id']              = (int) $row->getId();
                $methods[$key]['name']                 = $row->getName();
            }

            $paymentCards = $this->getDoctrine()->getRepository('SettingToolBundle:PaymentCard')->findAll();

            $cards = array();

            /* @var $row PaymentCard */

            foreach($paymentCards as $key => $row) {
                $cards[$key]['item_id']              = (int) $row->getId();
                $cards[$key]['name']                 = $row->getName();
            }

            $accountBanks = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption'=>$entity,'status'=>1));

            $banks = array();

            /* @var $row AccountBank */

            if($accountBanks){
                foreach($accountBanks as $key => $row) {
                    $banks[$key]['global_id']            = (int) $entity->getId();
                    $banks[$key]['item_id']              = (int) $row->getId();
                    $banks[$key]['name']                 = $row->getName();
                    $banks[$key]['service_charge']       = $row->getServiceCharge();
                }
            }

            $accountMobileBanks = $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption'=>$entity,'status'=>1));
            $mobiles = array();

            /* @var $row AccountMobileBank */

            if($accountMobileBanks) {
                foreach ($accountMobileBanks as $key => $row) {
                    $mobiles[$key]['global_id'] = (int)$entity->getId();
                    $mobiles[$key]['item_id'] = (int)$row->getId();
                    $mobiles[$key]['name'] = $row->getName();
                    $mobiles[$key]['service_charge'] = $row->getServiceCharge();
                }
            }


        }
        $jsonData = array(
            'setup'             => $data,
            'users'             => $users,
            'customers'         => $customers,
            'categories'        => $categories,
            'vendors'           => $vendors,
            'stocks'            => $stocks,
            'transaction_methods' => $methods,
            'payment_cards' => $cards,
            'bank_accounts' => $banks,
            'mobile_accounts' => $mobiles,

        );
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($jsonData));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    public function systemUsersAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('UserBundle:User')->getCustomers($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function dashboardAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{
            $entity = $this->checkApiValidation($request);
            $deviceId = $request->headers->get('X-DEVICE-ID');
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
            $data['device'] = $deviceId;
            $purchaseCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->androidDevicePurchaseOverview($entity,$data);
            $salesCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->androidDeviceSalesOverview($entity,$data);
            $expenditureOverview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->androidDeviceExpenditureOverview($entity,$data);
            $data = array(
                'globalOption'              => $entity->getId(),
                'expenditureOverview'       => $expenditureOverview ,
                'totalSales'                => $salesCashOverview['total'] ,
                'salesReceive'              => $salesCashOverview['salesReceive'] ,
                'salesVoucher'              => $salesCashOverview['voucher'] ,
                'purchaseTotal'             => $purchaseCashOverview['total'] ,
                'purchasePayment'           => $purchaseCashOverview['payment'] ,
            );

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function StockItemAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            if($entity->getMainApp()->getSlug() == 'miss'){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $data = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getApiStock($entity);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function customerAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->getApiCustomer($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function unitAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $data = $this->getDoctrine()->getRepository('SettingToolBundle:ProductUnit')->getApiUnit();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function categoryAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            if($entity->getMainApp()->getSlug() == 'miss'){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->getApiCategory($entity);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $data = $this->getDoctrine()->getRepository('RestaurantBundle:Category')->getApiCategory($entity);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getApiCategory($entity);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $data = $this->getDoctrine()->getRepository('BusinessBundle:Category')->getApiCategory($entity);
            }

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function vendorAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {
            return new Response('Unauthorized access.', 401);
        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            if($entity->getMainApp()->getSlug() == 'miss'){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->getApiVendor($entity);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $data = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->getApiVendor($entity);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $data = $this->getDoctrine()->getRepository('InventoryBundle:Vendor')->getApiVendor($entity);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $data = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->getApiVendor($entity);
            }

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function transactionMethodAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $result = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findAll();
            $data = array();

            /* @var $row TransactionMethod */

            foreach($result as $key => $row) {
                $data[$key]['item_id']              = (int) $row->getId();
                $data[$key]['name']                 = $row->getName();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function paymentCardAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $result = $this->getDoctrine()->getRepository('SettingToolBundle:PaymentCard')->findAll();
            $data = array();

            /* @var $row TransactionMethod */

            foreach($result as $key => $row) {
                $data[$key]['item_id']              = (int) $row->getId();
                $data[$key]['name']                 = $row->getName();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiBankAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption'=>$entity,'status'=>1));

            $data = array();

            /* @var $row AccountBank */

            if($result){
                foreach($result as $key => $row) {
                    $data[$key]['global_id']            = (int) $entity->getId();
                    $data[$key]['item_id']              = (int) $row->getId();
                    $data[$key]['name']                 = $row->getName();
                    $data[$key]['service_charge']       = $row->getServiceCharge();
                }
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiMobileAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption'=>$entity,'status'=>1));
            $data = array();

            /* @var $row AccountMobileBank */

            if($result) {
                foreach ($result as $key => $row) {
                    $data[$key]['global_id'] = (int)$entity->getId();
                    $data[$key]['item_id'] = (int)$row->getId();
                    $data[$key]['name'] = $row->getName();
                    $data[$key]['service_charge'] = $row->getServiceCharge();
                }
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function expenditureCategoryAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->getApiCategory($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function templateCustomizationAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);

            /* @var $template TemplateCustomize */

            $baseurl = "";
            $template = $entity->getTemplateCustomize();
            if($template->getAndroidLogo()){
                $dir = $template->getUploadDir();
                $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/'.$dir.'/'.$template->getAndroidLogo();
            }

            $data = array(
                'globalOption'                      => $entity->getId(),
                'androidLogo'                       => $baseurl,
                'androidHeaderBg'                   => $template->getAndroidHeaderBg() ,
                'androidMenuBg'                     => $template->getAndroidMenuBg() ,
                'androidMenuBgHover'                => $template->getAndroidMenuBgHover() ,
                'androidAnchorColor'                => $template->getAndroidAnchorColor() ,
                'androidAnchorHoverColor'           => $template->getAndroidAnchorHoverColor() ,
           );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function medicineDimsAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = array('offset' => 0,'limit' => 2000);
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->getApiDims($entity,$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function discountCouponAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('RestaurantBundle:Coupon')->getApiDiscountCoupon($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function tokenNoAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getApiRestaurantToken($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }


    public function apiPingAction(Request $request){

        $data = $request->request->all();
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode(array('message'=>$data['message'], 'ipAddress'=>$ipAddress)));
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    public function apiResponseAction(Request $request, $data)
    {

    }

    public function apiSalesAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);


        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{


            $jsonInput = '[
            {
            "invoiceId":"051900113","subTotal":"1200","discount":"200","discountType":"Flat","discountCalculation":"10","vat":"0","total":"1000","receive":"800","due":"200","customerId":"223","customerName":"Jacky","customerMobile":"01828148148","addresss":"Dhaka","transactionMethod":"cash","bankAccount":"","mobileBankAccount":"","paymentMobile":"","paymentCard":"","paymentCardNo":"","transactionId":"","salesBy":"","created":"","createdBy":"","slipNo":"","tokenNo":"","discountCoupon":"","remark":""
            }
        ]';

        $jsonInputItem = '[
            {"salesId":"051900113","stockId":"7087","unitPrice":"120","quantity":"2","subTotal":"240"},
            {"salesId":"051900113","stockId":"1295","unitPrice":"120","quantity":"2","subTotal":"240"},
            {"salesId":"051900113","stockId":"7088","unitPrice":"120","quantity":"2","subTotal":"240"},
            {"salesId":"051900113","stockId":"7420","unitPrice":"120","quantity":"2","subTotal":"240"}
        ]';



            $data = $request->request->all();

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $deviceId = $request->headers->get('X-DEVICE-ID');

           // $data = array('item' => $jsonInput,'itemCount'=> 1,'subItem'=> $jsonInputItem,'subItemCount'=> 4);


            if($entity->getMainApp()->getSlug() == 'miss'){
                $this->getDoctrine()->getRepository('MedicineBundle:MedicineAndroidProcess')->insertAndroidProcess($entity,$deviceId,'sales',$data);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantAndroidProcess')->insertAndroidProcess($entity,$deviceId,'sales',$data);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $this->getDoctrine()->getRepository('InventoryBundle:InventoryAndroidProcess')->insertAndroidProcess($entity,$deviceId,'sales',$data);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessAndroidProcess')->insertAndroidProcess($entity,$deviceId,'sales',$data);
            }
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array('message'=> "success", 'ipAddress' => $ipAddress)));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiPurchaseAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);


        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $deviceId = $request->headers->get('X-DEVICE-ID');

            $jsonInput = '[
            {
            "invoiceId":"051900113","subTotal":"1200","discount":"200","discountType":"Flat","discountCalculation":"10","vat":"0","total":"1000","receive":"800","due":"200","vendorId":"223","transactionMethod":"cash","bankAccount":"","mobileBankAccount":"","created":"","createdBy":"","invoiceMode":"","remark":""
            }
        ]';

            $jsonInputItem = '[
            {"purchaseId":"051900113","stockId":"10717","purchasePrice":"25","quantity":"2","subTotal":"50"},
            {"purchaseId":"051900113","stockId":"10713","purchasePrice":"15","quantity":"2","subTotal":"30"},
            {"purchaseId":"051900113","stockId":"10707","purchasePrice":"120","quantity":"2","subTotal":"240"},
            {"purchaseId":"051900113","stockId":"10708","purchasePrice":"10","quantity":"2","subTotal":"20"}
        ]';


           // $data = array('deviceId' => 1,'item' => $jsonInput ,'itemCount'=> 1,'subItem'=>$jsonInputItem,'subItemCount'=> 4);

            $data = $request->request->all();
            if($entity->getMainApp()->getSlug() == 'miss'){
                $this->getDoctrine()->getRepository('MedicineBundle:MedicineAndroidProcess')->insertAndroidProcess($entity,$deviceId,'purchase',$data);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantAndroidProcess')->insertAndroidProcess($entity,$deviceId,'purchase',$data);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $this->getDoctrine()->getRepository('InventoryBundle:InventoryAndroidProcess')->insertAndroidProcess($entity,$deviceId,'purchase',$data);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessAndroidProcess')->insertAndroidProcess($entity,$deviceId,'purchase',$data);
            }
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array('message'=>"success", 'ipAddress'=>$ipAddress)));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiExpenseAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $deviceId = $request->headers->get('X-DEVICE-ID');
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $data = $request->request->all();
            $this->getDoctrine()->getRepository('AccountingBundle:ExpenseAndroidProcess')->insertAndroidProcess($entity,$ipAddress,$deviceId,'expenditure',$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array('message'=>"success", 'ipAddress'=>$ipAddress)));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiInvoiceTokenAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $request->request->all();
            $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->insertApiInvoiceToken($entity,$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode("success"));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function createStockAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $formArray = $request->request->all();
            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            if($entity->getMainApp()->getSlug() == 'miss'){
                $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->insertAndroidStock($entity,$formArray);
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $data = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getApiStock($entity);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function stockEditAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {
            return new Response('Unauthorized access.', 401);
        }else{

            /* @var $entity GlobalOption */
            $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            $entity = $this->checkApiValidation($request);
            $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($id);
            $array  = array();
            if($stock){
                /* @var $stock MedicineStock */
                $array = array(
                    'stockId'=> $stock->getId(),
                    'name' => $stock->getName(),
                    'category' => ($stock->getCategory()) ? $stock->getCategory()->getName() : "",
                    'brandName' => $stock->getBrandName(),
                    'price' => $stock->getSalesPrice(),
                    'unit' => ($stock->getUnit()) ? $stock->getUnit()->getName() : "",
                    'minQuantity' => $stock->getMinQuantity()
                );
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($array));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function stockUpdateAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();
            $entity = $this->checkApiValidation($request);
            $stockId = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateAndroidStock($data);
            if($entity->getMainApp()->getSlug() == 'miss'){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $data = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getApiStock($entity);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getApiStock($entity);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function stockDeleteAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();
            $entity = $this->checkApiValidation($request);
            $stockId = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            if($entity->getMainApp()->getSlug() == 'miss'){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getApiStockDelete($entity,$stockId);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $data = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getApiStockDelete($entity,$stockId);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getApiStockDelete($entity,$stockId);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getApiStockDelete($entity,$stockId);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function createCustomerAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $formArray = $request->request->all();
            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $status = $this->getDoctrine()->getRepository("DomainUserBundle:Customer")->apiCreateCustomer($entity,$formArray);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array('status'=> $status)));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function updateCustomerAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $formArray = $request->request->all();
            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $status = $this->getDoctrine()->getRepository("DomainUserBundle:Customer")->apiUpdateCustomer($entity,$formArray);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array('status'=> $status)));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function customerLedgerAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $formArray = $request->request->all();
            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $result = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->apiCustomerLedger($entity,$formArray);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($result));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function customerInvoiceAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $invoice = $request->request->get('invoice');
            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            if($entity->getMainApp()->getSlug() == 'miss'){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->getApiSalesInvoice($entity,$invoice);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $data = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getApiSalesInvoice($entity,$invoice);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getApiSalesInvoice($entity,$invoice);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getApiSalesInvoice($entity,$invoice);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function salesInvoiceMessageAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            if($entity->getMainApp()->getSlug() == 'miss'){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->apiInvoiceMessage($entity);
            }elseif($entity->getMainApp()->getSlug() == 'restaurant'){
                $data = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->apiInvoiceMessage($entity);
            }elseif($entity->getMainApp()->getSlug() == 'inventory'){
                $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->apiInvoiceMessage($entity);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->apiInvoiceMessage($entity);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function salesReceiveAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();
            /* @var $option GlobalOption */
            $option = $this->checkApiValidation($request);
            $status = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->apiInsertSalesReceive($option,$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array('status'=> $status)));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

}
