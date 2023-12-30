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
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\SmsSender;
use Setting\Bundle\ToolBundle\Event\HmsInvoiceSmsEvent;
use Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class HmsInvoiceSmsListener extends BaseSmsAwareListener
{
    /**
     * @var EntityManager
     */
    protected  $em;
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    protected $gateway;

    public function  __construct(Registry $doctrine, SmsGateWay $gateway)
    {
        $this->doctrine = $doctrine;
        $this->gateway = $gateway;
        $this->em = $doctrine->getManager();
    }

    public function sendSms(HmsInvoiceSmsEvent $event)
    {
        /**
         * @var HmsInvoiceSmsEvent $event
         */

        $sales = $event->getSales();
        if(!empty($sales->getCustomer())) {
            if ($sales->getCustomer()->getLocation()->getParent()->getName() == 'Dhaka') {
                $customer = "Dear Customer your order is processing and you will get your product within 1 working days. Thanks " . $sales->getInventoryConfig()->getGlobalOption()->getName();
            } else {
                $customer = "Dear Customer your order is processing and you will get your product within 3 working days. Thanks " . $sales->getInventoryConfig()->getGlobalOption()->getName();
            }
            if ($sales->getCustomer()->getLocation()) {
                $location = "and Location " . $sales->getCustomer()->getLocation()->getName() . "," . $sales->getCustomer()->getLocation()->getParent()->getName();
            } else {
                $location = '';
            }
        }
        $administrator = "You get new order, Invoice no ".$sales->getInvoice() ." , Amount ".$sales->getTotal().$location;

        $customerMobile = "+88".$sales->getCustomer()->getMobile();
        $administratorMobile = "+88".$sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getMobile();

        if(!empty($sales->getInventoryConfig()->getGlobalOption()->getSmsSenderTotal()) and $sales->getInventoryConfig()->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1){

            if(!empty($customerMobile)){
                $status = $this->gateway->send($customer , $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertSalesSenderSms($sales,$status);
            }
            if(!empty($sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $mobile = $sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getMobile();
                $status = $this->gateway->send($administrator, $administratorMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertAdminSalesSenderSms($sales,$mobile,$status);
            }
        }

    }

    public function sendSalesConfirmSms(HmsInvoiceSmsEvent $event)
    {
        /**
         * @var HmsInvoiceSmsEvent $event
         */

        $sales = $event->getSales();
        $administrator = "You get invoice no ".$sales->getInvoice()." is ".$sales->getProcess();

        $administratorMobile = "+88".$sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getPaymentNotification();

        if(!empty($sales->getInventoryConfig()->getGlobalOption()->getSmsSenderTotal()) and $sales->getInventoryConfig()->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1){
            if((!empty($sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getMobile()) and $sales->getProcess() =='Paid') or (!empty($administratorMobile) and $sales->getProcess() =='Returned')) {
                $mobile = $sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getPaymentNotification();
                $status = $this->gateway->send($administrator, $administratorMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertAdminSalesConfirmSms($sales,$mobile,$status);
            }
        }

    }

    public function sendSalesCourierSms(HmsInvoiceSmsEvent $event)
    {
        /**
         * @var HmsInvoiceSmsEvent $event
         */

        $sales = $event->getSales();
        $customer = "Dear Customer your invoice ID.".$sales->getInvoice().' and Courier Invoice.'.$sales->getCourierInvoice().". Thanks ".$sales->getInventoryConfig()->getGlobalOption()->getName();
        $customerMobile = "+88".$sales->getCustomer()->getMobile();
        if(!empty($sales->getInventoryConfig()->getGlobalOption()->getSmsSenderTotal()) and $sales->getInventoryConfig()->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1){
            if(!empty($sales->getCustomer() and $sales->getCustomer()->getMobile())){
                $status = $this->gateway->send($customer , $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertSalesCourierSms($sales,$status);
            }
        }
    }


}