<?php

namespace Setting\Bundle\ToolBundle\EventListener;
    use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
    use Doctrine\Bundle\DoctrineBundle\Registry;
    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\EntityRepository;
    use Setting\Bundle\ToolBundle\Event\AssociationInvoiceSmsEvent;
    use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class AssociationInvoiceListener extends BaseSmsAwareListener
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

    public function sendSms(AssociationInvoiceSmsEvent $event)
    {

        /**
         * @var BusinessInvoice $event
         */

        $post = $event->getInvoice();
        $msg = "Dear Member, Your contribution payment has been posted, Invoice no -".$post->getInvoice();
        $customer = $post->getCustomer();
        if(($customer->getCountry() and $customer->getCountry()->getCountryCode() == "BD")){
            $mobile = "+88".$post->getCustomer()->getMobile();
        }else{
            $mobile = $post->getCustomer()->getMobile();
        }
        $status = $this->gateway->send($msg, $mobile);
        $this->em->getRepository('SettingToolBundle:SmsSender')->insertInvoiceSms($post->getCustomer(), $msg, $status);


    }
}