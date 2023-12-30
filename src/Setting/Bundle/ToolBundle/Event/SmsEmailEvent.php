<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Setting\Bundle\ContentBundle\Entity\ContactMessage;
use Symfony\Component\EventDispatcher\Event;


class SmsEmailEvent extends Event
{

    /** @var \Setting\Bundle\ContentBundle\Entity\ContactMessage  */

    protected $contactMessage;


    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;

    }


    /**
     * @return ContactMessage
     */
    public function getContactMessage()
    {
        return $this->contactMessage;
    }


}