<?php

namespace Frontend\FrontentBundle\Controller;
use Frontend\FrontentBundle\Service\MobileDetect;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Product\Bundle\ProductBundle\Entity\Product;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\SubscribeEmail;
use Setting\Bundle\ToolBundle\Event\ReceiveEmailEvent;
use Setting\Bundle\ToolBundle\Event\ReceiveSmsEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Syndicate\Bundle\ComponentBundle\Entity\Education;
use Syndicate\Bundle\ComponentBundle\Entity\Vendor;

class SmsEmailReceiveController extends Controller
{

    public function smsReceiveAction(Request $request,$subdomain)
    {

        $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' =>$subdomain));

        $data = $request->request->all();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
       // $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertContactCustomer($globalOption,$data,$mobile);
      //  $customerInbox = $this->getDoctrine()->getRepository('DomainUserBundle:CustomerInbox')->sendCustomerMessage($customer,$data,'sms');

        if( $globalOption ->isSmsIntegration() == 1 AND !empty($globalOption->getMobile())){
        $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.sms_receive', new ReceiveSmsEvent($globalOption,$customerInbox));

        }
        return new Response("success");
    }

    public function newsLetterReceiveAction(Request $request,$subdomain)
    {
        /*$globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('status' => 1, 'subDomain' => $subdomain));
        $data = $request->request->all();
        if (isset($data['mobile'])){
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
            $data['mobile'] = $mobile;
        }

        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertContactCustomer($globalOption,$data);
        if(!empty($globalOption->getEmail())) {

            // $dispatcher = $this->container->get('event_dispatcher');
            // $dispatcher->dispatch('setting_tool.post.email_receive', new ReceiveEmailEvent($globalOption , $customerInbox));

        }*/
        return new Response("success");
    }

    public function emailReceiveAction(Request $request,$subdomain)
    {

        $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('status' => 1, 'subDomain' => $subdomain));
        $data = $request->request->all();
        if (isset($data['mobile'])){
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
            $data['mobile'] = $mobile;
        }

        //  $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertContactCustomer($globalOption,$data);
        // $customerInbox = $this->getDoctrine()->getRepository('DomainUserBundle:CustomerInbox')->sendCustomerMessage($customer,$data,'email');

        if(!empty($globalOption->getEmail())) {

           // $dispatcher = $this->container->get('event_dispatcher');
           // $dispatcher->dispatch('setting_tool.post.email_receive', new ReceiveEmailEvent($globalOption , $customerInbox));

        }
        return new Response('success');


    }

    public function captchaAction(Request $request){
        $data = $request->request->all();
        $data1 = $data['recaptcha_challenge_field'];
        $data2 = $data['recaptcha_response_field'];

        if($data1 == $data2){
            return new Response('true');
        }else{
            return new Response('false');
        }
        exit;
    }

}
