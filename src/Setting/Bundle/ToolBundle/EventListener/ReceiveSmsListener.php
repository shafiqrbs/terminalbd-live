<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Setting\Bundle\ToolBundle\Event\ReceiveSmsEvent;
use Setting\Bundle\ToolBundle\Event\SmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class ReceiveSmsListener extends BaseSmsAwareListener
{

    /**
     * @var EntityManager
     */
    protected  $em;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    protected $gateway;

    public function  __construct(Registry $doctrine, SmsGateWay $gateway)
    {
        $this->doctrine = $doctrine;
        $this->gateway = $gateway;
        $this->em = $doctrine->getManager();
    }



    public function receiveSms(ReceiveSmsEvent $event)
    {

        /**
         * @var ReceiveSmsEvent $event
         */

        $post               = $event->getCustomerInbox();
        $globalOption       = $event->getGlobalOption();

        $mobile     = "88".$globalOption->getMobile();
        $msg        = $post->getContent();

        if(!empty($globalOption->getSmsSenderTotal()) and $globalOption->getSmsSenderTotal()->getRemaining() > 0 and $globalOption->getNotificationConfig()->getSmsActive() == 1 and $globalOption->getNotificationConfig()->getOnlineOrder() == 1) {

            $status = $this->gateway->send($msg, $mobile);
            $this->em->getRepository('SettingToolBundle:SmsSender')->insertCustomerSenderSms($mobile,$msg,$status);

        }




    }
}