<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Setting\Bundle\ToolBundle\Entity\SmsBulk;
use Symfony\Component\EventDispatcher\Event;


class SmsBulkEvent extends Event
{

    /** @var \Setting\Bundle\ToolBundle\Entity\SmsBulk */

    protected $smsBulk;


    public function __construct(SmsBulk $smsBulk)
    {
        $this->smsBulk = $smsBulk;
    }

    /**
     * @return SmsBulk
     */
    public function getSmsBulk()
    {
        return $this->smsBulk;
    }
}