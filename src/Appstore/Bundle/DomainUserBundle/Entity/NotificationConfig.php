<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\InventoryBundle\Entity\BranchInvoice;
use Appstore\Bundle\InventoryBundle\Entity\Delivery;
use Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\LocationBundle\Entity\Location;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * NotificationConfig
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\NotificationConfigRepository")
 */
class NotificationConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="notificationConfig" )
     **/
    private $globalOption;


    /**
     * @var boolean
     *
     * @ORM\Column(name="onlineOrder", type="boolean")
     */
    private $onlineOrder = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="customerBaseOrder", type="boolean")
     */
    private $customerBaseOrder = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="smsActive", type="boolean")
     */
    private $smsActive = true;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="text", nullable = true)
     */
     private $mobile;

   /**
     * @var string
     *
     * @ORM\Column(name="paymentNotification", type="text", nullable = true)
     */
     private $paymentNotification;

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
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param GlobalOption $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return boolean
     */
    public function getOnlineOrder()
    {
        return $this->onlineOrder;
    }

    /**
     * @param boolean $onlineOrder
     */
    public function setOnlineOrder($onlineOrder)
    {
        $this->onlineOrder = $onlineOrder;
    }

    /**
     * @return boolean
     */
    public function isCustomerBaseOrder()
    {
        return $this->customerBaseOrder;
    }

    /**
     * @param boolean $customerBaseOrder
     */
    public function setCustomerBaseOrder($customerBaseOrder)
    {
        $this->customerBaseOrder = $customerBaseOrder;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return boolean
     */
    public function getSmsActive()
    {
        return $this->smsActive;
    }

    /**
     * @param boolean $smsActive
     */
    public function setSmsActive($smsActive)
    {
        $this->smsActive = $smsActive;
    }

    /**
     * @return string
     */
    public function getPaymentNotification()
    {
        return $this->paymentNotification;
    }

    /**
     * @param string $paymentNotification
     */
    public function setPaymentNotification($paymentNotification)
    {
        $this->paymentNotification = $paymentNotification;
    }


}

