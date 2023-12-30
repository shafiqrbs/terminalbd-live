<?php

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class PasswordChangeSmsListener extends BaseSmsAwareListener
{
    /**
     * @var EntityManager
     */
    protected  $em;

    protected $doctrine;

    protected $gateway;

    public function  __construct(Registry $doctrine, SmsGateWay $gateway)
    {
        $this->doctrine = $doctrine;
        $this->gateway = $gateway;
        $this->em = $doctrine->getManager();
    }

    public function sendSms(PasswordChangeSmsEvent $event)
    {

        /**
         * @var PasswordChangeSmsEvent $event
         */

        $user = $event->getUser();
        $password = $event->getPassword();
      //  echo $user->getGlobalOption()->getOrganizationName();
        $msg = "Your login OTP code is - ".$password;
        $customer = $this->em->getRepository('DomainUserBundle:Customer')->findOneBy(array('user' => $user->getId()));
        if( empty($customer) or empty($customer->getCountry()) or ($customer->getCountry() and $customer->getCountry()->getCode() == "BD")){
            $mobile = "88".$user;
        }else{
            $mobile = "{$customer->getCountry()->getCode()}".$user;
        }
        $status = $this->gateway->send($msg, $mobile);
        $this->em->getRepository('SettingToolBundle:SmsSender')->insertLoginSms($user, $mobile, $status);

    }
}