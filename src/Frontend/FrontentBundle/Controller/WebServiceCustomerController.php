<?php

namespace Frontend\FrontentBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Event\AssociationSmsEvent;
use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\CustomerRegisterType;
use Frontend\FrontentBundle\Service\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class WebServiceCustomerController extends Controller
{


   public function registerAction($subdomain)
    {

        $entity = new User();
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $form   = $this->createCreateForm($subdomain,$entity);
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':register.html.twig',
                array(
                    'globalOption'  => $globalOption,
                    'pageName'      => 'register',
                    'form'   => $form->createView(),
                 )
            );
        }
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */

    private function createCreateForm($subdomain,User $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new CustomerRegisterType($location), $entity, array(
            'action' => $this->generateUrl($subdomain.'_webservice_customer_create'),
            'method' => 'POST',
            'attr' => array(
                'id' => 'signup',
                'class' => 'register',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a new User entity.
     *
     */

    public function userCheckingAction(Request $request)
    {
        $intlMobile = $request->query->get('Core_userbundle_user[profile][mobile]',NULL,true);
        $em = $this->getDoctrine()->getManager();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity = $em->getRepository('UserBundle:User')->findBy(array('username'=> $mobile));

        if( count($entity) > 0 ){
            $valid = 'false';
        }else{
            $valid = 'true';
        }
        return new Response($valid);
    }

    /**
     * Creates a new User entity.
     *
     */

    public function memberCheckingAction(Request $request)
    {

        $intlMobile = $request->query->get('registration_mobile',NULL,true);
        $em = $this->getDoctrine()->getManager();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity = $em->getRepository('UserBundle:User')->findBy(array('username'=> $mobile));
        if( count($entity) > 0 ){
            $valid = 'false';
        }else{
            $valid = 'true';
        }
        return new Response($valid);
    }

    /**
     * Creates a new User entity.
     *
     */

    public function userCheckingEmailAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $email = $request->query->get('Core_userbundle_user[email]',NULL,true);
        $entity = $em->getRepository('UserBundle:User')->findBy(array('email'=> $email));
        if( count($entity) > 0 ){
            $valid = 'false';
        }else{
            $valid = 'true';
        }
        return new Response($valid);
    }

    /**
     * Creates a new User entity.
     *
     */

    public function memberCheckingEmailAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $email = $request->query->get('registration_email',NULL,true);
        $entity = $em->getRepository('UserBundle:User')->findBy(array('email'=> $email));
        if( count($entity) > 0 ){
            $valid = 'false';
        }else{
            $valid = 'true';
        }
        return new Response($valid);
    }



    /**
     * Creates a new User entity.
     *
     */

    public function createAction($subdomain, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $form = $this->createCreateForm($subdomain,$entity);
        $form->handleRequest($request);
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if ($form->isValid()) {

            $intlMobile = $entity->getProfile()->getMobile();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            
            $entity->setPlainPassword("1234");
            $entity->setEnabled(true);
            $entity->setUsername($mobile);
            if(empty($entity->getEmail())){
                $entity->setEmail($mobile.'@gmail.com');
            }
            $entity->setGlobalOption($globalOption);
            $entity->setRoles(array('ROLE_BUYER'));
            $em->persist($entity);
            $em->flush();
            //$dispatcher = $this->container->get('event_dispatcher');
            //$dispatcher->dispatch('setting_tool.post.user_signup_msg', new \Setting\Bundle\ToolBundle\Event\UserSignup($entity));
            return $this->redirect($this->generateUrl('webservice_customer_confirm',array('subdomain' => $subdomain)));
        }
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() &&  $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':register.html.twig',
                array(
                    'globalOption'  => $globalOption,
                    'pageName'      => 'register',
                    'form'   => $form->createView(),
                )
            );
        }

    }

    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->container->get('security.context')->setToken($token);
    }

    public function insertAction($subdomain, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $form = $this->createCreateForm($subdomain,$entity);
        $form->handleRequest($request);
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $intlMobile = $entity->getProfile()->getMobile();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity->getProfile()->setMobile($mobile);
        $a = mt_rand(1000,9999);
        if ($form->isValid()) {
            $entity->setPlainPassword($a);
            $entity->setEnabled(true);
            $entity->setUsername($mobile);
            if(empty($entity->getEmail())){
                $entity->setEmail($mobile.'@gmail.com');
            }
            $entity->setGlobalOption($globalOption);
            $entity->setRoles(array('ROLE_BUYER'));
            $entity->setUserGroup('customer');
            $em->persist($entity);
            $em->flush();
            $token = new UsernamePasswordToken($entity, null, 'main', $entity->getRoles());
            $this->get('security.context')->setToken($token);
            $this->get('session')->set('_security_main',serialize($token));

           // $dispatcher = $this->container->get('event_dispatcher');
           // $dispatcher->dispatch('setting_tool.post.customer_signup_msg', new \Setting\Bundle\ToolBundle\Event\CustomerSignup($entity,$globalOption));
            return new Response('success');
        }else{
            return new Response('invalid');
        }
    }

    public function prescriptionAction($subdomain, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $form = $this->createCreateForm($subdomain,$entity);
        $form->handleRequest($request);
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $intlMobile = $entity->getProfile()->getMobile();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity->getProfile()->setMobile($mobile);
        $a = mt_rand(1000,9999);
        if ($form->isValid()) {
            $entity->setPlainPassword($a);
            $entity->setEnabled(true);
            $entity->setUsername($mobile);
            if(empty($entity->getEmail())){
                $entity->setEmail($mobile.'@gmail.com');
            }
            $entity->setGlobalOption($globalOption);
            $entity->setRoles(array('ROLE_BUYER'));
            $entity->setUserGroup('customer');
            $em->persist($entity);
            $em->flush();
            $token = new UsernamePasswordToken($entity, null, 'main', $entity->getRoles());
            $this->get('security.context')->setToken($token);
            $this->get('session')->set('_security_main',serialize($token));

            // $dispatcher = $this->container->get('event_dispatcher');
            // $dispatcher->dispatch('setting_tool.post.customer_signup_msg', new \Setting\Bundle\ToolBundle\Event\CustomerSignup($entity,$globalOption));
            return new Response('success');
        }else{
            return new Response('invalid');
        }
    }

    public function validateMobilex($mobile)
    {
        return preg_match('/^[0-9]{11}+$/', $mobile);
    }

    function validateMobile($phone)
    {
        // Allow +, - and . in phone number
        $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
        // Remove "-" from number
        $phone_to_check = str_replace("-", "", $filtered_phone_number);
        // Check the lenght of number
        // This can be customized if you want phone number from a specific country
        if (strlen($phone_to_check) < 10 || strlen($phone_to_check) > 14) {
            return "false";
        } else {
            return "true";
        }
    }

    public function insertMemberAction($subdomain, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $data = $request->request->all();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $intlMobile = (isset($data['registration_mobile']) and !empty($data['registration_mobile'])) ? $data['registration_mobile'] : "";
        $email = (isset($data['registration_email']) and !empty($data['registration_email'])) ? $data['registration_email'] : "";
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $exist = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('globalOption' => $globalOption,'username' => $mobile));
        if($globalOption and empty($exist) and $this->validateMobile($mobile) == true) {
            $entity->setPlainPassword("1234");
            $entity->setEnabled(true);
            $entity->setUsername($mobile);
            if ($email and filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $entity->setEmail($email);
            } else {
                $entity->setEmail($mobile.'@gmail.com');
            }
            $entity->setGlobalOption($globalOption);
            $entity->setRoles(array('ROLE_CUSTOMER','ROLE_MEMBER'));
            $entity->setUserGroup('customer');
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('UserBundle:Profile')->insertNewMember($entity, $data);
            $data = array('name' => $data['registration_name'],'email' => $data['registration_email'],'address' => $data['registration_address'],'facebookId' => $data['registration_facebookId'],'country' => $data['registration_country']);
            $token = new UsernamePasswordToken($entity, null, 'main', $entity->getRoles());
            $this->get('security.context')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertStudentMember($this->getUser() , $data);
            $msg = "Your account has been created, User name:{$this->getUser()}. Thank you. Be with www.{$this->getUser()->getGlobalOption()->getDomain()}";
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$globalOption,'user'=>$entity->getId()));
            if($customer->getCompany()){
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('appstore.customer.post.member_sms', new AssociationSmsEvent($customer, $msg));
            }
            $redirect = $this->generateUrl('domain_customer_homepage',array('shop' => $globalOption->getSlug()));
            return new Response($redirect);
        }
        return new Response('failed');
    }


    public function insertEcommerceCustomerAction($subdomain, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $data = $request->request->all();
        $mobile = isset($data['registration_mobile']) ? $data['registration_mobile'] :"";
        $name = isset($data['registration_name']) ? $data['registration_name'] :"";
        $email = isset($data['registration_email']) ? $data['registration_email'] :"";
        $address = isset($data['registration_address']) ? $data['registration_address'] :"";
        $location = isset($data['registration_location']) ? $data['registration_location'] :"";
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        if($globalOption) {
            $intlMobile = $mobile;
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            $entity->setPlainPassword("1234");
            $entity->setEnabled(true);
            $entity->setUsername($mobile);
            if (empty($email)) {
                $entity->setEmail($mobile . '@gmail.com');
            } else {
                $entity->setEmail($email);
            }
            $entity->setGlobalOption($globalOption);
            $entity->setRoles(array('ROLE_BUYER'));
            $entity->setUserGroup('customer');
            $em->persist($entity);
            $em->flush();
            $data = array('name' => $name ,'email' => $email,'address' => $address,'location' => $location);
            $this->getDoctrine()->getRepository('UserBundle:Profile')->insertEcommerce($entity, $data);
            $token = new UsernamePasswordToken($entity, null, 'main', $entity->getRoles());
            $this->get('security.context')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->eCommerceCustomer($this->getUser() , $data);
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.customer_signup_msg', new \Setting\Bundle\ToolBundle\Event\CustomerSignup($entity,$globalOption));
         //   $redirect = $this->generateUrl('domain_customer_homepage',array('shop' => $globalOption->getSlug()));
         //   return new Response($redirect);
            return new Response('success');
        }
        return new Response('failed');


    }

    public function updateEcommerceCustomerAction($subdomain, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $name = isset($data['registration_name']) ? $data['registration_name'] :"";
        $address = isset($data['registration_address']) ? $data['registration_address'] :"";
        $location = isset($data['registration_location']) ? $data['registration_location'] :"";
        $pickupMobile = isset($data['registration_additional_phone']) ? $data['registration_additional_phone'] :"";
        $data = array('name' => $name ,'address' => $address ,'pickupMobile' => $pickupMobile,'location' => $location);
        $this->getDoctrine()->getRepository('UserBundle:Profile')->updateEcommerce($this->getUser(), $data);
        return new Response('success');
    }

    public function confirmAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        $detect = new MobileDetect();
        if( $detect->isMobile() &&  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':login.html.twig',
            array(
                'globalOption'  => $globalOption,
                'entity' => $entity,
                'error' => '',
                'pageName'      => 'login',
                'csrf_token' => $csrfToken,
            )
        );
    }

    public function domainLoginAction($subdomain)
    {
        return $this->redirect('/');
    }

    public function domainCustomerHomeAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( ! $detect->isMobile() && ! $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':dashboard.html.twig',
            array(
                'globalOption'  => $globalOption,
            )
        );
    }


    public function customerLoginMobileValidationAction(Request $request)
    {
        $valid = 'Your registered mobile number is not valid.';

        $intlMobile = $request->query->get('mobile',NULL,true);
        $em = $this->getDoctrine()->getManager();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);

        $user = $em->getRepository('UserBundle:User')->findOneBy(array('username'=> $mobile,'enabled'=>1));
        /* @var $user User */
            if ($user) {
                $global = $user -> getGlobalOption();
                if ($global->getSmsSenderTotal() and $global->getSmsSenderTotal()->getRemaining() > 0 and $global->getNotificationConfig()->getSmsActive() == 1) {
                    $a = mt_rand(1000, 9999);
                    $user->setPlainPassword($a);
                    $this->get('fos_user.user_manager')->updateUser($user);
                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user, $a));
                    $valid = '4 digit OTP sent to your mobile number.';
                }
            }
        return new Response($valid);

    }

    public function mobileOtpAction($subdomain , Request $request)
    {
        $option = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $intlMobile =  $_REQUEST['mobile'];
        $otpCode = mt_rand(1000,9999);
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $msg = "{$option->getDomain()}, Your One-Time PIN is {$otpCode}. Please call for any support {$option->getHotline()}.";
        $mobileCode = "88".$mobile;
       // $response = $this->send($msg,$mobileCode);
     //   $items = json_decode($response,true);
      //  if($items['status'] == '201'){
            $this->get('session')->set('otpCode',$otpCode);
      //  }
        $array = (json_encode(array('status' => 201 ,'message' => '','mobile' => $mobile,'otpCode'=> $otpCode )));
        return new Response($array);

    }

    public function otpConfirmAction($subdomain , Request $request)
    {
        $option = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $data = $request->request->all();
        $otpCode = $data['otp'];
        $otp = $this->get('session')->get('otpCode');
        $intlMobile =  $data['resendMobile'];
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('username' => $mobile));
        if($otpCode == $otp and !empty($entity)){
            $entity->setGlobalOption($option);
            $this->get('fos_user.user_manager')->updateUser($entity);
            $token = new UsernamePasswordToken($entity, null, 'main', $entity->getRoles());
            $this->get('security.context')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            if(empty($entity->getProfile()->getName())){
                $array = (json_encode(array('status' => 'new')));
            }else{
                $array = (json_encode(array('status' => 'success')));
            }
        }elseif($otpCode == $otp and empty($entity)){
            $em = $this->getDoctrine()->getManager();
            $user = new User();
            $user->setPlainPassword("1234");
            $user->setEnabled(true);
            $user->setUsername($mobile);
            if (empty($email)) {
                $user->setEmail($mobile . '@gmail.com');
            } else {
                $user->setEmail($email);
            }
            $user->setGlobalOption($option);
            $user->setRoles(array('ROLE_SHOP'));
            $user->setUserGroup('customer');
            $em->persist($user);
            $em->flush();
            $data = array('name' => '' ,'email' => '','address' => '');
            $this->getDoctrine()->getRepository('UserBundle:Profile')->insertEcommerce($user, $data);
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.context')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $array = (json_encode(array('status' => 'new')));
        }else{
            $array = (json_encode(array('status' => 'invalid')));
        }
        return new Response($array);

    }



    public function customerForgetPasswordAction()
    {

        $mobile =  $_REQUEST['mobile'];
        $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('username' => $mobile));
        if($user){
            $a = mt_rand(1000,9999);
            $user->setPlainPassword($a);
            $this->get('fos_user.user_manager')->updateUser($user);
            $this->get('session')->getFlashBag()->add(
                'success',"Password reset successfully"
            );
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user,'123456'));
            echo 'Your reset password is '.$a;
        }else{
            echo 'This mobile '.$mobile.' is not correct,Please try another mobile no.';
        }
        exit;
    }

    function send($msg, $phone, $sender = ""){

        if(empty($sender)){
            $from = "03590602016";
        }else{
            $from = $sender;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.icombd.com/api/v1/campaigns/sms/1/text/single",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\"from\":\"{$from}\",\"text\":\"{$msg}\",\"to\":\"{$phone}\"}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Basic dW1hcml0OnVtYXJpdDE0OA=="
            ),
        ));
        $response = curl_exec($curl);
        return $response;

    }



}
