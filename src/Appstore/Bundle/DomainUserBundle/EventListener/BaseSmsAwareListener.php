<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 4/9/15
 * Time: 5:05 PM
 */

namespace  Appstore\Bundle\DomainUserBundle\EventListener;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;
use Symfony\Component\EventDispatcher\Event;

abstract class BaseSmsAwareListener {

    /**
     * @var \Setting\Bundle\ToolBundle\Service\SmsGateWay
     */
    protected $gateway;

    public function  __construct(SmsGateWay $gateway)
    {
        $this->gateway = $gateway;
    }

}