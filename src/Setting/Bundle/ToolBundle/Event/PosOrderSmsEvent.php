<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Symfony\Component\EventDispatcher\Event;


class PosOrderSmsEvent extends Event
{

    /** @var \Appstore\Bundle\InventoryBundle\Entity\Sales  */

    protected $sales;


    public function __construct(Sales $sales)
    {
        $this->sales = $sales;
    }

    /**
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
    }
}