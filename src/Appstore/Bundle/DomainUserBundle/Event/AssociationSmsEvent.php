<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Appstore\Bundle\DomainUserBundle\Event;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;


class AssociationSmsEvent extends Event
{


    protected $member;

    public $msg;


    public function __construct(Customer $member , $msg)
    {
        $this->member = $member;
        $this->msg = $msg;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->member;
    }

    public function getMemberMsg()
    {
        return $this->msg;
    }


}