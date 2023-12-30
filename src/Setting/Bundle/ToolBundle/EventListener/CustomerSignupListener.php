<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\CustomerApiSignup;
use Setting\Bundle\ToolBundle\Event\CustomerSignup;
use Setting\Bundle\ToolBundle\Event\UserSignup;
use Setting\Bundle\ToolBundle\Service\EasyMailer;


class CustomerSignupListener extends BaseSmsAwareListener
{
    /** @var EasyMailer */
    private $mailer;

    public function setMailer(EasyMailer $mailer)
    {
        $this->mailer = $mailer;
    }


    public function onCustomerSignup(CustomerSignup $event)
    {
        $this->sendSms($event);
    }


    private function sendSms($event)
    {

        /**
         * @var CustomerSignup $event
         */

        $post = $event->getUser();
        $option = $event->getGlobalOption()->getDomain();
        $mobile = "88".$post->getUsername();
        $msg = "Your account has been created, User name:{$post->getUsername()}. Thank you. Be with www.$option";
        $this->gateway->send($msg, $mobile);

    }


    private function sendEmail($event)
    {

        /**
         * @var UserSignup $event
         */

        $post = $event->getUser();

        $to         = 'shafiq@rightbrainsolution.com';
        $from       = 'shafiq@emicrograph.com';
        $subject    = 'Signup';
        $body       = 'Success';
        $this->mailer->send($to, $from, $subject, $body);

    }


}