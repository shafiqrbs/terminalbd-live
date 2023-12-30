<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\SmsBulk;
use Setting\Bundle\ToolBundle\Event\SmsBulkEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class SmsBulkListener extends BaseSmsAwareListener
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

    public function sendSms(SmsBulkEvent $event)
    {
        /**
         * @var SmsBulk $event
         */

        $bulk = $event->getSmsBulk();
        $globalOption = $bulk->getGlobalOption();

        $msg = $bulk->getSmsText();


            if($bulk->getBulkMobile()){

                $bulks = explode(",",$bulk->getBulkMobile());
                foreach ($bulks as $mobile){
                    $mobile = "+88" . $mobile;
                    $status = $this->gateway->send($msg, $mobile);
                    $this->em->getRepository('SettingToolBundle:SmsSender')->insertSmsBulk($globalOption,$mobile, $status);
                }
            }

            if($bulk->getSourceTo() == "Customer" or $bulk->getSourceTo() == "Member") {

                /* @var $customer Customer */

                foreach ($globalOption->getCustomers() as $customer) {

                    if ($customer->getMobile()) {
                        $mobile = "+88" . $customer->getMobile();
                        $status = $this->gateway->send($msg, $mobile);
                        $this->em->getRepository('SettingToolBundle:SmsSender')->insertSmsBulk($globalOption, $mobile, $status);
                    }
                }
            }


    }

}