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
use Setting\Bundle\ToolBundle\Event\HotelInvoiceSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class HotelInvoiceSmsListener extends BaseSmsAwareListener
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    protected $gateway;

    public function __construct(Registry $doctrine, SmsGateWay $gateway)
    {
        $this->doctrine = $doctrine;
        $this->gateway = $gateway;
        $this->em = $doctrine->getManager();
    }

    public function sendSms(HotelInvoiceSmsEvent $event)
    {
        /**
         * @var HotelInvoiceSmsEvent $event
         */

        $invoice = $event->getHotelInvoice();
	    $config = $invoice->getHotelConfig();
	    $orgName = $config->getGlobalOption()->getName();
	    $mobile = "+88".$invoice->getCustomer()->getMobile();
	    $process = $invoice->getProcess();
	    if($process == 'Booking'){
		    $msg = $config->getInvoiceBookingsms();
	    }elseif($process == 'Check-in'){
		    $msg = $config->getInvoiceCheckinsms();
	    }elseif ($process == 'Check-out'){
		    $msg = $config->getInvoiceCheckoutsms();
	    }

	  //  $mobile = "+8801828148148"; //.$invoice->getCustomer()->getMobile();
	    $msg = $orgName .'\nDear '.$msg;
	    if (!empty($mobile)) {
            $status = $this->gateway->send($msg,$mobile);
          //  $this->em->getRepository('SettingToolBundle:SmsSender')->insertHotelInvoiceSenderSms($invoice, $status);
	    }


    }

}