<?php

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Event\OtpSmsEvent;
use Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class OtpSmsListener extends BaseSmsAwareListener
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

    public function sendSms(OtpSmsEvent $event)
    {

        /**
         * @var OtpSmsEvent $event
         */

        $option = $event->getConfig();
        $user = $event->getMobile();
        $password = $event->getPassword();
        $msg = "{$option->getDomain()}, Your One-Time PIN is {$password}. Please call for any support {$option->getHotline()}.";
        $mobile = "88".$user;
        return $status = $this->gateway->send($msg, $mobile);

    }
}