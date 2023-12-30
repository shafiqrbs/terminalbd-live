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


class CustomerSignup extends Event
{

    /** @var User  */

    protected $user;

    /** @var GlobalOption  */

    protected $globalOption;

    public function __construct(User $user, GlobalOption $globalOption)
    {
        $this->user = $user;
        $this->globalOption = $globalOption;
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }


}