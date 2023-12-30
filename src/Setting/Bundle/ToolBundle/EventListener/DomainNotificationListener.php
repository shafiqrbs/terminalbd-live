<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\DomainNotification;
use Setting\Bundle\ToolBundle\Service\EasyMailer;


class DomainNotificationListener extends BaseSmsAwareListener
{
    /** @var EasyMailer */
    private $mailer;

    public function setMailer(EasyMailer $mailer)
    {
        $this->mailer = $mailer;
    }


    public function domainNotify(DomainNotification $event)
    {
        $this->sendSms($event);
        //$this->sendEmail($event);
    }

    private function sendSms($event)
    {

        /**
         * @var DomainNotification $event
         */
        if($event->getGlobalOption()->getStatus() == 2){
            $msg = "Your Account has been Hold, Please Contact Active Your Account,Thank You Terminalbd.com";
        }else{
            $msg = "Your Account has been Suspended, Please Contact Active Your Account,Thank You Terminalbd.com";
        }

        $mobile = "88".$event->getGlobalOption()->getMobile();
        //$mobile = "8801828148148";
        $this->gateway->send($msg, $mobile);

    }

    private function sendEmail($event)
    {

        /**
         * @var DomainNotification $event
         */

        $to         = 'shafiq@rightbrainsolution.com';
        $from       = 'shafiq@emicrograph.com';
        $subject    = 'Signup';
        $body       = 'Success';
        $this->mailer->send($to, $from, $subject, $body);

    }


}