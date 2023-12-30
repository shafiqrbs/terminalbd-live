<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\UserSignup;
use Setting\Bundle\ToolBundle\Service\EasyMailer;


class UserSignupListener extends BaseSmsAwareListener
{
    /** @var EasyMailer */
    private $mailer;

    public function setMailer(EasyMailer $mailer)
    {
        $this->mailer = $mailer;
    }


    public function onUserSignup(UserSignup $event)
    {
        $this->sendSms($event);
    }

    private function sendSms($event)
    {

        /**
         * @var UserSignup $event
         */
        $post = $event->getUser();
        $msg = "Your account has been created, Login user your mobile no and  password *148148#";
        $mobile = "88".$post->getProfile()->getMobile();
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