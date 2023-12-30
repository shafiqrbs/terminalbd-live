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
use Symfony\Component\EventDispatcher\Event;


class UserSignup extends Event
{

    /** @var \Core\UserBundle\Entity\User  */

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return ContactMessage
     */
    public function getUser()
    {
        return $this->user;
    }


}