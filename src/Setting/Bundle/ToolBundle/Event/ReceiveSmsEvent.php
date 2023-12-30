<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Appstore\Bundle\DomainUserBundle\Entity\CustomerInbox;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\EventDispatcher\Event;


class ReceiveSmsEvent extends Event
{


    /** @var GlobalOption  */

    protected $globalOption;

    /** @var  CustomerInbox */

    protected $customerInbox;



    public function __construct(GlobalOption $globalOption, CustomerInbox $customerInbox)
    {
        $this->globalOption     = $globalOption;
        $this->customerInbox    = $customerInbox;

    }


    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @return CustomerInbox
     */
    public function getCustomerInbox()
    {
        return $this->customerInbox;
    }


}