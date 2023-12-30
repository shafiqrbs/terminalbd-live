<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\SmsEmailEvent;
use Setting\Bundle\ToolBundle\Service\EasyMailer;


class SmsEmailListener extends BaseSmsAwareListener
{
    /** @var EasyMailer */
    private $mailer;

    public function setMailer(EasyMailer $mailer)
    {
        $this->mailer = $mailer;
    }


    public function onSmsEmail(SmsEmailEvent $event)
    {
        $this->sendSms($event);
        $this->sendEmail($event);
    }

    private function sendSms($event)
    {

        /**
         * @var SmsEmailEvent $event
         */

        $post = $event->getContactMessage();

        $msg = "Your request confirmation code is ";
        //$mobile = "88".$user->getMobile();
        $mobile = "8801828148148";
        $this->gateway->send($msg, $mobile);

    }

    private function sendEmail($event)
    {

        /**
         * @var SmsEmailEvent $event
         */

        $post = $event->getContactMessage();

        $to         = '';
        $from       = '';
        $subject    = '';
        $body       = '';
        $this->mailer->send($to, $from, $subject, $body);

    }


}