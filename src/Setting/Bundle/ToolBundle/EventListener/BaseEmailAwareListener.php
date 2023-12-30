<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 4/9/15
 * Time: 5:05 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;


use Setting\Bundle\ToolBundle\Service\EasyMailer;
use Symfony\Component\EventDispatcher\Event;

abstract class BaseEmailAwareListener {

    /**
     * @var \Setting\Bundle\ToolBundle\Service\EasyMailer
     */
    protected $easymailer;

    public function  __construct(EasyMailer $easymailer)
    {
        $this->easymailer = $easymailer;
    }

}