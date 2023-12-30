<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Setting\Bundle\ToolBundle\Entity\SmsSender;
use Setting\Bundle\ToolBundle\Event\SmsEvent;


class SmsListener extends BaseSmsAwareListener
{

    public function sendSms(SmsEvent $event)
    {

        /**
         * @var SmsEvent $event
         */

        $post = $event->getContactMessage();

        $msg = "Your request confirmation code is ";
        //$mobile = "88".$user->getMobile();
        $mobile = "8801828148148";
        $status = $this->gateway->send($msg, $mobile);
        $this->insertSms($status);



    }

    public function insertSms($status)
    {
        $entity = new SmsSender();
    }
}