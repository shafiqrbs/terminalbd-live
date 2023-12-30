<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * CustomerConfig
 *
 * @ORM\Table("customer_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\CustomerConfigRepository")
 */
class CustomerConfig
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var boolean
     *
     * @ORM\Column(name="emailNotification", type="boolean")
     */
    private $emailNotification = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="smsNotification", type="boolean")
     */
    private $smsNotification = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isAgent", type="boolean")
     */
    private $isAgent = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="multipleShop", type="boolean")
     */
    private $multipleShop = false;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function getEmailNotification()
    {
        return $this->emailNotification;
    }

    /**
     * @param boolean $emailNotification
     */
    public function setEmailNotification($emailNotification)
    {
        $this->emailNotification = $emailNotification;
    }

    /**
     * @return boolean
     */
    public function getSmsNotification()
    {
        return $this->smsNotification;
    }

    /**
     * @param boolean $smsNotification
     */
    public function setSmsNotification($smsNotification)
    {
        $this->smsNotification = $smsNotification;
    }

    /**
     * @return boolean
     */
    public function getIsAgent()
    {
        return $this->isAgent;
    }

    /**
     * @param boolean $isAgent
     */
    public function setIsAgent($isAgent)
    {
        $this->isAgent = $isAgent;
    }

    /**
     * @return boolean
     */
    public function getMultipleShop()
    {
        return $this->multipleShop;
    }

    /**
     * @param boolean $multipleShop
     */
    public function setMultipleShop($multipleShop)
    {
        $this->multipleShop = $multipleShop;
    }


}

