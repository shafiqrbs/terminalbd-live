<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Core\UserBundle\Entity\User;
use Setting\Bundle\ContentBundle\Entity\ContactMessage;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\EventDispatcher\Event;


class DomainNotification extends Event
{

    /** @var \Setting\Bundle\ToolBundle\Entity\GlobalOption  */

    protected $globalOption;

    public function __construct(GlobalOption $globalOption)
    {
        $this->globalOption = $globalOption;
    }


    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }


}