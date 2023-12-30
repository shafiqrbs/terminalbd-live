<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Core\UserBundle\Entity\User;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\EventDispatcher\Event;


class OtpSmsEvent extends Event
{
    /* @var $config GlobalOption */
    protected $config;

    protected $mobile;

    protected $password;

    public function __construct($config , $mobile, $password)
    {
        $this->config = $config;
        $this->mobile = $mobile;
        $this->password = $password;

    }

    /* @return  GlobalOption  */

    public function getConfig()
    {
        return $this->config;
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function getPassword()
    {
        return $this->password;
    }


}