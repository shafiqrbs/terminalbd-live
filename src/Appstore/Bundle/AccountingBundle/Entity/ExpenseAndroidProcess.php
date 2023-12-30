<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\AndroidDeviceSetup;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * MedicineAndroidProcess
 *
 * @ORM\Table(name="expense_android_process")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\ExpenseAndroidProcessRepository")
 */
class ExpenseAndroidProcess
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption")
     */
    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\AndroidDeviceSetup", inversedBy="expenseAndroidProcess")
     **/
    private  $androidDevice;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="androidProcess" )
     **/
    private  $expendituries;



    /**
     * @var string
     *
     * @ORM\Column(name="jsonItem", type="text",  nullable=true)
     */
    private $jsonItem;


     /**
     * @var string
     *
     * @ORM\Column(name="jsonSubItem", type="text",  nullable=true)
     */
    private $jsonSubItem;


    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length = 30, nullable=true)
     */
    private $process = 'expense';

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="itemCount", type="integer", nullable=true)
     */
    private $itemCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="subItemCount", type="integer", nullable=true)
     */
    private $subItemCount = 0;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="ipAddress", type="string", length = 100, nullable=true)
     */
    private $ipAddress;



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
     * @return AndroidDeviceSetup
     */
    public function getAndroidDevice()
    {
        return $this->androidDevice;
    }

    /**
     * @param AndroidDeviceSetup $androidDevice
     */
    public function setAndroidDevice($androidDevice)
    {
        $this->androidDevice = $androidDevice;
    }

    /**
     * @return string
     */
    public function getJsonItem()
    {
        return $this->jsonItem;
    }

    /**
     * @param string $jsonItem
     */
    public function setJsonItem($jsonItem)
    {
        $this->jsonItem = $jsonItem;
    }

    /**
     * @return string
     */
    public function getJsonSubItem()
    {
        return $this->jsonSubItem;
    }

    /**
     * @param string $jsonSubItem
     */
    public function setJsonSubItem($jsonSubItem)
    {
        $this->jsonSubItem = $jsonSubItem;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess($process)
    {
        $this->process = $process;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return int
     */
    public function getItemCount()
    {
        return $this->itemCount;
    }

    /**
     * @param int $itemCount
     */
    public function setItemCount($itemCount)
    {
        $this->itemCount = $itemCount;
    }

    /**
     * @return int
     */
    public function getSubItemCount()
    {
        return $this->subItemCount;
    }

    /**
     * @param int $subItemCount
     */
    public function setSubItemCount($subItemCount)
    {
        $this->subItemCount = $subItemCount;
    }

    /**
     * @return Expenditure
     */
    public function getExpendituries()
    {
        return $this->expendituries;
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
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }


}

