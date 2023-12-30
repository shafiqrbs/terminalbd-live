<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\PasswordChangeDomainSmsEvent;


class PasswordChangeDomainSmsListener extends BaseSmsAwareListener
{

    public function sendSms(PasswordChangeDomainSmsEvent $event)
    {

        /**
         * @var PasswordChangeDomainSmsEvent $event
         */

        $option = $event->getOption();
        $username = $event->getUsername();
        $password = $event->getPassword();

        $msg = "Requesting new password is: ".$password.' and user name: '.$username;
        //$mobile = "88".$user->getMobile();
        $mobile = "+88".$option->getMobile();
        $this->gateway->send($msg, $mobile);

    }
}