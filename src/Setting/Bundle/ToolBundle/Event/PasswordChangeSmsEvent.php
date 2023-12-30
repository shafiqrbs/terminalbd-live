<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Core\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;


class PasswordChangeSmsEvent extends Event
{

    /** @var \Core\UserBundle\Entity\User  */

    protected $user;

    protected $password;

    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;

    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }


}