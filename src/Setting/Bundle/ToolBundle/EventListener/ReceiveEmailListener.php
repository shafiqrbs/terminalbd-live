<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\ReceiveEmailEvent;
use Setting\Bundle\ToolBundle\Service\EasyMailer;


class ReceiveEmailListener extends BaseSmsAwareListener
{
    /** @var EasyMailer */

    private $mailer;

    public function  __construct(EasyMailer $mailer)
    {
        $this->mailer = $mailer;
    }


    public function receiveEmail(ReceiveEmailEvent $event)
    {

        /**
         * @var ReceiveEmailEvent $event
         */

        $post       = $event->getCustomerInbox();
        $domain     = $event->getGlobalOption();

        $to         = $domain->getEmail();
        $from       = $post->getCustomer()->getEmail();
        $subject    = 'Customer Email';
        $body       = $post->getContent();
        $this->mailer->send($to, $from, $subject, $body);

    }


}