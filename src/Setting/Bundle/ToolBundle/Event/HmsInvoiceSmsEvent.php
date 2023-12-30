<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Symfony\Component\EventDispatcher\Event;


class HmsInvoiceSmsEvent extends Event
{

    /** @var \Appstore\Bundle\HospitalBundle\Entity\Invoice  */

    protected $sales;


    public function __construct(Invoice $sales)
    {
        $this->sales = $sales;
    }

    /**
     * @return Invoice
     */
    public function getSales()
    {
        return $this->sales;
    }
}