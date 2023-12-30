<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Setting\Bundle\ToolBundle\Event\CustomerOutstandingSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class CustomerOutstandingSmsListener extends BaseSmsAwareListener
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

    public function sendSms(CustomerOutstandingSmsEvent $event)
    {
        /**
         * @var CustomerOutstandingSmsEvent $event
         */

        $invoice = $event->getCustomer();
	    $orgName = $invoice->getGlobalOption()->getName();
	    $hotline = $invoice->getGlobalOption()->getHotline();
	    echo $mobile = "+88".$invoice->getMobile();

	    $mobile = "+8801828148148"; //.$invoice->getCustomer()->getMobile();
        $date = date('d-m-Y');
        $balance = $this->getDoctrine()->getRepository(AccountSales::class)->customerSingleOutstanding( $invoice->getGlobalOption(),$invoice->getId());
        $outstanding = number_format($balance,2);
        $msg = "Hello, {$invoice}! We’d like to remind you that a payment for {$outstanding} will be due by {$date}. For more information, {$hotline}";
	    $msg = $orgName .'\nDear '.$msg;
	    if (!empty($mobile)) {
            $status = $this->gateway->send($msg,$mobile);
          //  $this->em->getRepository('SettingToolBundle:SmsSender')->insertHotelInvoiceSenderSms($invoice, $status);
	    }


    }

}